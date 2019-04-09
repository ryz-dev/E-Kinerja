<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Skpd;
use App\Repositories\PegawaiRepository;
use Auth;
use DB;
use Illuminate\Http\Request;

class MonitoringAbsenController extends Controller
{
    public function index(Request $request)
    {
        // special user
        $special_user = [2, 3, 4];

        $user = Auth::user();
        $role = $user->role()->first()->id;
        $skpd = in_array($role, $special_user) ? Skpd::all() : Skpd::where('id', $user->id_skpd);
        $skpd = $skpd->pluck('nama_skpd', 'id');

        if ($role == 2) {
            $skpd = collect([-1 => 'SEKERTARIS DAERAH'] + $skpd->all());
        }

        if (in_array($role, $special_user)) {
            $skpd = collect([0 => ' ALL '] + $skpd->all());
        }

        $skpd = $skpd->toArray();

        return view('layouts.users.monitoringabsen.index', compact('skpd'));
    }

    public function dataAbsensi(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $page = $request->has('page') ? $request->input('page') : 1;
        $skpd = $request->input('skpd');
        $date = $request->input('d');
        $search = $request->has('search') ? $request->input('search') : '';
        $nip = auth('web')->user()->nip;
        $pegawai = new PegawaiRepository();
        try {
            $data = $pegawai->dataAbsensi($nip, $skpd, $date, $search, $this->show_limit, $page);
            return $this->ApiSpecResponses($data);
        } catch (\Exception $exception){
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function getPage(Request $request)
    {
        $skpd = $request->input('skpd');
        $search = $request->has('search') ? $request->input('search') : '';
        $nip = auth('web')->user()->nip;
        $count = (new PegawaiRepository())->getPageMonitoringAbsen($nip,$skpd,$search);
        $data = ceil($count / $this->show_limit);
        return response()->json(['page' => $data]);
    }
}
