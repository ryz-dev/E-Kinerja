<?php

namespace App\Http\Controllers\APIMobile;

use App\Models\Absen\Etika;
use App\Models\MasterData\Pegawai;
use App\Repositories\KinerjaRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenilaianKinerjaController extends ApiController
{

    public function getBawahan(Request $r)
    {
        $nip = auth('api')->user()->nip;
        $date = $r->has('date') ? $r->date : date('Y-m-d');
        $search = $r->has('search') ? $r->input('search') : '';
        $data = KinerjaRepository::getBawahanPenilaianKinerja($nip, $date, $search);
        return apiResponse($data->map(function ($val) {
            return ['uuid' => $val->uuid,
                'nip' => $val->nip,
                'foto' => $val->foto,
                'nama' => $val->nama,
                'approve' => count($val->kinerja) ? $val->kinerja[0]->approve : 0,
            ];
        })->sortBy('approve'));
    }

    public function getKinerja($nip, Request $r)
    {
        $date = $r->has('date') ? $r->date : date('Y-m-d');
        return apiResponse(KinerjaRepository::getKinerjaPenilaianKinerja($nip, $date));
    }

    public function replyKinerja(Request $r)
    {
        $r->validate([
            'id' => 'required',
            'nip' => ['numeric', 'required', Rule::in(Pegawai::pluck('nip')->toArray())],
            'type' => ['numeric', 'required', Rule::in([1, 2])],
            'keterangan_approve' => ['required'],
            'nilai_kinerja' => 'required|numeric'
        ]);
        try {
            return apiResponse(KinerjaRepository::replyKinerjaPenilaianKinerja($r->input()));
        } catch (Exception $e) {
            return apiResponse('', [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }
}
