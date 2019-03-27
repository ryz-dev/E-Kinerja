<?php

namespace App\Http\Controllers\API;

use App\Models\Absen\Checkinout;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\Agama;
use App\Models\MasterData\FormulaVariable;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Jabatan;
use App\Models\MasterData\Pegawai;
use App\Http\Controllers\MasterData\MasterDataController;
use App\Models\MasterData\Skpd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;

class PegawaiController extends ApiController
{
    private $special_user_id = [2,3,4];

    public function getSkpd(){
        $skpd = Skpd::pluck('nama_skpd','id')->all();
        return $this->ApiSpecResponses($skpd);
    }

    public function listPegawai(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        try {
            $pegawai = Pegawai::with('jabatan','agama','skpd')->orderBy('created_at', 'DESC');
            if ($request->has('q')) {
                $pegawai = $pegawai->where(function ($query)use($request){
                    $query->where('nip','like','%'.$request->input('q').'%');
                    $query->orWhere('nama','like','%'.$request->input('q').'%');
                });
            }
            if ($request->has('deleted')){
                $pegawai = $pegawai->withTrashed()->whereNotNull('deleted_at');
            }
            $pegawai = $pegawai->paginate($this->show_limit);
            return $this->ApiSpecResponses($pegawai);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function detailPegawai($id){
        try {
            $pegawai = Pegawai::with('jabatan','agama','skpd')->where('nip',$id)->orWhere('uuid',$id)->firstOrFail();
            return $this->ApiSpecResponses($pegawai);
        } catch (\Exception $exception){
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function storePegawai(Request $request){
        $pegawai = new \App\Http\Controllers\MasterData\PegawaiController();
        $data = $pegawai->store($request,false);
        return $this->ApiSpecResponses($data);
    }

    public function updatePegawai(Request $request,$id){
        $pegawai = new \App\Http\Controllers\MasterData\PegawaiController();
        $data = $pegawai->update($request,$id,false);
        return $this->ApiSpecResponses($data);
    }

    public function deletePegawai($id){
        $pegawai = new \App\Http\Controllers\MasterData\PegawaiController();
        $data = $pegawai->delete($id,false);
        return $this->ApiSpecResponses($data);
    }

    public function updatePassword(Request $request){
        $user = auth('web')->user();
        
        if ($request->input('oldPassword') === $request->input('newPassword')) {
            return response()->json([
                'message' => 'Kata sandi lama dan kata sandi baru tidak boleh sama!'
            ],500);
        }

        if (\Hash::check($request->input('oldPassword'), $user->password)){
            $pegawai = new \App\Http\Controllers\MasterData\PegawaiController();
            $data = $pegawai->updatePassword($user->nip,$request->input('newPassword'));
            return $this->ApiSpecResponses($data);
        }
        else{
            return response()->json([
                'message' => 'Kata sandi lama salah!'
            ],500);
        }
    }

    public function restorePegawai($nip){
        Pegawai::withTrashed()->whereNip($nip)->restore();
    }

    public function getPage(Request $request)
    {
        if ($request->has('deleted') && $request->has('q')) {
            $data = Pegawai::withTrashed()
                ->whereNotNull('deleted_at')
                ->where(function ($query)use($request){
                    $query->where('nip','like','%'.$request->input('q').'%');
                    $query->orWhere('nama','like','%'.$request->input('q').'%');
                })
                ->count();
        } else if ($request->has('q')){
            $data = Pegawai::where('nip','like','%'.$request->input('q').'%')
                ->orWhere('nama','like','%'.$request->input('q').'%')
                ->count();
        } else if ($request->has('deleted')){
            $data = Pegawai::withTrashed()->count();
        } else {
            $data = Pegawai::count();
        }
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
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
        $persen['kinerja'] = $formula->where('variable','kinerja')->first()->persentase_nilai;
        $persen['absen'] = $formula->where('variable','absen')->first()->persentase_nilai;
        $pegawai = $this->getDataPegawai($user,$bulan,$tahun,$request->input('id_skpd'));

        $data = $this->parseDataRekap($pegawai,$persen,$hari_kerja);
        $skpd = Skpd::find($request->id_skpd);
        $namaSkpd = $skpd?$skpd->nama_skpd:'PEMERINTAH KABUPATEN KOLAKA';
        $periode = ucfirst(\App\Models\MasterData\Bulan::find((int)date('m', strtotime($periode_rekap)))->nama_bulan.' '.date('Y', strtotime($periode_rekap)));
        $tanggal_cetak = date('d').' '.ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan).' '.date('Y');
        $pdf = \PDF::loadView('pdf.rekap-bulanan', compact('data','namaSkpd', 'periode','tanggal_cetak'));
        $pdf->setPaper('legal','landscape');

        return $pdf->stream('rekap_bulanan.pdf');
    }

    private function toDecimal($number){
        return $number?number_format((float)$number,2,',','.'):0;
    }

    private function getDataPegawai($user,$bulan,$tahun,$id_skpd){
        $pegawai = Pegawai::where('nip','!=','')->whereNotNull('id_jabatan');
        if ($id_skpd > 0) {
            $pegawai->where('id_skpd',$id_skpd);
        }

        if ($id_skpd < 0) {
            $pegawai->where('id_jabatan',3);
        }

        $pegawai = $pegawai->leftJoin('jabatan','pegawai.id_jabatan','=','jabatan.id');
        $pegawai = $pegawai->leftJoin('golongan','jabatan.id_golongan','=','golongan.id');
        $pegawai = $pegawai->orderBy('golongan.tunjangan','desc');
        $pegawai = $pegawai->orderBy('pegawai.nama');

        if (in_array($user->role()->pluck('id_role')->max(),$this->special_user_id) == false) {
            if ($user->role()->pluck('id_role')->max() != 5) {
                $pegawai->whereHas('jabatan', function($query) use ($user){
                    $query->where('id_atasan','=',$user->id_jabatan);
                });
            }
        }

        $pegawai = $pegawai->with(
            [
                
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
            $tunjangan = $item->jabatan()->first()->golongan->tunjangan;
            $data['jabatan'] = $item->jabatan()->first()->jabatan;
            $data['kelas_jabatan'] = $item->jabatan()->first()->golongan->golongan;
            $data['data_pribadi'] = $item->toArray();
            $raw_kinerja = $this->parseKinerja($item,$key,$hari_kerja);
            $tambahan_absen = $raw_kinerja->sum('absen_tambahan');
            $raw_absen = $this->parseAbsen($item,$key,$hari_kerja);
            $data['kinerja'] = $raw_kinerja->sum('kinerja');
            $data['persentase_kinerja'] = ((($data['kinerja']/$hari_kerja->count()) * 100)*$persen['kinerja'])/100;
            $data['absen'] = $raw_absen->count()+$tambahan_absen;
            $data['persentase_absen'] = ((($data['absen'] / $hari_kerja->count()) * 100 ) *$persen['absen'])/100;
            $total = $this->calculateTotalTunjangan($data['persentase_absen'],$data['persentase_kinerja'],$tunjangan );
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
                    if (strtotime($masuk->first()->checktime) <= strtotime(date('Y-m-d',strtotime($masuk->first()->checktime))." 08:00:00") ) {
                        if ( (strtotime($keluar->first()->checktime) - strtotime($masuk->first()->checktime)) >= (8 * 3600) ){
                            return 1;
                        } else {
                            return 0.2;
                        }
                    } else if (strtotime($masuk->first()->checktime) <= strtotime(date('Y-m-d',strtotime($masuk->first()->checktime))." 08:30:00") ) {
                        if ( (strtotime($keluar->first()->checktime) - strtotime($masuk->first()->checktime)) >= (8 * 3600) ){
                            return 0.8;
                        } else {
                            return 0.2;
                        }
                    } else if (strtotime($masuk->first()->checktime) <= strtotime(date('Y-m-d',strtotime($masuk->first()->checktime))." 09:00:00") ) {
                        if ( (strtotime($keluar->first()->checktime) - strtotime($masuk->first()->checktime)) >= (8 * 3600) ){
                            return 0.6;
                        } else {
                            return 0.2;
                        }
                    } else if (strtotime($masuk->first()->checktime) <= strtotime(date('Y-m-d',strtotime($masuk->first()->checktime))." 09:30:00") ) {
                        if ( (strtotime($keluar->first()->checktime) - strtotime($masuk->first()->checktime)) >= (8 * 3600) ){
                            return 0.4;
                        } else {
                            return 0.2;
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

    private function calculateTotalTunjangan($absen,$kinerja,$tunjangan){
        $jumlah = ($absen+$kinerja);
        return [
            'persentase' => $jumlah,
            'tunjangan' => (floor($jumlah) * $tunjangan) /100
        ];
    }

}
