<?php

namespace App\Http\Controllers\APIBackup;

use App\Models\MasterData\HariKerja;
use App\Models\MasterData\StatusHari;
use App\Http\Controllers\Admin\AdminController;
use App\Repositories\HariKerjaRepository;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class HariKerjaController extends ApiController
{
    protected $hari_kerja;
    public function __construct()
    {
        $this->hari_kerja = new HariKerjaRepository();
    }

    public function index(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $hariKerja = $this->hari_kerja->with(['StatusHari','Bulan','Hari'])->search($request->query(),$this->show_limit);
        if (!$hariKerja->isEmpty()) {

          /* Paging */
          $data['paging'] = $this->paging($hariKerja);

          /* Response */
          foreach ($hariKerja as $key => $value) {
            $data['response'][] = [
              'id'=>$value->id,
              'url_edit'=>route('hari_kerja_edit',['id'=>$value->id]),
              'status_hari'=>json_decode($value)->status_hari->status_hari,
              'tanggal'=> $this->formatDate($value->tanggal),
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
      $data = $this->hari_kerja->getPage($request->query());
      $data = ceil($data/$this->show_limit);
      return response()->json([
        'halaman' => $data
      ]);
    }

    public function delete(Request $request) {
        if ($this->hari_kerja->delete($request->id)){
            return response()->json([
                'diagnostic'=>[
                    'code'=>200,
                    'status'=>'HTTP_OK'
                ]
            ],200);
        }
        return response()->json([
          'diagnostic'=>[
            'code'=>503,
            'status'=>'DATA_NOT_FOUND'
          ]
        ],503);
    }
}
