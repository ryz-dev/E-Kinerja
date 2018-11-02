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
        // return $pegawai = Pegawai::with('role')->orderBy('created_at', 'DESC')->get();s
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        try {
            $pegawai = Pegawai::with('role','jabatan')->orderBy('created_at', 'DESC');
            if ($request->has('q')) {
                $pegawai = $pegawai->where('nip','like','%'.$request->input('q').'%')
                    ->orWhere('nama','like','$'.$request->input('q').'%');
            }
            $pegawai = $pegawai->paginate($this->show_limit);
            return $this->ApiSpecResponses($pegawai);
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

}
