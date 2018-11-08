<?php

namespace App\Http\Controllers\MasterData;

use App\Models\MasterData\Skpd;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SkpdController extends MasterDataController
{
    public function index(){
        return view('layouts/admin/skpd/index');
    }

    public function show($id){
        $skpd = Skpd::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        return view('layouts.admin.skpd.detail',compact('skpd'));
    }

    public function edit($id){
        $skpd = Skpd::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        return view('layouts.admin.skpd.edit',compact('skpd'));
    }

    public function add(){
        return view('layouts.admin.skpd.add');
    }

    public function store(Request $request,$json = true){
        $this->validate($request,[
            'nama_skpd' => 'required',
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        $skpd = Skpd::create($input);
        if ($json) {
            return response()->json($skpd);
        }
        return $skpd;
    }

    public function update(Request $request,$id,$json = true){
        $skpd = Skpd::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        $this->validate($request,[
            'nama_skpd' => 'required',
        ]);
        $input = $request->input();
        $skpd->update($input);
        if ($json)
            return response()->json($skpd);

        return $skpd;
    }

    public function delete($id,$json = true){
        $skpd = Skpd::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        try {
            $skpd->delete();
        } catch (\Exception $exception){}
        if ($json)
            return response()->json([
                'message' => 'data berhasil dihapus'
            ]);

        return [
            'message' => 'data berhasil dihapus'
        ];
    }
}
