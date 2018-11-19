<?php

namespace App\Http\Controllers\APIMobile;
use App\Http\Controllers\Controller;

use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Kinerja;
use App\Models\Absen\Etika;
use App\Models\Absen\Checkinout;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenilaianKinerjaController extends Controller
{
    public function getBawahan(){
        $pegawai = Pegawai::wherehas('jabatan', function($query){
            $query->where('id_atasan','=',2); /** TODO : Ganti dengan user yang login */
            })->with(['kinerja' => function($query){
            $query->whereDate('tgl_mulai','=',date('Y-m-d'));
        }])->get();

        $data = [];
        foreach($pegawai as $p){
            $data[] = [
                'nip' => $p->nip,
                'foto' => $p->foto,
                'nama' => $p->nama,
                'kinerja' => $p->kinerja,
            ];
        }
  
        return $this->ApiSpecResponses($data);
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
