<?php

namespace App\Http\Controllers\MasterData;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterData\AbsenUpacara;
use Illuminate\Support\Str;

class AbsenUpacaraController extends Controller
{
    public function index(){
        return view('layouts.admin.absen-upacara.index');
    }

    public function store(Request $request){
        $this->validate($request, [
            'SN' => 'required|min:3',
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        $absen_upacara = AbsenUpacara::create($input);
        return $absen_upacara;
    }

    public function destroy(Request $request){
        $input = $request->input();
        $absen_upacara = AbsenUpacara::where('uuid',$input['uuid']);
        return $absen_upacara->delete();
        
    }

    public function update(Request $request){
        $input = $request->input();
        $absen_upacara = AbsenUpacara::where('uuid',$input['uuid'])->first();
        $absen_upacara->SN = $input['SN'];
        return $absen_upacara->save();
    }
}
