<?php

namespace App\Http\Controllers\API;

use App\Models\MasterData\Agama;
use App\Models\MasterData\Jabatan;
use App\Models\MasterData\Pegawai;
use App\Http\Controllers\MasterData\MasterDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException as Exception;

class PegawaiController extends MasterDataController
{
  public function listPegawai() {
      try {
        $pegawai = Pegawai::with('jabatan.pegawai_bawahan.jabatan')
                            ->orderBy('created_at','DESC')
                            ->paginate($this->show_limit);
        /* Paginate  */
        $paginate = $this->paging($pegawai);
        $data = [
          'response'=>$pegawai,
          'diagnostic'=> [
            'code'=>200,
            'status'=>'HTTP_OK'
          ]
        ];
        return response()->json($data,200);
      } catch (Exception $e) {
        return response()->json([
          'message'=> 'NOT_FOUND'
        ],404);
      }
  }

  public function paging($raw)
  {
      $object = new \stdClass;
      $object->total = $raw->total();
      $object->per_page = $raw->perPage();
      $object->current_page = $raw->currentPage();
      $object->last_page = $raw->lastPage();
      $object->from = $raw->firstItem();
      $object->to = $raw->lastItem();
      return [
          'pagination' => $object
      ];
  }

    public function getPage(){
        $data = Pegawai::count();
        $data = ceil($data/$this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }
}
