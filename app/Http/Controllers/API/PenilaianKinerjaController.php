<?php

namespace App\Http\Controllers\API;

use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Kinerja;
use App\Models\Absen\Etika;
use App\Models\Absen\Checkinout;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PenilaianKinerjaController extends ApiController
{
    public function getBawahan(){
        $user = Pegawai::whereIdJabatan(2)->first();
        $user->load('jabatan.pegawai_bawahan');
        $bawahan = $user->jabatan->pegawai_bawahan;
        return $this->ApiSpecResponses($bawahan);
    }

    public function getKinerja($nip){
        /* Data kinerja */
        $pegawai = Pegawai::where('nip',$nip)->first();
        $kinerja = Kinerja::where('userid',$pegawai->userid)
        ->whereDate('tgl_mulai','2018-10-20')
        ->first();
        // date('Y-m-d')
       return $this->ApiSpecResponses($kinerja);
    }

    public function replyKinerja(Request $r) {
      $r->validate([
        'userid' => ['numeric','required',Rule::in(Pegawai::pluck('userid')->toArray())],
        'type' => ['numeric','required',Rule::in([1,0])],
        'keterangan_approve' => ['required']
      ]);
      return $this->ApiSpecResponses($r);
      try {
        $kinerja = Kinerja::find($r->id);
        $kinerja->keterangan_approve = $r->keterangan_approve;
        $kinerja->approve = $r->approve;
        $kinerja->save();
        return $this->ApiSpecResponses(['status'=>'ok']);
      } catch (\Exception $e) {
        return $this->ApiSpecResponses(['status'=>'fail']);
      }
    }
}
