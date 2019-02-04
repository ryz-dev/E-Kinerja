<?php

namespace App\Http\Controllers\MasterData;

use App\Models\MasterData\Golongan;
use Illuminate\Database\QueryException;
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
        $golongan = Golongan::where('uuid',$id)->firstOrFail();
        $golongan->update($request->input());
        if ($json)
        return response()->json($golongan->toArray());
        return $golongan;
    }

    public function delete($id,$json = true){
        $golongan = Golongan::where('uuid',$id)->firstOrFail();
        try {
            $golongan->delete();
        } catch (QueryException $exception){
            if ($json)
                return response()->json([
                    'status' => '500',
                    'message' => 'Tidak dapat menghapus Golongan Jabatan, Golongan Jabatan memiliki pegawai aktif'
                ]);
            return [
                'status' => '500',
                'message' => 'Tidak dapat menghapus Golongan Jabatan, Golongan Jabatan memiliki pegawai aktif'
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
}
