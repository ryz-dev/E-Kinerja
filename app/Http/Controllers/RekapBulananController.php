<?php

namespace App\Http\Controllers;

use App\Models\MasterData\Skpd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RekapBulananController extends Controller
{
    public function rekapBulanan(){
        // special user
        $special_user = ['Bupati','Wakil Bupati','Sekertaris Daerah'];

        $user = Auth::user();
        $skpd = in_array($user->role()->first()->nama_role,$special_user)?Skpd::all():Skpd::where('id',$user->id_skpd);
        $skpd = $skpd->pluck('nama_skpd','id');

        if (in_array($user->role()->first()->nama_role,$special_user)) {
            $skpd->prepend('-- ALL --');
        }

        $skpd = $skpd->toArray();

        return view('layouts.users.rekap-bulanan.index',compact('skpd'));
    }


}
