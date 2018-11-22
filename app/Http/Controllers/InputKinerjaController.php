<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InputKinerjaController extends Controller
{
    public function inputKinerja(Request $request){
        return view('layouts.users.input-kinerja.index');
    }
}
