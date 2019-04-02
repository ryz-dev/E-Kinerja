<?php

namespace App\Http\Controllers;

use App\Models\Absen\Kinerja;
use App\Models\MasterData\Pegawai;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InputKinerjaController extends Controller
{
    public function inputKinerja(Request $request){
        $user = Auth::user();
        $role = $user->role()->first()->id;
        $permission = $user->role()->first()->permissions;

        if ($role == 2 || $role == 3){
            if ($permission['input-kinerja'] == false)
            return redirect()->route('monitoring.absen.index');
        }
        return view('layouts.users.input-kinerja.index');
    }
}
