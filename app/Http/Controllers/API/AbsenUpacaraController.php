<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterData\AbsenUpacara;

class AbsenUpacaraController extends Controller
{
    protected $show_limit = 10;
    
    public function list(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $absen_upacara = AbsenUpacara::where('SN','<>','');
        try {
            if ($request->has('q')) {
                $absen_upacara = $absen_upacara->where('SN','like','%'.$request->input('q').'%');
            }
            $absen_upacara = $absen_upacara->paginate($this->show_limit);
            return $this->ApiSpecResponses(
                $absen_upacara
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function page(){
        $data = AbsenUpacara::count();
        $data = ceil($data / $this->show_limit);

        return response()->json([ 'page'=> $data ]);
    }

    public function store(Request $request){
        $absen_upacara = new \App\Http\Controllers\MasterData\AbsenUpacaraController;
        $absen_upacara->store($request);
        return $this->ApiSpecResponses($absen_upacara);
    }

    public function delete(Request $request){
        $absen_upacara = new \App\Http\Controllers\MasterData\AbsenUpacaraController;
        $absen_upacara->destroy($request);
        return $this->ApiSpecResponses($absen_upacara);
    }

    public function update(Request $request){
        $absen_upacara = new \App\Http\Controllers\MasterData\AbsenUpacaraController;
        $absen_upacara->update($request);
        return $this->ApiSpecResponses($absen_upacara);
    }
}
