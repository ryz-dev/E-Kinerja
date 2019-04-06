<?php

namespace App\Http\Controllers\Admin;

use App\Models\MasterData\HariKerja;
use App\Models\MasterData\StatusHari;
use App\Repositories\HariKerjaRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class HariKerjaController extends AdminController
{

    protected $hari_kerja;
    public function __construct(HariKerjaRepository $hari_kerja)
    {
        $this->hari_kerja = $hari_kerja;
    }

    public function index(Request $request){
      return view('layouts/admin/harikerja/index');
    }

    public function add(){
      $data['status'] = StatusHari::get();
      return view('layouts/admin/harikerja/add',$data);
    }

    public function store(Request $request){
      $this->validate($request,[
        'tgl_mulai' => 'required',
        'tgl_selesai' => 'required',
      ]);
      if (strtotime($request->tgl_selesai) >= strtotime($request->tgl_mulai)) {
          $tgl = $request->tgl_mulai;
          while(strtotime($tgl) <= strtotime($request->tgl_selesai)) {
              try {
                  $input['tanggal'] = $tgl;
                  $input['uuid'] = (string)Str::uuid();
                  $input['bulan'] = (int)date('m', strtotime($input['tanggal']));
                  $input['tahun'] = date('Y', strtotime($input['tanggal']));
                  $input['hari'] = date('N', strtotime($input['tanggal']));
                  if ($input['hari'] == 6 || $input['hari'] == 7) {
                      $input['id_status_hari'] = 2;
                  } else {
                      $input['id_status_hari'] = 1;
                  }
                  $hariKerja = HariKerja::create($input);
//                  return redirect(route('hari_kerja'));
              } catch (\Exception $e) {
                  /* Jika tanggal yang di set sudah terimpan di database */
                  /*return redirect()
                      ->back()
                      ->with('message', 'Tanggal sudah ada !');*/
//                  return response()->json($e,500);
              }
              $tgl = date('Y-m-d',strtotime($tgl) + 86400);
          }
          return redirect(route('hari_kerja'));
      }
      return redirect()
          ->back()
          ->with('message','Tanggal Selesai harus lebih besar atau sama dengan Tanggal Mulai');
    }

    public function edit($id){
      $data['harikerja'] = HariKerja::where('id',$id)
      ->orWhere('uuid',$id)
      ->firstOrFail();
      $data['status'] = StatusHari::get();
      return view('layouts/admin/harikerja/edit',$data);
    }

    public function update(Request $request,$id){
      $this->validate($request,[
          'tanggal' => 'required',
          'id_status_hari' => 'required|in:'.$this->getStatusHari()
      ]);

      try {

        $hari_kerja = HariKerja::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        $input = $request->input();
        $input['bulan'] = (int)date('m',strtotime($request->input('tanggal')));
        $input['tahun'] = date('Y',strtotime($request->input('tanggal')));
        $input['hari'] = date('N',strtotime($request->input('tanggal')));
        $hari_kerja->update($input);
        return redirect( route('hari_kerja') );
        
      } catch (\Exception $e) {

        /* Jika tanggal yang di set sudah terimpan di database */
        return redirect()
          ->back()
          ->with('message', 'Tanggal sudah ada !');
      }
    }

    private function getStatusHari(){
      return implode(',',StatusHari::select('id')->pluck('id')->all());
    }

    public function show($id){
      $hari_kerja = HariKerja::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
      return response()->json($hari_kerja->toArray());
    }

    public function list(Request $request)
    {
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $golongan = $this->hari_kerja->with(['statusHari','Bulan','Hari'])->orderBy('tanggal', 'asc')->search($request->query(),$this->show_limit);
        return $this->ApiSpecResponses($golongan);

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
