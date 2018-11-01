<?php

namespace App\Http\Controllers\MasterData;

use App\Models\MasterData\Agama;
use App\Models\MasterData\Jabatan;
use App\Models\MasterData\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PegawaiController extends MasterDataController
{
    public function index(){
//        $data['pegawai']  = Pegawai::with('jabatan.pegawai_bawahan.jabatan')->get();
        return view('layouts/admin/pegawai/index');
    }

    public function show($id){
        $pegawai = Pegawai::with('jabatan','agama')->where('nip',$id)->orWhere('uuid',$id)->firstOrFail();
        return view('layouts.admin.pegawai.detail',compact('pegawai'));
    }

    public function edit($id){
        $pegawai = Pegawai::with('jabatan','agama')->where('nip',$id)->orWhere('uuid',$id)->firstOrFail();
        $data_option = new \stdClass();
        $data_option->agama = Agama::get();
        $data_option->jabatan = Jabatan::get();
        return view('layouts.admin.pegawai.edit',compact('pegawai','data_option'));
    }

    public function add(){
        $data_option = new \stdClass();
        $data_option->agama = Agama::get();
        $data_option->jabatan = Jabatan::get();
        return view('layouts.admin.pegawai.add',compact('data_option'));
    }

    public function store(Request $request){
        $this->validate($request,[
            'nip' => 'required|unique:pegawai,nip',
            'foto' => 'image',
            'nama' => 'required',
            'tanggal_lahir' => 'required|date',
            'id_agama' => 'required|in:'.$this->getListAgama(),
            'id_jabatan' => 'in:'.$this->getListJabatan(),
            'jns_kel' => 'required|in:laki-laki,perempuan',
            'tempat_lahir' => 'required'
        ]);
        $input = $request->input();
        /*upload file foto*/
        if ($request->hasFile('foto')) {
            $input['foto'] = $this->uploadFoto($request);
        }
        /*================*/
        $input['uuid'] = (string)Str::uuid();
        $pegawai = Pegawai::create($input);
        return response()->json($pegawai);
    }

    public function update(Request $request,$id){
        $pegawai = Pegawai::where('nip',$id)->orWhere('uuid',$id)->firstOrFail();
        $this->validate($request,[
            'nip' => 'unique:pegawai,nip,'.$request->input('nip').',nip',
            'foto' => 'image',
            'id_agama' => 'in:'.$this->getListAgama(),
            'id_jabatan' => 'in:'.$this->getListJabatan(),
            'jns_kel' => 'in:laki-laki,perempuan',
        ]);
        $input = $request->input();
        /*upload file foto*/
        if ($request->hasFile('foto')) {
            $input['foto'] = $this->uploadFoto($request);
        }
        /*================*/
        $pegawai->update($input);
        return response()->json($pegawai);
    }

    public function delete($id){
        $pegawai = Pegawai::where('nip',$id)->orWhere('uuid',$id)->firstOrFail();
        try {
            $pegawai->delete();
        } catch (\Exception $exception){}
        return response()->json([
            'message' => 'data berhasil dihapus'
        ]);
    }

    private function getListAgama(){
        return implode(',',Agama::select('id')->pluck('id')->all());
    }

    private function getListJabatan(){
        return implode(',',Jabatan::select('id')->pluck('id')->all());
    }

    private function uploadFoto(Request $request){
        /*todo : handle untuk upload foto*/
        if ($request->hasFile('foto')) {
            return str_replace('public/','',$request->file('foto')->store('public/upload'));
        }
    }

}
