<?php

namespace App\Http\Controllers\APIBackup;

use App\Models\MasterData\Golongan;
use App\Repositories\GolonganRepository;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GolonganController extends ApiController
{

    protected $golongan;
    public function __construct(GolonganRepository $golongan)
    {
        $this->golongan = $golongan;
    }

    public function listGolongan(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $golongan = $this->golongan->orderBy('created_at', 'DESC')->search($request->query(),$this->show_limit);
        return $this->ApiSpecResponses($golongan);

    }

    public function detailGolongan($id)
    {
        if ($golongan = $this->golongan->find($id)){
            return $this->ApiSpecResponses($golongan);
        }
        return $this->ApiSpecResponses([
            'message' => 'NOT_FOUND'
        ], 404);
    }

    public function storeGolongan(Request $request)
    {
        $validation = Validator::make($request->input(),$this->golongan->required());
        if ($validation->fails()){
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ],422);
        }
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        $data = $this->golongan->create($input);
        return $this->ApiSpecResponses($data);
    }

    public function updateGolongan(Request $request,$id)
    {
        $validation = Validator::make($request->input(),$this->golongan->required());
        if ($validation->fails()){
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ],422);
        }
        $update = $request->input();
        if ($this->golongan->update($id,$update)){
            return $this->ApiSpecResponses([
                'message' => 'berhasil mengupdate golongan'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'Gagal mengupdate data golongan'
        ],500);

    }

    public function deleteGolongan($id)
    {
        if ($this->golongan->delete($id)){
            return $this->ApiSpecResponses([
                'message' => 'skp pegawai berhasil dihapus'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'skp pegawai gagal dihapus'
        ]);
    }

    public function getPage(Request $request)
    {
        $data = $this->golongan->getPage($request->query());
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }
}
