<?php

namespace App\Http\Controllers\MasterData;

use App\Models\MasterData\Eselon;
use App\Models\MasterData\Jabatan;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class JabatanController extends MasterDataController
{
    public function index(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $this->query = $request->has('q') ? $request->input('q') : $this->query;
        $jabatan = new Jabatan();
        if ($this->query){
            $jabatan = $jabatan->where('jabatan','like','%'.$this->query.'%')
                ->orWhere('id_eselon','like','%'.$this->query.'%')
                ->orWhere('id_atasan','like','%'.$this->query.'%');
        }
        $jabatan = $jabatan->paginate($this->show_limit);
        return response()->json($jabatan->toArray());
    }

    public function show($id){
        $jabatan = Jabatan::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        return response()->json($jabatan->toArray());
    }


    public function store(Request $request){
        $this->validate($request,[
            'jabatan' => 'required',
            'id_eselon' => 'required|in:'.$this->getListEselon(),
            'id_atasan' => 'required|in:'.$this->getListJabatan(),
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();

        $agama = Jabatan::create($input);
        return response()->json($agama->toArray());
    }

    public function update(Request $request,$id){
        $jabatan = Jabatan::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        $this->validate($request,[
            'jabatan' => 'required',
            'id_eselon' => 'required|in:'.$this->getListEselon(),
            'id_atasan' => 'required|in:'.$this->getListJabatan().'|not_in:'.$jabatan->id,
        ]);
        $input = $request->input();
        $jabatan->update($input);
        return response()->json($jabatan->toArray());
    }

    public function delete($id){
        $jabatan = Jabatan::whereId($id)->orWhere('uuid',$id)->firstOrFail();
        try {
            $jabatan->delete();
        } catch (\Exception $exception){}
        return response()->json([
            'message' => 'berhasil menghapus data'
        ]);
    }

    private function getListEselon(){
        return implode(',',Eselon::select('id')->pluck('id')->all());
    }

    private function getListJabatan(){
        return implode(',',Jabatan::select('id')->pluck('id')->all());
    }
}
