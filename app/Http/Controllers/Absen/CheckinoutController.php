<?php

namespace App\Http\Controllers\Absen;

use App\Models\Absen\Checkinout;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckinoutController extends Controller
{
    public function index(Request $request){
       return view('layouts.admin.checkinout.index');
    }

    public function show($id){
        dd('work show');
    }


    public function store(Request $request){
        dd('work store');
    }

    public function update(Request $request,$id){
       dd('work update');
    }

    public function delete($id){
        dd('work delete');
    }
}
