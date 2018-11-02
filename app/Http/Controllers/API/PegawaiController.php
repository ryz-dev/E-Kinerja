<?php

namespace App\Http\Controllers\API;

use App\Models\MasterData\Agama;
use App\Models\MasterData\Jabatan;
use App\Models\MasterData\Pegawai;
use App\Http\Controllers\MasterData\MasterDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;

class PegawaiController extends ApiController
{
    public function listPegawai(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        try {
            $pegawai = Pegawai::with('jabatan','agama')->orderBy('created_at', 'DESC');
            if ($request->has('q')) {
                $pegawai = $pegawai->where('nip','like','%'.$request->input('q').'%')
                    ->orWhere('nama','like','%'.$request->input('q').'%');
            }
            $pegawai = $pegawai->paginate($this->show_limit);
            return $this->ApiSpecResponses($pegawai);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function detailPegawai($id){
        try {
            $pegawai = Pegawai::with('jabatan','agama')->where('nip',$id)->orWhere('uuid',$id)->firstOrFail();
            return $this->ApiSpecResponses($pegawai);
        } catch (\Exception $exception){
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function addPegawai(Request $request){
        $pegawai = new \App\Http\Controllers\MasterData\PegawaiController();
        $data = $pegawai->store($request);
        return $this->ApiSpecResponses($data);
    }

    public function getPage(Request $request)
    {
        if ($request->has('q')) {
            $data = Pegawai::where('nip','like','%'.$request->input('q').'%')
                ->orWhere('nama','like','%'.$request->input('q').'%')
                ->count();
        } else {
            $data = Pegawai::count();
        }
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }
}
