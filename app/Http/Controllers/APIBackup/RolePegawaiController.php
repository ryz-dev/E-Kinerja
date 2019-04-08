<?php

namespace App\Http\Controllers\APIBackup;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Role;
use App\Repositories\PegawaiRepository;
use Illuminate\Http\Request;

class RolePegawaiController extends Controller
{
    protected $pegawai;

    public function __construct()
    {
        $this->pegawai = new PegawaiRepository();
    }

    public function listRole(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $pegawai = $this->pegawai->with(['role', 'jabatan'])->orderBy('id_jabatan', 'ASC')->search($request->query(), $this->show_limit);
        return $this->ApiSpecResponses($pegawai);
    }

    public function getPage(Request $request)
    {
        $data = $this->pegawai->getPage($request->query());
        $data = ceil($data / $this->show_limit);
        return response()->json(['page' => $data]);
    }

    public function getRoles()
    {
        $roles = Role::pluck('nama_role', 'id');
        return $this->ApiSpecResponses($roles);
    }

    public function store(Request $request)
    {
        $data = $this->pegawai->storeRole($request->input('nip'), $request->input('role'));
        return $this->ApiSpecResponses($data);
    }

    public function delete(Request $request)
    {
        $data = $this->pegawai->deleteRole($request->input('nip'));
        return $this->ApiSpecResponses($data);
    }

}
