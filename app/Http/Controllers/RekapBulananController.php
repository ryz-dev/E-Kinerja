<?php

namespace App\Http\Controllers;

use App\Models\MasterData\Skpd;
use App\Models\Absen\Checkinout;
use App\Models\Absen\Etika;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\Bulan;
use App\Models\MasterData\FormulaVariable;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapBulananController extends Controller
{
    private $special_user_id = [2,3,4];
    

    public function rekapBulanan(){
        $special_user = ['Bupati', 'Wakil Bupati', 'Sekretaris Daerah'];
        $user = Auth::user();
        $skpd = in_array($user->role()->first()->nama_role,$special_user)?Skpd::all():Skpd::where('id',$user->id_skpd);
        $skpd = $skpd->pluck('nama_skpd','id');

        if (in_array($user->role()->first()->nama_role,$special_user)) {

            if ($user->role()->first()->nama_role == 'Bupati'){
                $skpd->prepend(strtoupper('Sekretaris Daerah'),'-1');
            }
        }

        $skpd = $skpd->toArray();

        return view('layouts.users.rekap-bulanan.index',compact('skpd'));
    }

    public function downloadRekapBulanan(Request $request){
        // dd($request);
        $periode_rekap = $request->input('periode_rekap')?$request->input('periode_rekap'):date('Y-m-d');
        $bulan = (int)($periode_rekap ? date('m', strtotime($periode_rekap)) : date('m'));
        $tahun = (int)($periode_rekap ? date('Y', strtotime($periode_rekap)) : date('Y'));
        $user = Auth::user();
        $hari_kerja = HariKerja::whereHas('statusHari',function ($query) use($bulan,$tahun){
            $query->where('status_hari','kerja');
        })->where('bulan',$bulan)->where('tahun',$tahun)->orderBy('tanggal','asc')->get();

        $formula = FormulaVariable::all();
        $persen['etika'] = $formula->where('variable','etika')->first()->persentase_nilai;
        $persen['kinerja'] = $formula->where('variable','kinerja')->first()->persentase_nilai;
        $persen['absen'] = $formula->where('variable','absen')->first()->persentase_nilai;

        $pegawai = $this->getDataPegawai($user,$bulan,$tahun,$request->input('d_id_skpd'));

        $data = $this->parseDataRekap($pegawai,$persen,$hari_kerja);
        
        $skpd = Skpd::where('id',$user->id_skpd)->first();
        $namaSkpd = $skpd?$skpd->nama_skpd:'PEMERINTAH KABUPATEN KOLAKA';
        $periode = ucfirst(\App\Models\MasterData\Bulan::find((int)date('m', strtotime($periode_rekap)))->nama_bulan.' '.date('Y', strtotime($periode_rekap)));
        $tanggal_cetak = date('d').' '.ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan).' '.date('Y');
        $pdf = \PDF::loadView('pdf.rekap-bulanan', compact('data','namaSkpd', 'periode','tanggal_cetak'));
        $pdf->setPaper('legal','landscape');

        return $pdf->download('rekap_bulanan.pdf');
    }

    private function toDecimal($number){
        return $number?number_format((float)$number,2,',','.'):0;
    }

    private function getDataPegawai($user,$bulan,$tahun,$id_skpd){
        $pegawai = Pegawai::where('nip','!=','');
        if ($id_skpd > 0) {
            $pegawai->where('id_skpd',$id_skpd);
        }

        if ($id_skpd < 0) {
            $pegawai->where('id_jabatan',3);
        }
        
        $pegawai = $pegawai->leftJoin('jabatan','pegawai.id_jabatan','=','jabatan.id');
        $pegawai = $pegawai->orderBy('jabatan.id_golongan');
        
        if (in_array($user->role()->pluck('id_role')->max(),$this->special_user_id) == false) {
            if ($user->role()->pluck('id_role')->max() != 5) {
                $pegawai->whereHas('jabatan', function($query) use ($user){
                    $query->where('id_atasan','=',$user->id_jabatan);
                });
            }
        }

        $pegawai = $pegawai->with(
                    [
                        'etika'=>function($query)use($bulan,$tahun){
                            $query->select('nip','persentase')->whereMonth('tanggal',$bulan)->whereYear('tanggal',$tahun);
                        },
                        'checkinout' => function($query)use($bulan,$tahun){
                            $query->select('nip','checktime','checktype')->whereMonth('checktime',$bulan)->whereYear('checktime',$tahun);
                        },
                        'kinerja' =>function($query)use($bulan,$tahun){
                            $query->select('nip','approve','jenis_kinerja','tgl_mulai','tgl_selesai')->whereMonth('tgl_mulai',$bulan)->whereYear('tgl_mulai',$tahun);
                        }
                    ]
                );
        return $pegawai;
    }

    private function parseDataRekap($pegawai,$persen,$hari_kerja){
        
        return $data = $pegawai->get()->map(function($item, $key) use($persen,$hari_kerja){
            // dd($item->jabatan());
            $tunjangan = $item->jabatan()->first()?$item->jabatan()->first()->golongan->tunjangan:0;
            $data['jabatan'] = $item->jabatan()->first()->jabatan;
            $data['kelas_jabatan'] = $item->jabatan()->first()->golongan->golongan;
            $data['data_pribadi'] = $item->toArray();
            $data['etika'] = $item->etika->first()?$item->etika->first()->persentase:0;
            $data['persentase_etika'] = ($data['etika'] * $persen['etika'])/100;
            $raw_kinerja = $this->parseKinerja($item,$key,$hari_kerja);
            $tambahan_absen = $raw_kinerja->sum('absen_tambahan');
            $raw_absen = $this->parseAbsen($item,$key,$hari_kerja);
            $data['kinerja'] = $raw_kinerja->sum('kinerja');
            $data['persentase_kinerja'] = ((($data['kinerja']/$hari_kerja->count()) * 100)*$persen['kinerja'])/100;
            $data['absen'] = $raw_absen->count()+$tambahan_absen;
            $data['persentase_absen'] = ((($data['absen'] / $hari_kerja->count()) * 100 ) *$persen['absen'])/100;
            $total = $this->calculateTotalTunjangan($data['persentase_absen'],$data['persentase_kinerja'],$data['persentase_etika'],$tunjangan );
            $data['total_tunjangan'] = $total['tunjangan'];
            $data['total_persentase'] = $total['persentase'];
            
            return $data;
        });    
    }

    private function parseAbsen($item,$key,$hari_kerja){
        return $item->checkinout->groupBy(function($itemcheckiout,$keycheckiout){
                    return date('Y-m-d',strtotime($itemcheckiout->checktime));
                })->map(function($itemabsen,$keyabsen) use ($key,$hari_kerja) {
                    if ($itemabsen->wherein('checktime',$hari_kerja->pluck('tanggal'))) {
                        $masuk = $itemabsen->where('checktype','0')?$itemabsen->where('checktype','0'):false;
                        $keluar = $itemabsen->where('checktype','1')?$itemabsen->where('checktype','1'):false;
                        $nip = $itemabsen->first()->nip;

                        if ($masuk->first() && $keluar->first() ) {
                            if (strtotime($masuk->first()->checktime) <= strtotime(date('Y-m-d',strtotime($masuk->first()->checktime))." 09:00:00") ) {
                                if ( (strtotime($keluar->first()->checktime) - strtotime($masuk->first()->checktime)) >= (8.5 * 3600) ){
                                    return 1;
                                }
                            }
                        }                        
                    }
                })->filter(function($value,$key){ return $value > 0 ; });
    }

    private function parseKinerja($item,$key,$hari_kerja){
        return $item->kinerja->map(function($itemkinerja, $keykinerja) use($key,$hari_kerja){
                    $kinerja = $absen_tambahan = 0;
                    if ($itemkinerja->wherein('tgl_mulai',$hari_kerja->pluck('tanggal')) && $itemkinerja->approve == 2 ) {
                        $kinerja = 1;                    
                    }
                    if ($itemkinerja->wherein('tgl_mulai',$hari_kerja->pluck('tanggal')) && $itemkinerja->approve == 2 && $itemkinerja->jenis_kinerja <> 'hadir') {
                        $absen_tambahan = 1;
                    }
                    return collect(['kinerja'=>$kinerja,'absen_tambahan'=>$absen_tambahan]);
                })->filter(function($value,$key){ return $value->filter(function($v,$k){ return $v > 0; }) ; });
    }

    private function calculateTotalTunjangan($absen,$kinerja,$etika,$tunjangan){
        $jumlah = ($absen+$kinerja+$etika);
        return [
            'persentase' => $jumlah,
            'tunjangan' => (floor($jumlah) * $tunjangan) /100 
        ];
    }


}
