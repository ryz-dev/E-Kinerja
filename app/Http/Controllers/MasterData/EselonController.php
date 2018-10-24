<?php

namespace App\Http\Controllers\MasterData;

use App\Models\MasterData\Eselon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EselonController extends MasterDataController
{
    public function index(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $this->query = $request->has('q') ? $request->input('q') : $this->query;
        $eselon = new Eselon();
        if ($this->query){
            $eselon = $eselon->where('eselon','like','%'.$this->query.'%')
                ->orWhere('tunjangan','like','%'.$this->query.'%');
        }
        $eselon = $eselon->paginate($this->show_limit);
        return response()->json($eselon->toArray());
    }

    public function show($id){
        $eselon = Eselon::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        return response()->json($eselon->toArray());
    }


    public function store(Request $request){
        $this->validate($request,[
            'eselon' => 'required',
            'tunjangan' => 'required'
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        $agama = Eselon::create($input);
        return response()->json($agama->toArray());
    }

    public function update(Request $request,$id){
        $this->validate($request,[
            'eselon' => 'required',
            'tunjangan' => 'required'
        ]);
        $eselon = Eselon::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        $eselon->update($request->input());
        return response()->json($eselon->toArray());
    }

    public function delete($id){
        $eselon = Eselon::whereId($id)->orWhere('uuid',$id)->firstOrFail();
        try {
            $eselon->delete();
        } catch (\Exception $exception){}
        return response()->json([
            'message' => 'berhasil menghapus data'
        ]);
    }
}
