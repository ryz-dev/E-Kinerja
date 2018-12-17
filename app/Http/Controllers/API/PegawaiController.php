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
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;

class PegawaiController extends ApiController
{

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
        $periode_rekap = $request->input('periode_rekap')?$request->input('periode_rekap'):date('Y-m-d');
        $bulan = (int)($periode_rekap ? date('m', strtotime($periode_rekap)) : date('m'));
        $tahun = (int)($periode_rekap ? date('Y', strtotime($periode_rekap)) : date('Y'));

        $formula = FormulaVariable::all();
        $persen['etika'] = $formula->where('variable','etika')->first()->persentase_nilai;
        $persen['kinerja'] = $formula->where('variable','kinerja')->first()->persentase_nilai;
        $persen['absen'] = $formula->where('variable','absen')->first()->persentase_nilai;

        $hari_kerja = HariKerja::whereHas('statusHari',function ($query){
            $query->where('status_hari','kerja');
        })->where('bulan',$bulan)->where('tahun',$tahun)->orderBy('tanggal','asc')->get();

        $jumlah_hari = $hari_kerja->count();

        $jumlah_kinerja = $jumlah_etika = $absen = 0;

        $pegawai = Pegawai::with('jabatan')->where('id_skpd','=',$request->id_skpd);

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

        $skpd = Skpd::where('id',$request->id_skpd)->first()->nama_skpd;
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

}
