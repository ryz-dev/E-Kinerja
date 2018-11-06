<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RekapBulananController extends Controller
{
    public function rekapBulanan(){
        return view('layouts.users.rekap-bulanan.index');
    }


}
