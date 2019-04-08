<?php

namespace App\Http\Controllers\Pegawai;


use App\Http\Controllers\Controller;

class SkpController extends Controller
{
    public function index()
    {
        return view('layouts.users.tunjangan-kinerja.index');
    }
}
