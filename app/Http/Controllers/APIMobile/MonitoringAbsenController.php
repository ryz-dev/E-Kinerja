<?php

namespace App\Http\Controllers\APIMobile;

use App\Http\Controllers\Controller;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use App\Repositories\PegawaiRepository;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;


class MonitoringAbsenController extends Controller
{
    private $jam_masuk = '09:00:59';

    public function dataAbsensi(Request $request)
    {
        $this->show_limit_mobile = $request->has('s') ? $request->input('s') : $this->show_limit_mobile;
        $pegawai = new PegawaiRepository();
        $nip = Auth::user()->nip;
        $skpd = $request->input('skpd');
        $raw_date = $request->input('d');
        $search = $request->has('search') ? $request->input('search') : '';
        $page = $request->input('page');
        $res = $pegawai->dataAbsensi($nip,$skpd,$raw_date,$search,$this->show_limit_mobile,$page,true);
        return apiResponse($res['data'],$res['diagnostic']);
    }
}
