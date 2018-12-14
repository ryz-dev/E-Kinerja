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
    public function rekapBulanan(){
        // special user
        $special_user = ['Bupati','Wakil Bupati','Sekretaris Daerah'];

        $user = Auth::user();
        $skpd = in_array($user->role()->first()->nama_role,$special_user)?Skpd::all():Skpd::where('id',$user->id_skpd);
        $skpd = $skpd->pluck('nama_skpd','id');

        if (in_array($user->role()->first()->nama_role,$special_user)) {
            $skpd->prepend('-- ALL --');
        }

        $skpd = $skpd->toArray();

        return view('layouts.users.rekap-bulanan.index',compact('skpd'));
    }

    public function downloadRekapBulanan(Request $request){
        $periode_rekap = $request->input('periode_rekap')?$request->input('periode_rekap'):date('Y-m-d');
        $bulan = (int)($periode_rekap ? date('m', strtotime($periode_rekap)) : date('m'));
        $tahun = (int)($periode_rekap ? date('Y', strtotime($periode_rekap)) : date('Y'));
        $user = Auth::user();

        $formula = FormulaVariable::all();
        $persen['etika'] = $formula->where('variable','etika')->first()->persentase_nilai;
        $persen['kinerja'] = $formula->where('variable','kinerja')->first()->persentase_nilai;
        $persen['absen'] = $formula->where('variable','absen')->first()->persentase_nilai;

        $hari_kerja = HariKerja::whereHas('statusHari',function ($query){
            $query->where('status_hari','kerja');
        })->where('bulan',$bulan)->where('tahun',$tahun)->orderBy('tanggal','asc')->get();

        $jumlah_hari = $hari_kerja->count();

        $jumlah_kinerja = $jumlah_etika = $absen = 0;

        $pegawai = Pegawai::whereHas('jabatan', function($query) use($user){
            $query->where('id_atasan','=',$user->id_jabatan);
        });

        $data = [];


        foreach($pegawai->get() AS $key => $value){
            
            $data[$key]['data_pribadi'] = $value->toArray();
            $data[$key]['etika'] = $this->getEtika($value, $bulan, $tahun);

            if ($hari_kerja->count() > 0) {
                $nip = $value->nip;
                $jumlah_tunjangan = $value->jabatan->golongan->tunjangan;
                foreach ($hari_kerja AS $hk) {
                    $knj = Kinerja::where('nip', $nip)->where('tgl_mulai', '<=', $hk->tanggal)->where('tgl_selesai', '>=', $hk->tanggal)->terbaru();
                    $abs = Checkinout::where('nip', $nip)->whereDate('checktime', $hk->tanggal)->orderBy('checktype','asc')->get();
                    $status = 'alpa';
                    if ($abs->count() > 0) {
                        $in = false;
                        $out = false;
                        $masuk = $pulang = null;
                        foreach ($abs AS $a) {
                            if ($a->checktype == '0') {
                                $in = true;
                                $masuk = $a->checktime;
                            }
                            if ($a->checktype == '1') {
                                $out = true;
                                $pulang = $a->checktime;
                            }
                        }
                        if (strtotime($masuk) <= strtotime($hk->tanggal." 09:00:00") ){
                            if ((strtotime($pulang)-(strtotime($masuk))) >= (8.5 * 3600)){
                                $absen++;
                                $status = 'hadir';
                            }
                        }
                    }
                    
                    if ($knj->where('approve', 2)->first()) {
                        $jumlah_kinerja++;
                    }
                    if ($knj->where('jenis_kinerja', '<>', 'hadir')->where('approve', 2)->first()) {
                        $absen++;
                    }
                }
            }
            $data[$key]['kinerja'] = $jumlah_kinerja;
            $data[$key]['absen'] = $absen;
            $data[$key]['persentase'] = [
                'absen' => ($absen / $jumlah_hari) * 100,
                'kinerja' => ($jumlah_kinerja / $jumlah_hari) * 100,
                'etika' => $data[$key]['etika']
            ];
            $data[$key]['persentase_total'] = [
                'absen' => $data[$key]['persentase']['absen'] * $persen['absen'] / 100,
                'kinerja' => $data[$key]['persentase']['kinerja'] * $persen['kinerja'] / 100,
                'etika' => $data[$key]['persentase']['etika'] * $persen['etika'] / 100
            ];
            $data[$key]['total_tunjangan'] = (array_sum($data[$key]['persentase_total']) * $jumlah_tunjangan)/100;

        }
        
        $skpd = Skpd::where('id',$user->id_skpd)->first()->nama_skpd;
        $periode = ucfirst(\App\Models\MasterData\Bulan::find((int)date('m', strtotime($periode_rekap)))->nama_bulan.' '.date('Y', strtotime($periode_rekap)));
        $tanggal_cetak = date('d').' '.ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan).' '.date('Y');
        $pdf = \PDF::loadView('pdf.rekap-bulanan', compact('data','skpd', 'periode','tanggal_cetak'));
        $pdf->setPaper('letter','landscape');

        return $pdf->stream('rekap_bulanan.pdf');
    }

    private function getEtika($pegawai = null, $bulan = null, $tahun = null){
        $persentase = $pegawai->etika()->where('tanggal','like', $tahun."-".$bulan."%")->first();
        return $persentase?$persentase->persentase:0;
    }


    private function toDecimal($number){
        return $number?number_format((float)$number,2,',','.'):0;
    }


}
