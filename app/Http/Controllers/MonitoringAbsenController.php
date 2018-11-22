<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterData\Pegawai;
use App\Models\MasterData\Skpd;

class MonitoringAbsenController extends Controller
{
    public function index(Request $request){
        $user = \Auth::user();
        $skpd = $user->id_jabatan > 1? Skpd::where('id',$user->id_skpd) :Skpd::all();
        $skpd = $skpd->pluck('nama_skpd','id');
        if ($user->id_jabatan == 1) {
            $skpd->prepend('-- ALL --');
        }

        $skpd = $skpd->toArray();

        return view('layouts.users.monitoringabsen.index',compact('skpd'));
    }
}
