<?php

namespace App\Http\Controllers\API;

use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Kinerja;
use App\Models\Absen\Etika;
use App\Models\Absen\Checkinout;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenilaianKinerjaController extends ApiController
{
    public function getBawahan(){
      $user = auth('web')->user();
      $pegawai = Pegawai::wherehas('jabatan', function($query) use ($user){
        $query->where('id_atasan','=',$user->id_jabatan)
      })->with(['kinerja' => function($query){
        $query->whereDate('tgl_mulai','=',date('Y-m-d'));
      }])->get();

      return $this->ApiSpecResponses($pegawai);
    }

    public function getKinerja($nip){
        $pegawai = Pegawai::where('nip',$nip)->first();
        $kinerja = Kinerja::where('userid',$pegawai->userid)
        ->whereDate('tgl_mulai',date('Y-m-d'))
        ->first();
       return $this->ApiSpecResponses($kinerja);
    }

    public function replyKinerja(Request $r) {
      $r->validate([
        'userid' => ['numeric','required',Rule::in(Pegawai::pluck('userid')->toArray())],
        'type' => ['numeric','required',Rule::in([1,0])],
        'keterangan_approve' => ['required']
      ]);
      try {
        $kinerja = Kinerja::find($r->id);
        $kinerja->keterangan_approve = $r->keterangan_approve;
        $kinerja->approve = $r->type;
        $kinerja->save();
        return $this->ApiSpecResponses(['status'=>'HTTP_OK']);
      } catch (Exception $e) {
        return $this->ApiSpecResponses($e);
      }
    }
}
