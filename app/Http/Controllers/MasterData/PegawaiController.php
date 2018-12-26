<?php

namespace App\Http\Controllers\MasterData;

use App\Imports\PegawaiImport;
use App\Models\MasterData\Agama;
use App\Models\MasterData\Jabatan;
use App\Models\MasterData\Pegawai;
use App\Models\MasterData\Skpd;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class PegawaiController extends MasterDataController
{
    public function index()
    {
        return view('layouts/admin/pegawai/index');
    }

    public function show($id)
    {
        $pegawai = Pegawai::with('jabatan', 'agama', 'skpd')->where('nip', $id)->orWhere('uuid', $id)->firstOrFail();
        return view('layouts.admin.pegawai.detail', compact('pegawai'));
    }

    public function edit($id)
    {
        $pegawai = Pegawai::with('jabatan', 'agama', 'skpd')->where('nip', $id)->orWhere('uuid', $id)->firstOrFail();
        $data_option = new \stdClass();
        $data_option->agama = Agama::get();
        $data_option->jabatan = Jabatan::get();
        $data_option->skpd = Skpd::get();
        return view('layouts.admin.pegawai.edit', compact('pegawai', 'data_option'));
    }

    public function add()
    {
        $data_option = new \stdClass();
        $data_option->agama = Agama::get();
        $data_option->jabatan = Jabatan::get();
        $data_option->skpd = Skpd::get();
        return view('layouts.admin.pegawai.add', compact('data_option'));
    }

    public function store(Request $request, $json = true)
    {
        $this->validate($request, [
            'nip' => 'required|unique:pegawai,nip',
            'foto' => 'image',
            'nama' => 'required',
            'tanggal_lahir' => 'required|date',
            'id_agama' => 'required|in:'.$this->getListAgama(),
            'id_jabatan' => 'in:'.$this->getListJabatan(),
            'jns_kel' => 'required|in:laki-laki,perempuan',
            'tempat_lahir' => 'required',
            'id_skpd' => 'required',
            'status_upacara' => 'required'
        ]);
        $input = $request->input();
        /*upload file foto*/
        if ($request->hasFile('foto')) {
            $input['foto'] = $this->uploadFoto($request);
        }
        /*================*/
        $input['uuid'] = (string)Str::uuid();
        $input['password'] = bcrypt('secret');
        $pegawai = Pegawai::create($input);
        if ($json) {
            return response()->json($pegawai);
        }
        return $pegawai;
    }

    public function update(Request $request, $id, $json = true)
    {
        $pegawai = Pegawai::where('nip', $id)->orWhere('uuid', $id)->firstOrFail();
        $this->validate($request, [
            'nip' => 'unique:pegawai,nip,'.$request->input('nip').',nip',
            'foto' => 'image',
            'id_agama' => 'in:'.$this->getListAgama(),
            'id_jabatan' => 'in:'.$this->getListJabatan(),
            'jns_kel' => 'in:laki-laki,perempuan',
            'id_skpd' => 'required',
            'status_upacara' => 'required'
        ]);
        $input = $request->input();
        /*upload file foto*/
        if ($request->hasFile('foto')) {
            $input['foto'] = $this->uploadFoto($request);
        }
        /*================*/
        $pegawai->update($input);
        if ($json) {
            return response()->json($pegawai);
        }

        return $pegawai;
    }

    public function delete($id, $json = true)
    {
        $pegawai = Pegawai::where('nip', $id)->orWhere('uuid', $id)->firstOrFail();
        try {
            $pegawai->delete();
        } catch (QueryException $exception) {
            if ($json) {
                return response()->json([
                    'status' => '500',
                    'message' => 'Tidak dapat menghapus Pegawai, Pegawai memiliki pegawai aktif'
                ]);
            }
            return [
                'status' => '500',
                'message' => 'Tidak dapat menghapus Pegawai, Pegawai memiliki pegawai aktif'
            ];
        } catch (\Exception $exception) {
            if ($json) {
                return response()->json([
                    'status' => '500',
                    'message' => $exception->getMessage()
                ]);
            }
            return [
                'status' => '500',
                'message' => $exception->getMessage()
            ];
        }
        if ($json) {
            return response()->json([
                'status' => '200',
                'message' => 'data berhasil dihapus'
            ]);
        }

        return [
            'status' => '200',
            'message' => 'data berhasil dihapus'
        ];
    }

    public function deleted()
    {
        return view('layouts/admin/pegawai/deleted');
    }

    private function getListAgama()
    {
        return implode(',', Agama::select('id')->pluck('id')->all());
    }

    private function getListJabatan()
    {
        return implode(',', Jabatan::select('id')->pluck('id')->all());
    }

    private function uploadFoto(Request $request)
    {
        /*todo : handle untuk upload foto*/
        if ($request->hasFile('foto')) {
            return str_replace('public/', '', $request->file('foto')->store('public/upload'));
        }
    }

    public function updatePassword($nip, $newPassword)
    {
        $pegawai =Pegawai::whereNip($nip)->first();
        $pegawai->password = bcrypt($newPassword);
        $pegawai->save();
        return $pegawai;
    }

    public function import(Request $request)
    {
        Excel::import(new PegawaiImport, $request->file('import'));
        return redirect()->back();
    }
}
