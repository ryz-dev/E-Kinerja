<?php

namespace App\Http\Controllers\Absen;

use App\Models\Absen\Checkinout;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckinoutRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckinoutController extends Controller
{
    public function index(Request $request){
        $all = Checkinout::paginate(10);
        return view('layouts.admin.checkinout.index', compact('all'));
    }

    public function show($id){
        dd('work show');
    }

    public function create(){
        return view('layouts.admin.checkinout.create');
    }

    public function store(CheckinoutRequests $request){
        // dd($request->all());
        $req = $request->all();
        $req['checktime'] = $request->checktime.":00";
        // dd($req);
        Checkinout::create($req);
        return view('layouts.admin.checkinout.index')->with('success', 'Sukses membuat data baru');
    }

    public function edit($id){
        $checkinout = Checkinout::find($id);
        return view('layouts.admin.checkinout.edit', compact('checkinout'));
    }

    public function update(CheckinoutRequests $request,$id){
       $checkinout = Checkinout::findOrFail($id);
       $req = $request->all();
       $req['checktime'] = strlen($request->checktime) === 19 ? $request->checktime : $request->checktime.":00";
       $checkinout->update($req);
       return view('layouts.admin.checkinout.index')->with('success', 'Data Berhasil di Perbarui');
    }

    public function destroy($id){
        $checkinout = Checkinout::findOrFail($id);
        if ($checkinout->delete()) {
           return response()->json(['status' => 'success']);
        }else{
           return response()->json(['status' => 'error']);
        }

    }
}
