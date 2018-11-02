<?php

namespace App\Http\Controllers\API;

use App\Models\MasterData\HariKerja;
use App\Models\MasterData\StatusHari;
use App\Http\Controllers\MasterData\MasterDataController;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class HariKerjaController extends ApiController
{
    public function index(Request $request){
        /* Ambil kata kunci yang di cari */
        $this->query = $request->has('q') ? $request->input('q') : $this->query;

        /* Proses query */
        $hariKerja = HariKerja::with('StatusHari','Bulan','Hari')
        ->where('tanggal','like','%'.$this->query.'%')
        ->orWhere('bulan','like','%'.$this->query.'%')
        ->orWhere('hari','like','%'.$this->query.'%')
        ->orWhere('id_status_hari','like','%'.$this->query.'%')
        ->orWhere('tahun','like','%'.$this->query.'%')
        ->paginate($this->show_limit);

        if (!$hariKerja->isEmpty()) {

          /* Paging */
          $data['paging'] = $this->paging($hariKerja);

          /* Response */
          foreach ($hariKerja as $key => $value) {
            $data['response'][] = [
              'id'=>$value->id,
              'url_edit'=>route('hari_kerja_edit',['id'=>$value->id]),
              'status_hari'=>json_decode($value)->status_hari->status_hari,
              'tanggal'=>$value->tanggal,
              'hari'=>ucfirst($value->Hari->nama_hari),
              'bulan'=>ucfirst($value->Bulan->nama_bulan),
              'tahun'=>$value->tahun
            ];
          }
          return $this->ApiResponse($data);
        } else {
          return response()->json([
            'diagnostic'=>[
              'code'=>404,
              'status'=> 'NOT_FOUND'
            ]
          ],200);
        }
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
      $data = ceil($data/$this->show_limit);
      return response()->json([
        'halaman' => $data
      ]);
    }

    public function delete(Request $request) {
      try {
        $item = HariKerja::findOrfail($request->id);
        $item->delete();
        /* Diagnostic */
        return response()->json([
          'diagnostic'=>[
            'code'=>200,
            'status'=>'HTTP_OK'
          ]
        ],200);
      } catch (ModelNotFoundException $e) {
        /* Diagnostic */
        return response()->json([
          'diagnostic'=>[
            'code'=>503,
            'status'=>'DATA_NOT_FOUND'
          ]
        ],503);
      }
    }
}
