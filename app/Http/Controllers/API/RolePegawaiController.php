<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Pegawai;
Use App\Models\MasterData\Role;
Use App\Models\MasterData\Jabatan;

class RolePegawaiController extends Controller
{
    public function listRole(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $pegawai = Pegawai::with('role','jabatan')->orderBy('id_jabatan', 'ASC');
        try {
            if ($request->has('q')) {
                $pegawai = $pegawai->where('nip','like','%'.$request->input('q').'%')
                ->orWhere('nama','like','%'.$request->input('q').'%');
            }
            $pegawai = $pegawai->paginate($this->show_limit);
            return $this->ApiSpecResponses(
                $pegawai
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function getPage(){
        $data = Pegawai::count();
        $data = ceil($data / $this->show_limit);

        return response()->json([ 'page'=> $data ]);
    }

    public function getRoles(){
        $roles = Role::pluck('nama_role','id');
        return $this->ApiSpecResponses($roles);
    }

    public function store(Request $request){
        $pegawai = Pegawai::find($request->input('nip'));
        $data = $pegawai->role()->attach($request->input('role'),['uuid'=>(string)\Illuminate\Support\Str::uuid()]);
        return $this->ApiSpecResponses($data);
    }

    public function delete(Request $request){
        $pegawai = Pegawai::find($request->input('nip'));
        $data = $pegawai->role()->where('id',$request->input('role_id'))->detach();
        return $this->ApiSpecResponses($data);
    }

}
