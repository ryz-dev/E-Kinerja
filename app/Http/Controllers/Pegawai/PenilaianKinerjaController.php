<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Pegawai;
use App\Repositories\KinerjaRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenilaianKinerjaController extends Controller
{
    public function penilaianKinerja()
    {
        return view('layouts.users.penilaian-kinerja.index');
    }

    public function getBawahan(Request $r)
    {
        $nip = auth('web')->user()->nip;
        $date = $r->has('date') ? $r->date : null;
        return apiResponse(KinerjaRepository::getBawahanPenilaianKinerja($nip, $date));
    }

    public function getKinerja($nip, Request $r)
    {
        $date = $r->has('date') ? $r->date : null;
        return apiResponse(KinerjaRepository::getKinerjaPenilaianKinerja($nip, $date));
    }

    public function replyKinerja(Request $r)
    {
        $r->validate([
            'id' => 'required',
            'nip' => ['numeric', 'required', Rule::in(Pegawai::pluck('nip')->toArray())],
            'type' => ['numeric', 'required', Rule::in([1, 2])],
            'keterangan_approve' => ['required'],
            'nilai_kinerja' => 'required'
        ]);
        try {
            return apiResponse(KinerjaRepository::replyKinerjaPenilaianKinerja($r->input()));
        } catch (Exception $e) {
            return $this->ApiSpecResponses($e);
        }
    }
}
