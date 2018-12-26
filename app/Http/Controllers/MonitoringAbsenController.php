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
        $role = $user->role()->first()->id;
        $skpd = in_array($role,$special_user)?Skpd::all():Skpd::where('id',$user->id_skpd);
        $skpd = $skpd->pluck('nama_skpd','id');

        if ($role == 2) {
            $skpd = collect([-1=>'SEKERTARIS DAERAH'] + $skpd->all());
        }
        
        if (in_array($role,$special_user)) {
            $skpd = collect([0=>' ALL '] + $skpd->all());
        }

        $skpd = $skpd->toArray();

        return view('layouts.users.monitoringabsen.index',compact('skpd'));
    }
}
