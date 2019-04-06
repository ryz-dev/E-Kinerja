<?php

namespace App\Http\Controllers\APIBackup;


use App\Models\Skp;
use App\Models\SkpPegawai;
use App\Repositories\SkpPegawaiRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SkpPegawaiController extends ApiController
{
    protected $skp_pegawai;
    public function __construct(SkpPegawaiRepository $skp)
    {
        $this->skp_pegawai = $skp;
    }

    public function listSkpPegawai(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $skp_pegawai = $this->skp_pegawai->with(['pegawai','skpTask','atasanUpdate'])->orderBy('id','asc')->search($request->query(),$request->input());
        return $this->ApiSpecResponses($skp_pegawai);
    }

    public function detailSkpPegawai($id){

        if ($skp_pegawai = $this->skp_pegawai->with(['pegawai','skpTask','atasanUpdate'])->find($id)){
            return $this->ApiSpecResponses($skp_pegawai);
        }
        return $this->ApiSpecResponses([
            'message' => 'NOT_FOUND'
        ], 404);
    }

    public function storeSkpPegawai(Request $request){
        $validation = Validator::make($request->input(),$this->skp_pegawai->required());
        if ($validation->fails()){
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ],422);
        }
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        if ($skp_pegawai = $this->skp_pegawai->create($input)){
            return $this->ApiSpecResponses($skp_pegawai);
        }
        return $this->ApiSpecResponses([
            'message' => 'gagal menyimpan skp pegawai'
        ],500);
    }

    public function updateSkpPegawai(Request $request,$id){
        $validation = Validator::make($request->input(),$this->skp_pegawai->required());
        if ($validation->fails()){
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ],422);
        }
        $update = $request->input();

        if ($this->skp_pegawai->update($id,$update)){
            return $this->ApiSpecResponses([
                'message' => 'berhasil mengupdate skp pegawai'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'Gagal mengupdate data skp pegawai'
        ], 500);

    }

    public function deleteSkpPegawai($id){
        if ($this->skp_pegawai->delete($id)){
            return response()->json([
                'status' => 200,
                'message' => 'skp pegawai berhasil dihapus'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'skp pegawai gagal dihapus'
        ],500);
    }

    public function getPageSkpPegawai(Request $request)
    {
        $data = $this->skp_pegawai->getPage($request->query());
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }

}
