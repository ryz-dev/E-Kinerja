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
        $user = auth('api')->user();
        $pegawai = Pegawai::wherehas('jabatan', function($query) use ($user){
            $query->where('id_atasan','=', $user->id_jabatan);
            })->with(['kinerja' => function($query){
            $query->whereDate('tgl_mulai','=',date('Y-m-d'));
        }])->get();

        $data = [];
        foreach($pegawai as $p){
            $data[] = [
                'uuid' => $p->uuid,
                'nip' => $p->nip,
                'foto' => $p->foto,
                'nama' => $p->nama,
                'approve' => count($p->kinerja) ? $p->kinerja[0]->approve : 0,
            ];
        }

        $sort = array();
        foreach($data as $k=>$v) {
            $sort['approve'][$k] = $v['approve'];
        }
        array_multisort($sort['approve'], SORT_ASC, SORT_STRING, $data);
  
        return $this->ApiSpecResponses($data);
    }
  
    public function getKinerja($nip){
        $pegawai = Pegawai::where('nip',$nip)->first();
        $kinerja = Kinerja::where('userid',$pegawai->userid)
        ->select('id', 'userid','tgl_mulai', 'tgl_selesai', 'jenis_kinerja', 'rincian_kinerja', 'approve', 'keterangan_approve')
            ->whereDate('tgl_mulai',date('Y-m-d'))
            ->first();

        return $this->ApiSpecResponses($kinerja);
    }
  
    public function replyKinerja(Request $r) {
        $r->validate([
            'userid' => ['numeric','required',Rule::in(Pegawai::pluck('userid')->toArray())],
            'type' => ['numeric','required',Rule::in([1,2])],
            'keterangan_approve' => ['required']
        ]);
        try {
            $kinerja = Kinerja::find($r->id);
            $kinerja->keterangan_approve = $r->keterangan_approve;
            $kinerja->approve = $r->type;
            $kinerja->save();
            return $this->ApiSpecResponses(['status'=>'OK']);
        } catch (Exception $e) {
            return $this->ApiSpecResponses($e);
        }
    }
}
