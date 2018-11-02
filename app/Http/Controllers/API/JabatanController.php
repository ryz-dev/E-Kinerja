<?php

namespace App\Http\Controllers\API;

use App\Models\MasterData\Jabatan;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;

class JabatanController extends ApiController
{
    public function listJabatan(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        try {
            $jabatan = Jabatan::with('eselon','atasan')->orderBy('created_at', 'DESC');
            if ($request->has('q')) {
                $jabatan = $jabatan->where('jabatan','like','%'.$request->input('q').'%');
            }
            $jabatan = $jabatan->paginate($this->show_limit);
            return $this->ApiSpecResponses($jabatan);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function detailJabatan($id){
        try {
            $jabatan = Jabatan::with('eselon','atasan')->where('id',$id)->orWhere('uuid',$id)->firstOrFail();
            return $this->ApiSpecResponses($jabatan);
        } catch (\Exception $exception){
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function addJabatan(Request $request){
        $jabatan = new \App\Http\Controllers\MasterData\JabatanController();
        $data = $jabatan->store($request);
        return $this->ApiSpecResponses($data);
    }

    public function getPage(Request $request)
    {
        if ($request->has('q')) {
            $data = Jabatan::where('jabatan','like','%'.$request->input('q').'%')->count();
        } else {
            $data = Jabatan::count();
        }
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }
}
