<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InputKinerjaController extends Controller
{
    public function inputKinerja(Request $request){
        $user = Auth::user();
        $role = $user->role()->first()->nama_role;
        $permission = $user->role()->first()->permissions;
        if ($role == 'Bupati' || $role == 'Wakil Bupati'){
            if (!key_exists('input-kinerja',$permission))
            return redirect()->route('monitoring.absen.index');
        }
        return view('layouts.users.input-kinerja.index');
    }
}
