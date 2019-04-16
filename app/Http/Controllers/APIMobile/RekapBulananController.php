<?php

namespace App\Http\Controllers\APIMobile;

use App\Models\Absen\Checkinout;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use App\Repositories\PegawaiRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RekapBulananController extends ApiController
{

    protected $rekap;
    public function __construct(PegawaiRepository $rekap)
    {
        $this->rekap = $rekap;
    }

    public function getBawahan(Request $request)
    {
        $skpd = $request->has('skpd') ? $request->input('skpd') : null;
        $nip = Auth::user()->nip;
        $page = $request->input('page');
        $search = $request->has('search') ? $request->input('search') : '';
        return apiResponse($this->rekap->getBawahan($nip,$skpd,$search,$page,$this->show_limit_mobile)->map(function ($val){
            return [
                'uuid' => $val->uuid,
                'nama' => $val->nama,
                'foto' => $val->foto,
                'nip' => $val->nip,
                'created_at' => $val->created_at,
            ];
        }));
    }

    public function getRekap($nip, $bulan = null, $tahun = null)
    {
        $nip_user = auth('api')->user()->nip;
        $data = $this->rekap->getRekap($nip_user,$nip,$bulan,$tahun);
        return apiResponse($data);
    }

    public function getDetailRekap($nip, $tgl)
    {

        return apiResponse($this->rekap->getDetailRekap($nip,$tgl,true));
    }
}
