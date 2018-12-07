<?php

namespace App\Http\Controllers\MasterData;

use App\Models\MasterData\Golongan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GolonganController extends MasterDataController
{
    public function index(Request $request){
        /*$this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $this->query = $request->has('q') ? $request->input('q') : $this->query;
        $golongan = new Golongan();
        if ($this->query){
            $golongan = $golongan->where('golongan','like','%'.$this->query.'%')
                ->orWhere('tunjangan','like','%'.$this->query.'%');
        }
        $golongan = $golongan->paginate($this->show_limit);*/
        return view('layouts.admin.golongan.index');
    }

    public function show($id){
        $golongan = Golongan::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        return view('layouts.admin.golongan.detail',compact('golongan'));
    }

    public function add(){
        return view('layouts.admin.golongan.add');
    }

    public function edit($id){
        $golongan = Golongan::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        return view('layouts.admin.golongan.edit',compact('golongan'));
    }


    public function store(Request $request,$json = true){
        $this->validate($request,[
            'golongan' => 'required',
            'tunjangan' => 'required'
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        $agama = Golongan::create($input);
        if ($json)
        return response()->json($agama->toArray());
        return $agama;
    }

    public function update(Request $request,$id,$json = true){
        $this->validate($request,[
            'golongan' => 'required',
            'tunjangan' => 'required'
        ]);
        $golongan = Golongan::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        $golongan->update($request->input());
        if ($json)
        return response()->json($golongan->toArray());
        return $golongan;
    }

    public function delete($id,$json = true){
        $golongan = Golongan::whereId($id)->orWhere('uuid',$id)->firstOrFail();
        try {
            $golongan->delete();
        } catch (\Exception $exception){}
        if ($json)
        return response()->json([
            'message' => 'berhasil menghapus data'
        ]);
        return [
            'message' => 'berhasil menghapus data'
        ];
    }
}
