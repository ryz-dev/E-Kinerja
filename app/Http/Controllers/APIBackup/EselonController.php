<?php

namespace App\Http\Controllers\APIBackup;

use App\Models\MasterData\Eselon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;

class EselonController extends ApiController
{
    public function listEselon(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        try {
            $eselon = Eselon::orderBy('created_at', 'DESC');
            if ($request->has('q')) {
                $eselon = $eselon->where('eselon', 'like', '%' . $request->input('q') . '%')
                    ->orWhere('tunjangan', 'like', '$' . $request->input('q') . '%');
            }
            $eselon = $eselon->paginate($this->show_limit);
            return $this->ApiSpecResponses($eselon);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function detailEselon($id)
    {
        try {
            $eselon = Eselon::where('id', $id)->orWhere('uuid', $id)->firstOrFail();
            return $this->ApiSpecResponses($eselon);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function storeEselon(Request $request)
    {
        $eselon = new \App\Http\Controllers\Admin\EselonController();
        $data = $eselon->store($request,false);
        return $this->ApiSpecResponses($data);
    }

    public function updateEselon(Request $request,$id)
    {
        $eselon = new \App\Http\Controllers\Admin\EselonController();
        $data = $eselon->update($request,$id,false);
        return $this->ApiSpecResponses($data);
    }

    public function deleteEselon($id)
    {
        $eselon = new \App\Http\Controllers\Admin\EselonController();
        $data = $eselon->delete($id,false);
        return $this->ApiSpecResponses($data);
    }

    public function getPage(Request $request)
    {
        if ($request->has('q')) {
            $data = Eselon::where('eselon', 'like', '%' . $request->input('q') . '%')
                ->orWhere('tunjangan', 'like', '$' . $request->input('q') . '%')->count();
        } else {
            $data = Eselon::count();
        }
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }
}
