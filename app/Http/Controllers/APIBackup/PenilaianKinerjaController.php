<?php

namespace App\Http\Controllers\APIBackup;

use App\Models\Absen\Kinerja;
use App\Models\MasterData\Pegawai;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenilaianKinerjaController extends ApiController
{
    public function getBawahan(Request $r)
    {
        $user = auth('web')->user();
        if ($r->date != null) {
            $user = auth('web')->user();
            $pegawai = Pegawai::wherehas('jabatan', function ($query) use ($user) {
                $query->where('id_atasan', '=', $user->id_jabatan);
            })->with(['kinerja' => function ($query) use ($r) {
                $query->whereDate('tgl_mulai', '<=', $r->date);
                $query->whereDate('tgl_mulai', '>=', $r->date);
                $query->terbaru();
            }])->get();
        } else {
            $user = auth('web')->user();
            $pegawai = Pegawai::wherehas('jabatan', function ($query) use ($user) {
                $query->where('id_atasan', '=', $user->id_jabatan);
            })->with(['kinerja' => function ($query) {
                $query->whereDate('tgl_mulai', '<=', date('Y-m-d'));
                $query->whereDate('tgl_mulai', '>=', date('Y-m-d'));
                $query->terbaru();
            }])->get();
        }
        return $this->ApiSpecResponses($pegawai);
    }

    public function getKinerja($nip, Request $r)
    {
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
                ->terbaru()
                ->first();
        } else {
            $kinerja = Kinerja::where('nip', $pegawai->nip)
                ->whereDate('tgl_mulai', '<=', date('Y-m-d'))
                ->whereDate('tgl_mulai', '>=', date('Y-m-d'))
                ->terbaru()
                ->first();
        }
        return $this->ApiSpecResponses([
            'now' => $kinerja,
            'old' => $old_kinerja->pluck('tgl_mulai')->toArray()
        ]);
    }

    public function replyKinerja(Request $r)
    {
        $r->validate([
            'nip' => ['numeric', 'required', Rule::in(Pegawai::pluck('nip')->toArray())],
            'type' => ['numeric', 'required', Rule::in([1, 2])],
            'keterangan_approve' => ['required']
        ]);
        try {
            $kinerja = Kinerja::find($r->id);
            $kinerja->keterangan_approve = $r->keterangan_approve;
            $kinerja->approve = $r->type;
            $kinerja->save();
            return $this->ApiSpecResponses(['status' => 'HTTP_OK']);
        } catch (Exception $e) {
            return $this->ApiSpecResponses($e);
        }
    }
}
