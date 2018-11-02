<?php

namespace App\Http\Controllers\MasterData;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RolePegawaiController extends Controller
{
    public function index(){
        return view('layouts.admin.rolepegawai.index');
    }
}
