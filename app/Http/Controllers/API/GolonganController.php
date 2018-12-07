<?php

namespace App\Http\Controllers\API;

use App\Models\MasterData\Golongan;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;

class GolonganController extends ApiController
{
    public function listGolongan(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        try {
            $golongan = Golongan::orderBy('created_at', 'DESC');
            if ($request->has('q')) {
                $golongan = $golongan->where('golongan', 'like', '%' . $request->input('q') . '%')
                    ->orWhere('tunjangan', 'like', '$' . $request->input('q') . '%');
            }
            $golongan = $golongan->paginate($this->show_limit);
            return $this->ApiSpecResponses($golongan);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function detailGolongan($id)
    {
        try {
            $golongan = Golongan::where('id', $id)->orWhere('uuid', $id)->firstOrFail();
            return $this->ApiSpecResponses($golongan);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function storeGolongan(Request $request)
    {
        $golongan = new \App\Http\Controllers\MasterData\GolonganController();
        $data = $golongan->store($request,false);
        return $this->ApiSpecResponses($data);
    }

    public function updateGolongan(Request $request,$id)
    {
        $golongan = new \App\Http\Controllers\MasterData\GolonganController();
        $data = $golongan->update($request,$id,false);
        return $this->ApiSpecResponses($data);
    }

    public function deleteGolongan($id)
    {
        $golongan = new \App\Http\Controllers\MasterData\GolonganController();
        $data = $golongan->delete($id,false);
        return $this->ApiSpecResponses($data);
    }

    public function getPage(Request $request)
    {
        if ($request->has('q')) {
            $data = Golongan::where('golongan', 'like', '%' . $request->input('q') . '%')
                ->orWhere('tunjangan', 'like', '$' . $request->input('q') . '%')->count();
        } else {
            $data = Golongan::count();
        }
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }
}
