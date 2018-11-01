<?php

namespace App\Http\Controllers\API;

use App\Models\MasterData\HariKerja;
use App\Models\MasterData\StatusHari;
use App\Http\Controllers\MasterData\MasterDataController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class HariKerjaController extends MasterDataController
{
    public function index(Request $request){
      /* Ambil kata kunci yang di cari */
      $this->query = $request->has('q') ? $request->input('q') : $this->query;

      /* Proses query */
      $hariKerja = HariKerja::where('tanggal','like','%'.$this->query.'%')
      ->orWhere('bulan','like','%'.$this->query.'%')
      ->orWhere('hari','like','%'.$this->query.'%')
      ->orWhere('id_status_hari','like','%'.$this->query.'%')
      ->orWhere('tahun','like','%'.$this->query.'%')
      ->paginate(20);

      if (!$hariKerja->isEmpty()) {
        /* Paginate  */
        $data = $this->paging($hariKerja);

        /* Response */
        foreach ($hariKerja as $key => $value) {
          $data['response'][] = $value;
        }

        /* Diagnostic */
        $data['diagnostic'] = [
          'code'=>200,
          'status'=>'HTTP_OK'
        ];

        return response()->json($data, 200);
      }
      return response()->json([
        'diagnostic'=>[
          'code'=>200,
          'status'=> 'NOT_FOUND'
        ]
      ],200);
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

    public function getPage(Request $request){
      /* Ambil kata kunci yang di cari */
      $this->query = $request->has('q') ? $request->input('q') : $this->query;

      /* Proses query */
      $data = HariKerja::where('tanggal','like','%'.$this->query.'%')
      ->orWhere('bulan','like','%'.$this->query.'%')
      ->orWhere('hari','like','%'.$this->query.'%')
      ->orWhere('id_status_hari','like','%'.$this->query.'%')
      ->orWhere('tahun','like','%'.$this->query.'%')
      ->count();
      $data = ceil($data/20);

      return response()->json([
          'halaman' => $data
      ]);
    }

}
