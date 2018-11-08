<?php

namespace App\Http\Controllers\API;

use App\Models\MasterData\Skpd;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;

class SkpdController extends ApiController
{
    public function listSkpd(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        try {
            $skpd = Skpd::orderBy('created_at', 'DESC');
            if ($request->has('q')) {
                $skpd = $skpd->where('nama_skpd','like','%'.$request->input('q').'%');
            }
            $skpd = $skpd->paginate($this->show_limit);
            return $this->ApiSpecResponses($skpd);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function detailSkpd($id){
        try {
            $skpd = Skpd::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
            return $this->ApiSpecResponses($skpd);
        } catch (\Exception $exception){
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function storeSkpd(Request $request){
        $skpd = new \App\Http\Controllers\MasterData\SkpdController();
        $data = $skpd->store($request,false);
        return $this->ApiSpecResponses($data);
    }

    public function updateSkpd(Request $request,$id){
        $skpd = new \App\Http\Controllers\MasterData\SkpdController();
        $data = $skpd->update($request,$id,false);
        return $this->ApiSpecResponses($data);
    }

    public function deleteSkpd($id){
        $skpd = new \App\Http\Controllers\MasterData\SkpdController();
        $data = $skpd->delete($id,false);
        return $this->ApiSpecResponses($data);
    }

    public function getPage(Request $request)
    {
        if ($request->has('q')) {
            $data = Skpd::where('nama_skpd','like','%'.$request->input('q').'%')
                ->count();
        } else {
            $data = Skpd::count();
        }
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }
}
