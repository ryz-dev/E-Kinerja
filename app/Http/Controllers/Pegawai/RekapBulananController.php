<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Bulan;
use App\Models\MasterData\FormulaVariable;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use App\Models\MasterData\Skpd;
use App\Repositories\PegawaiRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class RekapBulananController extends Controller
{
    protected $rekap;
    private $special_user_id = [2, 3, 4];

    public function __construct(PegawaiRepository $rekap)
    {
        $this->rekap = $rekap;
    }

    public function rekapBulanan()
    {
        $special_user = ['Bupati', 'Wakil Bupati', 'Sekretaris Daerah'];
        $user = Auth::user();
        $skpd = in_array($user->role()->first()->nama_role, $special_user) ? Skpd::all() : Skpd::where('id', $user->id_skpd);
        $skpd = $skpd->pluck('nama_skpd', 'id');

        if (in_array($user->role()->first()->nama_role, $special_user)) {

            if ($user->role()->first()->nama_role == 'Bupati') {
                $skpd->prepend(strtoupper('Sekretaris Daerah'), '-1');
            }
        }

        $skpd = $skpd->toArray();

        return view('layouts.users.rekap-bulanan.index', compact('skpd'));
    }

    public function downloadRekapBulanan(Request $request)
    {
        // dd($request);
        $d_id_skpd = $request->has('d_id_skpd') ? $request->input('d_id_skpd') : null;
        $periode_rekap = $request->input('periode_rekap') ? $request->input('periode_rekap') : date('Y-m-d');
        $user = Auth::user();
        $skpd = $user->id_skpd;
        return $this->rekap->downloadRekapBulanan($user->nip,$skpd,$d_id_skpd,$periode_rekap,true);
    }

    public function getBawahan(Request $request)
    {
        $user = auth('web')->user();
        $skpd = $request->has('skpd') ? $request->input('skpd') : null;
        $pegawai = $this->rekap->getBawahan($user->nip, $skpd);
        return $this->ApiSpecResponses($pegawai);
    }

    public function getRekap($nip, $bulan = null, $tahun = null)
    {
        $user = auth('web')->user();
        $nip_user = $user->nip;
        $bulan = (int)($bulan ?: date('m'));
        $tahun = ($tahun ?: date('Y'));
        $data = $this->rekap->getRekap($nip_user, $nip, $bulan, $tahun);
        return $this->ApiSpecResponses($data);
    }

    public function getDetailRekap($nip, $tgl)
    {
        return $this->ApiSpecResponses($this->rekap->getDetailRekap($nip, $tgl));
    }

    private function toDecimal($number)
    {
        return $number ? number_format((float)$number, 2, ',', '.') : 0;
    }
}
