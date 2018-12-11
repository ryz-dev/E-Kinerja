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
            $pegawai = Pegawai::with('jabatan','agama','skpd')->orderBy('created_at', 'DESC');
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
            $pegawai = Pegawai::with('jabatan','agama','skpd')->where('nip',$id)->orWhere('uuid',$id)->firstOrFail();
            return $this->ApiSpecResponses($pegawai);
        } catch (\Exception $exception){
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function storePegawai(Request $request){
        $pegawai = new \App\Http\Controllers\MasterData\PegawaiController();
        $data = $pegawai->store($request,false);
        return $this->ApiSpecResponses($data);
    }

    public function updatePegawai(Request $request,$id){
        $pegawai = new \App\Http\Controllers\MasterData\PegawaiController();
        $data = $pegawai->update($request,$id,false);
        return $this->ApiSpecResponses($data);
    }

    public function deletePegawai($id){
        $pegawai = new \App\Http\Controllers\MasterData\PegawaiController();
        $data = $pegawai->delete($id,false);
        return $this->ApiSpecResponses($data);
    }

    public function updatePassword(Request $request){
        $user = auth('web')->user();
        
        if ($request->input('oldPassword') === $request->input('newPassword')) {
            return response()->json([
                'message' => 'Kata sandi lama dan kata sandi baru tidak boleh sama!'
            ],500);
        }

        if (\Hash::check($request->input('oldPassword'), $user->password)){
            $pegawai = new \App\Http\Controllers\MasterData\PegawaiController();
            $data = $pegawai->updatePassword($user->nip,$request->input('newPassword'));
            return $this->ApiSpecResponses($data);
        }
        else{
            return response()->json([
                'message' => 'Kata sandi lama salah!'
            ],500);
        }
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
