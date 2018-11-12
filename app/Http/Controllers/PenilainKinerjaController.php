<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenilainKinerjaController extends Controller
{
    public function penilaianKinerja(){
        return view('layouts.users.penilaian-kinerja.index');
    }


}
