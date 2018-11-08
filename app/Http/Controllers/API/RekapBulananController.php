<?php

namespace App\Http\Controllers\API;


use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Kinerja;
use App\Models\Absen\Etika;
use App\Models\Absen\Checkinout;

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
        foreach ($hari_kerja AS $key => $hk){
            $kinerja = $pegawai->kinerja()->where('tgl_mulai','>=',$hk->tanggal)->where('tgl_selesai','<=',$hk->tanggal)->first();
            $etika = $pegawai->etika()->where('tanggal',$hk->tanggal)->first();
            $data_inout[] = [
                'tgl_prev' => isset($hari_kerja[$key-1]->tanggal) ? $hari_kerja[$key-1]->tanggal : '',
                'tgl_next' => isset($hari_kerja[$key+1]->tanggal) ? $hari_kerja[$key+1]->tanggal : '',
                'tgl' => $hk->tanggal,
                'tanggal' => $this->formatDate($hk->tanggal),
                'hari' => ucfirst($hk->Hari->nama_hari),
                'checkinout' => $pegawai->checkinout()->where('checktime','like','%'.$hk->tanggal.'%')->get()->toArray(),
                'status' => ucfirst(str_replace('_',' ',isset($kinerja->jenis_kinerja)?$kinerja->jenis_kinerja:'')),
                'persentase' => isset($etika->persentase)?$etika->persentase : '',
                'approve' => isset($kinerja->approve) ? $kinerja->approve : ''
            ];
        }
        return $this->ApiSpecResponses([
            'tanggal_sekarang' => $this->formatDate(date('Y-m-d')),
            'rekap_bulanan' => $data_inout
        ]);
    }

    public function getDetailRekap($nip,$tgl) {
        $date = new HariKerja;

        /* Tarik tanggal sebelumnya */
        $date_prev = $date->whereDate('tanggal','<',$tgl)
        ->whereIdStatusHari(1)
        ->orderBy('tanggal','desc')
        ->first();

        /* Tarik tanggal setelahnya */
        $date_next = $date->whereDate('tanggal','>',$tgl)
        ->whereIdStatusHari(1)
        ->orderBy('tanggal','asc')
        ->first();

        /* Data kinerja */
        $pegawai = Pegawai::where('nip',$nip)->first();
        $kinerja = Kinerja::where('userid',$pegawai->userid)
        ->whereDate('tgl_mulai',$tgl)
        ->first();

        /* Data etika */
        $etika = Etika::where("userid",$pegawai->userid)
        ->where("tanggal",$tgl)
        ->first();

        /* Data checkinout */
        $checkinout = Checkinout::where("userid",$pegawai->userid)
        ->whereDate("checktime",$tgl)
        ->get();


        /* Data array */
        $result = [
          "kinerja"=>$kinerja,
          "etika"=>$etika,
          "checkinout"=>$checkinout
        ];

        return $this->ApiSpecResponses(array_merge($result,[
          'prev'=>isset($date_prev->tanggal)==false?'':$date_prev->tanggal,
          'next'=>isset($date_next->tanggal)==false?'':$date_next->tanggal
        ]));
    }
}
