<?php

namespace App\Http\Controllers\APIMobile;

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
    public function getBawahan(Request $r){
        $user = auth('api')->user();
        $search = $r->has('search')? $r->input('search'):'';
        if ($r->date != null) {
            $user = auth('api')->user();
            $pegawai = Pegawai::wherehas('jabatan', function ($query) use ($user) {
                $query->where('id_atasan', '=', $user->id_jabatan);
            })->with(['kinerja' => function ($query) use ($r) {
                $query->whereDate('tgl_mulai', '<=', $r->date);
                $query->whereDate('tgl_mulai', '>=', $r->date);
                $query->terbaru();
            }]);
        } else {
            $user = auth('api')->user();
            $pegawai = Pegawai::wherehas('jabatan', function ($query) use ($user) {
                $query->where('id_atasan', '=', $user->id_jabatan);
            })->with(['kinerja' => function ($query) {
                $query->whereDate('tgl_mulai', '<=', date('Y-m-d'));
                $query->whereDate('tgl_mulai', '>=', date('Y-m-d'));
                $query->terbaru();
            }]);
        }

        if ($search) {
            $pegawai->where(function($query) use ($search){
                $query->where('nip','like','%'.$search.'%')->orWhere('nama','like','%'.$search.'%');
            });
        }

        $pegawai = $pegawai->get();

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
  
    public function getKinerja($nip, Request $r){
        $pegawai = Pegawai::where('nip', $nip)->first();
        $old_kinerja = Kinerja::where('nip', $pegawai->nip)
            ->where('approve', 0)
            ->whereMonth('tgl_mulai', date('m'))
            ->whereDate('tgl_mulai', '<', date('Y-m-d'))
            ->get();
        if ($r->date != null) {
            $kinerja = Kinerja::where('nip', $pegawai->nip)
            ->whereDate('tgl_mulai', '<=', $r->date)
            ->whereDate('tgl_mulai', '>=', $r->date)
            ->select('id', 'nip', 'tgl_mulai', 'tgl_selesai', 'jenis_kinerja', 'rincian_kinerja', 'approve', 'keterangan_approve')
            ->terbaru()
            ->first();
        } else {
            $kinerja = Kinerja::where('nip', $pegawai->nip)
            ->whereDate('tgl_mulai', '<=', date('Y-m-d'))
            ->whereDate('tgl_mulai', '>=', date('Y-m-d'))
            ->select('id', 'nip', 'tgl_mulai', 'tgl_selesai', 'jenis_kinerja', 'rincian_kinerja', 'approve', 'keterangan_approve')
            ->terbaru()
            ->first();
        }
        return $this->ApiSpecResponses([
          'now'=>$kinerja,
          'old'=>$old_kinerja->pluck('tgl_mulai')->toArray()
        ]);
    }
  
    public function replyKinerja(Request $r) {
        $r->validate([
            'nip' => ['numeric','required',Rule::in(Pegawai::pluck('nip')->toArray())],
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
