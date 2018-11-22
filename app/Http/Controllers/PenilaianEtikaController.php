<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PenilaianEtikaController extends Controller
{
    public function index(){
        return view('layouts.users.penilaian-etika.index');
    }

    public function store(Request $request){
        $this->validate($request, [
            'nip' => 'required',
            'persentase' => 'required',
            'keterangan' => 'required'
        ]);
    }
}
