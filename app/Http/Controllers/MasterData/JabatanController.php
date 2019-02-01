<?php

namespace App\Http\Controllers\MasterData;

use App\Models\MasterData\Eselon;
use App\Models\MasterData\Golongan;
use App\Models\MasterData\Jabatan;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class JabatanController extends MasterDataController
{
    public function index(Request $request){
        /*$this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $this->query = $request->has('q') ? $request->input('q') : $this->query;
        $jabatan = new Jabatan();
        if ($this->query){
            $jabatan = $jabatan->where('jabatan','like','%'.$this->query.'%')
                ->orWhere('id_golongan','like','%'.$this->query.'%')
                ->orWhere('id_atasan','like','%'.$this->query.'%');
        }
        $jabatan = $jabatan->paginate($this->show_limit);*/
        return view('layouts.admin.jabatan.index');
    }

    public function show($id){
        $jabatan = Jabatan::with('atasan','golongan')->where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        return view('layouts.admin.jabatan.detail',compact('jabatan'));
    }

    public function add(){
        $data_option = new \stdClass();
        $data_option->golongan = Golongan::get();
        $data_option->jabatan = Jabatan::get();
        return view('layouts.admin.jabatan.add',compact('data_option'));
    }

    public function edit($id){
        $jabatan = Jabatan::with('atasan','golongan')->where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        $data_option = new \stdClass();
        $data_option->golongan = Golongan::get();
        $data_option->jabatan = Jabatan::get();
        return view('layouts.admin.jabatan.edit',compact('jabatan','data_option'));
    }

    public function store(Request $request,$json = true){
        $this->validate($request,[
            'jabatan' => 'required',
            'id_golongan' => 'required|in:'.$this->getListGolongan(),
//            'id_atasan' => 'in:'.$this->getListJabatan(),
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();

        $agama = Jabatan::create($input);
        if ($json)
        return response()->json($agama->toArray());
        return $agama;
    }

    public function update(Request $request,$id,$json = true){
        $jabatan = Jabatan::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        $this->validate($request,[
            'jabatan' => 'required',
            'id_golongan' => 'required|in:'.$this->getListGolongan(),
//            'id_atasan' => 'in:'.$this->getListJabatan().'|not_in:'.$jabatan->id,
        ]);
        $input = $request->input();
        $jabatan->update($input);
        if ($json)
        return response()->json($jabatan->toArray());
        return $jabatan;
    }

    public function delete($id,$json=true){
        $jabatan = Jabatan::whereId($id)->orWhere('uuid',$id)->firstOrFail();
        try {
            $jabatan->delete();
        } catch (QueryException $exception){
            if ($json)
                return response()->json([
                    'status' => '500',
                    'message' => 'Tidak dapat menghapus Jabatan, Jabatan memiliki pegawai aktif',
                    'error_message' => $exception->getMessage()
                ]);
            return [
                'status' => '500',
                'message' => 'Tidak dapat menghapus Jabatan, Jabatan memiliki pegawai aktif',
                'error_message' => $exception->getMessage()
            ];
        } catch (\Exception $exception){
            if ($json)
                return response()->json([
                    'status' => '500',
                    'message' => $exception->getMessage()
                ]);
            return [
                'status' => '500',
                'message' => $exception->getMessage()
            ];
        }
        if ($json)
            return response()->json([
                'status' => '200',
                'message' => 'data berhasil dihapus'
            ]);

        return [
            'status' => '200',
            'message' => 'data berhasil dihapus'
        ];
    }

    private function getListEselon(){
        return implode(',',Eselon::select('id')->pluck('id')->all());
    }

    private function getListGolongan(){
        return implode(',',Golongan::select('id')->pluck('id')->all());
    }

    private function getListJabatan(){
        return implode(',',Jabatan::select('id')->pluck('id')->all());
    }
}
