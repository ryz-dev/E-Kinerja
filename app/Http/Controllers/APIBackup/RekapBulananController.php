<?php

namespace App\Http\Controllers\APIBackup;


use App\Models\MasterData\Bulan;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Kinerja;
use App\Models\Absen\Checkinout;
use App\Repositories\PegawaiRepository;
use App\User;
use Illuminate\Http\Request;

class RekapBulananController extends ApiController
{
    private $special_user = ['Bupati', 'Wakil Bupati', 'Sekretaris Daerah'];
    private $special_user_id = [2,3,4];

    protected $rekap;
    public function __construct(PegawaiRepository $rekap)
    {
        $this->rekap = $rekap;
    }

    public function getBawahan(Request $request)
    {
        $user = auth('web')->user();
        $skpd = $request->has('skpd') ? $request->input('skpd') : null;
        $pegawai = $this->rekap->getBawahan($user->nip,$skpd);
        return $this->ApiSpecResponses($pegawai);
    }

    public function getRekap($nip, $bulan = null, $tahun = null)
    {
        $user = auth('web')->user();
        $nip_user = $user->nip;
        $bulan = (int)($bulan ?: date('m'));
        $tahun = ($tahun ?: date('Y'));
        $data = $this->rekap->getRekap($nip_user,$nip,$bulan,$tahun);
        return $this->ApiSpecResponses($data);
    }

    public function getDetailRekap($nip, $tgl)
    {
        return $this->ApiSpecResponses($this->rekap->getDetailRekap($nip,$tgl));
    }
}
