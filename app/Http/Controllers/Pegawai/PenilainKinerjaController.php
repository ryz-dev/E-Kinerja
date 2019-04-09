<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\Pegawai;
use App\Repositories\PegawaiRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenilainKinerjaController extends Controller
{
    public function penilaianKinerja()
    {
        return view('layouts.users.penilaian-kinerja.index');
    }

    public function getBawahan(Request $r)
    {
        $nip = auth('web')->user()->nip;
        $date = $r->has('date') ? $r->date : null;
        return $this->ApiSpecResponses(PegawaiRepository::getBawahanPenilaianKinerja($nip,$date));
    }

    public function getKinerja($nip, Request $r)
    {
        $date = $r->has('date') ? $r->date : null;
        return $this->ApiSpecResponses(PegawaiRepository::getKinerjaPenilaianKinerja($nip,$date));
    }

    public function replyKinerja(Request $r)
    {
        $r->validate([
            'id' => 'required',
            'nip' => ['numeric', 'required', Rule::in(Pegawai::pluck('nip')->toArray())],
            'type' => ['numeric', 'required', Rule::in([1, 2])],
            'keterangan_approve' => ['required']
        ]);
        try {
            return $this->ApiSpecResponses(PegawaiRepository::replyKinerjaPenilaianKinerja($r->input()));
        } catch (Exception $e) {
            return $this->ApiSpecResponses($e);
        }
    }
}
