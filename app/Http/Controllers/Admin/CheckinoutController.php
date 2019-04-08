<?php


namespace App\Http\Controllers\Admin;

use App\Http\Requests\CheckinoutRequests;
use App\Models\Absen\Checkinout;
use App\Models\MasterData\Pegawai;
use Illuminate\Http\Request;

class CheckinoutController extends AdminController
{
    public function index(Request $request)
    {
        $all = Checkinout::paginate(10);
        return view('layouts.admin.checkinout.index', compact('all'));
    }

    public function show($id)
    {
        dd('work show');
    }

    public function create()
    {
        $data = Pegawai::all()->pluck('nip');
        return view('layouts.admin.checkinout.create', compact('data'));
    }

    public function store(CheckinoutRequests $request)
    {
        // dd($request->all());
        $req = $request->all();
        $req['checktime'] = $request->checktime . ":00";
        // dd($req);
        Checkinout::create($req);
        return view('layouts.admin.checkinout.index')->with('success', 'Sukses membuat data baru');
    }

    public function edit($id)
    {
        $checkinout = Checkinout::find($id);
        $data = Pegawai::all()->pluck('nip');
        return view('layouts.admin.checkinout.edit', compact('checkinout', 'data'));
    }

    public function update(CheckinoutRequests $request, $id)
    {
        $checkinout = Checkinout::findOrFail($id);
        $req = $request->all();
        $req['checktime'] = strlen($request->checktime) === 19 ? $request->checktime : $request->checktime . ":00";
        $checkinout->update($req);
        return view('layouts.admin.checkinout.index')->with('success', 'Data Berhasil di Perbarui');
    }

    public function destroy($id)
    {
        $checkinout = Checkinout::findOrFail($id);
        $checkinout->delete();

        return view('layouts.admin.checkinout.index')->with('success', 'Data Berhasil di Di hapus');
    }
}
