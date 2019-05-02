<?php

namespace App\Http\Controllers\APIMobile;

use App\Models\Absen\Etika;
use App\Models\MasterData\Pegawai;
use App\Repositories\KinerjaRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PenilaianKinerjaController extends ApiController
{

    public function getBawahan(Request $r)
    {
        $nip = auth('api')->user()->nip;
        $date = $r->has('date') ? $r->date : date('Y-m-d');
        $search = $r->has('search') ? $r->input('search') : '';
        try {
            $data = KinerjaRepository::getBawahanPenilaianKinerja($nip, $date, $search);
        } catch (\Exception $exception) {
            return $this->error500($exception->getMessage());
        }
        $data = $data->map(function ($val) {
            return ['uuid' => $val->uuid,
                'nip' => $val->nip,
                'foto' => $val->foto,
                'nama' => $val->nama,
                'approve' => count($val->kinerja) ? $val->kinerja[0]->approve : 0,
                'nilai_kinerja' => count($val->kinerja) ? $val->kinerja[0]->nilai_kinerja : 0
            ];
        })->sortBy('approve')->values()->all();
        return apiResponse($data);
    }

    public function getKinerja($nip, Request $r)
    {
        $date = $r->has('date') ? $r->date : date('Y-m-d');
        return apiResponse(KinerjaRepository::getKinerjaPenilaianKinerja($nip, $date));
    }

    public function replyKinerja(Request $r)
    {
        $validate = Validator::make($r->input(), [
            'id' => 'required',
            'nip' => ['numeric', 'required', Rule::in(Pegawai::pluck('nip')->toArray())],
            'type' => ['numeric', 'required', Rule::in([1, 2])],
            'keterangan_approve' => ['required'],
            'nilai_kinerja' => 'required|numeric'
        ]);
        if ($validate->fails()) {
            return $this->error422($validate->errors());
        }
        try {
            return apiResponse(KinerjaRepository::replyKinerjaPenilaianKinerja($r->input()));
        } catch (Exception $e) {
            if ($e instanceof NotFoundHttpException) {
                return $this->error404('SKP Tidak Ditemukan');
            }
            return $this->error500($e->getMessage());
            /*return apiResponse('', [
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);*/
        }
    }
}
