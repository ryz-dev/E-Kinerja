<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterData\Pegawai;
use App\Models\MasterData\Skpd;

class MonitoringAbsenController extends Controller
{
    public function index(Request $request){
        // special user
        $special_user = [2,3,4];

        $user = \Auth::user();
        $skpd = in_array($user->role()->first()->nama_role,$special_user)?Skpd::all():Skpd::where('id',$user->id_skpd);
        $skpd = $skpd->pluck('nama_skpd','id');

        if (in_array($user->role()->first()->id,$special_user)) {
            $skpd->prepend('-- ALL --');
        }

        $skpd = $skpd->toArray();

        return view('layouts.users.monitoringabsen.index',compact('skpd'));
    }
}
