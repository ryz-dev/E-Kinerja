<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterData\Pegawai;
use App\Models\MasterData\Skpd;

class MonitoringAbsenController extends Controller
{
    public function index(Request $request){
        $skpd = Skpd::all();
        return view('layouts.users.monitoringabsen.index',compact('skpd'));
    }
}
