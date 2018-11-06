<?php

namespace App\Http\Controllers\API;


use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;

class RekapBulananController extends ApiController
{
    public function getBawahan(){
        $user = Pegawai::whereIdJabatan(2)->first();
        $user->load('jabatan.pegawai_bawahan');
        $bawahan = $user->jabatan->pegawai_bawahan;
        return $this->ApiSpecResponses($bawahan);
    }

    public function getRekap($nip,$bulan = null,$tahun = null){
        $bulan = (int)($bulan?:date('m'));
        $tahun = ($tahun?:date('Y'));
        $hari_kerja = HariKerja::where('bulan',$bulan)->where('tahun',$tahun)->whereHas('statusHari',function ($query){
            $query->where('status_hari','kerja');
        })->get();
        $pegawai = Pegawai::whereNip($nip)->first();
        $data_inout = [];
        foreach ($hari_kerja AS $hk){
            $kinerja = $pegawai->kinerja()->where('tgl_mulai','>=',$hk->tanggal)->where('tgl_selesai','<=',$hk->tanggal)->first();
            $etika = $pegawai->etika()->where('tanggal',$hk->tanggal)->first();
            $data_inout[] = [
                'tanggal' => $this->formatDate($hk->tanggal),
                'hari' => ucfirst($hk->Hari->nama_hari),
                'checkinout' => $pegawai->checkinout()->where('checktime','like','%'.$hk->tanggal.'%')->get()->toArray(),
                'status' => ucfirst(str_replace('_',' ',isset($kinerja->jenis_kinerja)?$kinerja->jenis_kinerja:'')),
                'persentase' => isset($etika->persentase)?$etika->persentase : 0,
                'approve' => isset($kinerja->approve) ? $kinerja->approve : ''
            ];
        }
        return $this->ApiSpecResponses([
            'tanggal_sekarang' => $this->formatDate(date('Y-m-d')),
            'rekap_bulanan' => $data_inout
        ]);
    }
}
