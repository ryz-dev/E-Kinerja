<?php

namespace App\Http\Controllers\MasterData;

use App\Models\MasterData\HariKerja;
use App\Models\MasterData\StatusHari;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class HariKerjaController extends MasterDataController
{
    public function index(Request $request){
      return view('layouts/admin/harikerja/index');
    }

    public function add(){
      $data['status'] = StatusHari::get();
      return view('layouts/admin/harikerja/add',$data);
    }

    public function store(Request $request){
      $this->validate($request,[
        'tanggal' => 'required',
        'id_status_hari' => 'required|in:'.$this->getStatusHari()
      ]);
      try {
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        $input['bulan'] = (int)date('m',strtotime($request->input('tanggal')));
        $input['tahun'] = date('Y',strtotime($request->input('tanggal')));
        $input['hari'] = date('N',strtotime($request->input('tanggal')));
        $hariKerja = HariKerja::create($input);
        return redirect( route('hari_kerja') );
      } catch (\Exception $e) {

        /* Jika tanggal yang di set sudah terimpan di database */
        return redirect()
          ->back()
          ->with('message', 'Tanggal sudah ada !');
      }
    }

    public function edit($id){
      $data['harikerja'] = HariKerja::where('id',$id)
      ->orWhere('uuid',$id)
      ->firstOrFail();
      $data['status'] = StatusHari::get();
      return view('layouts/admin/harikerja/edit',$data);
    }

    public function update(Request $request,$id){
      $this->validate($request,[
          'tanggal' => 'required',
          'id_status_hari' => 'required|in:'.$this->getStatusHari()
      ]);

      try {

        $hari_kerja = HariKerja::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        $input = $request->input();
        $input['bulan'] = (int)date('m',strtotime($request->input('tanggal')));
        $input['tahun'] = date('Y',strtotime($request->input('tanggal')));
        $input['hari'] = date('N',strtotime($request->input('tanggal')));
        $hari_kerja->update($input);
        return redirect( route('hari_kerja') );
        
      } catch (\Exception $e) {

        /* Jika tanggal yang di set sudah terimpan di database */
        return redirect()
          ->back()
          ->with('message', 'Tanggal sudah ada !');
      }
    }

    public function delete($id){
      $hari_kerja = HariKerja::whereId($id)->orWhere('uuid',$id)->firstOrFail();
      try {
          $hari_kerja->delete();
      } catch (\Exception $exception){}
      return response()->json([
          'message' => 'berhasil menghapus data'
      ]);
    }

    private function getStatusHari(){
      return implode(',',StatusHari::select('id')->pluck('id')->all());
    }

    public function show($id){
      $hari_kerja = HariKerja::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
      return response()->json($hari_kerja->toArray());
    }

}
