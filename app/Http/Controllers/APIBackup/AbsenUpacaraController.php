<?php

namespace App\Http\Controllers\APIBackup;

use App\Repositories\AbsenUpacaraRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterData\AbsenUpacara;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AbsenUpacaraController extends Controller
{
    protected $show_limit = 10;
    protected $absen_upacara;
    public function __construct()
    {
        $this->absen_upacara = new AbsenUpacaraRepository();
    }

    public function list(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $data = $this->absen_upacara->search($request->query(),$this->show_limit);
        return $this->ApiSpecResponses(
            $data
        );
    }

    public function page(Request $request){
        $data = $this->absen_upacara->getPage($request->query());
        $data = ceil($data / $this->show_limit);
        return response()->json([ 'page'=> $data ]);
    }

    public function store(Request $request){
        $validation = Validator::make($request->input(),$this->absen_upacara->required());
        if ($validation->fails()){
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ],422);
        }
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        if ($absen_upacara = $this->absen_upacara->create($input)){
            return $this->ApiSpecResponses($absen_upacara);
        }
        return $this->ApiSpecResponses([
            'message' => 'gagal menyimpan jabatan'
        ],500);
    }

    public function delete(Request $request){
        if ($this->absen_upacara->deleteAbsenUpcara($request->uuid)){
            return $this->ApiSpecResponses([
                'message' => 'Absen Upacara berhasil dihapus'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'Absen Upcara gagal dihapus'
        ],500);
    }

    public function update(Request $request){
        $validation = Validator::make($request->input(),$this->absen_upacara->required());
        if ($validation->fails()){
            return $this->ApiSpecResponses([
                'required' => $validation->errors()
            ],422);
        }
        $update = $request->input();
        if ($this->absen_upacara->updateAbsenUpacara($update['uuid'],$update)){
            return $this->ApiSpecResponses([
                'message' => 'berhasil mengupdate Absen Upacara'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'Gagal mengupdate data Absen Upacara'
        ], 500);
    }
}
