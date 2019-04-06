<?php

namespace App\Http\Controllers\Admin;

use App\Models\MasterData\Agama;
use App\Models\MasterData\Bulan;
use App\Models\MasterData\Hari;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StaticDataController extends AdminController
{
    /*agama*/
    public function getAgama(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $this->query = $request->has('q') ? $request->input('q') : $this->query;
        $agama = new Agama();
        if ($this->query){
            $agama = $agama->where('agama','like','%'.$this->query.'%');
        }
        $agama = $agama->paginate($this->show_limit);
        return response()->json($agama->toArray());
    }

    public function showAgama($id){
        $agama = Agama::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        return response()->json($agama->toArray());
    }

    public function storeAgama(Request $request){
        $this->validate($request,[
            'agama' => 'required'
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        $agama = Agama::create($input);
        return response()->json($agama->toArray());
    }

    public function updateAgama(Request $request,$id){
        $this->validate($request,[
            'agama' => 'required'
        ]);
        $agama = Agama::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        $agama->update($request->input());
        return response()->json($agama->toArray());
    }

    public function deleteAgama($id){
        $agama = Agama::whereId($id)->orWhere('uuid',$id)->firstOrFail();
        try {
            $agama->delete();
        } catch (\Exception $exception){}
        return response()->json([
            'message' => 'berhasil menghapus data'
        ]);
    }

    /*bulan*/
    public function getBulan(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $this->query = $request->has('q') ? $request->input('q') : $this->query;
        $bulan = new Bulan();
        if ($this->query){
            $bulan = $bulan->where('nama_bulan','like','%'.$this->query.'%')
                ->orWhere('kode','like','%'.$this->query.'%');
        }
        $bulan = $bulan->paginate($this->show_limit);
        return response()->json($bulan->toArray());
    }

    public function showBulan($id){
        $bulan = Bulan::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        return response()->json($bulan->toArray());
    }

    public function storeBulan(Request $request){
        $this->validate($request,[
            'nama_bulan' => 'required',
            'kode' => 'required'
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        $bulan = Bulan::create($input);
        return response()->json($bulan->toArray());
    }

    public function updateBulan(Request $request,$id){
        $this->validate($request,[
            'nama_bulan' => 'required',
            'kode' => 'required'
        ]);
        $bulan = Bulan::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        $bulan->update($request->input());
        return response()->json($bulan->toArray());
    }

    public function deleteBulan($id){
        $bulan = Bulan::whereId($id)->orWhere('uuid',$id)->firstOrFail();
        try {
            $bulan->delete();
        } catch (\Exception $exception){}
        return response()->json([
            'message' => 'berhasil menghapus data'
        ]);
    }
    /*hari*/
    public function getHari(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $this->query = $request->has('q') ? $request->input('q') : $this->query;
        $hari = new Hari();
        if ($this->query){
            $hari = $hari->where('nama_hari','like','%'.$this->query.'%');
        }
        $hari = $hari->paginate($this->show_limit);
        return response()->json($hari->toArray());
    }

    public function showHari($id){
        $hari = Hari::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        return response()->json($hari->toArray());
    }

    public function storeHari(Request $request){
        $this->validate($request,[
            'nama_hari' => 'required',
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        $hari = Hari::create($input);
        return response()->json($hari->toArray());
    }

    public function updateHari(Request $request,$id){
        $this->validate($request,[
            'nama_hari' => 'required',
        ]);
        $hari = Hari::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        $hari->update($request->input());
        return response()->json($hari->toArray());
    }

    public function deleteHari($id){
        $hari = Hari::whereId($id)->orWhere('uuid',$id)->firstOrFail();
        try {
            $hari->delete();
        } catch (\Exception $exception){}
        return response()->json([
            'message' => 'berhasil menghapus data'
        ]);
    }
}
