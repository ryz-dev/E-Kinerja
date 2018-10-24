<?php

namespace App\Http\Controllers\MasterData;

use App\Models\MasterData\HariKerja;
use App\Models\MasterData\StatusHari;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class HariKerjaController extends MasterDataController
{
    public function index(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $this->query = $request->has('q') ? $request->input('q') : $this->query;
        $hari_kerja = new HariKerja();
        if ($this->query){
            $hari_kerja = $hari_kerja->where('tanggal','like','%'.$this->query.'%')
                ->orWhere('bulan','like','%'.$this->query.'%')
                ->orWhere('hari','like','%'.$this->query.'%')
                ->orWhere('id_status_hari','like','%'.$this->query.'%')
                ->orWhere('tahun','like','%'.$this->query.'%');
        }
        $hari_kerja = $hari_kerja->paginate($this->show_limit);
        return response()->json($hari_kerja->toArray());
    }

    public function show($id){
        $hari_kerja = HariKerja::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        return response()->json($hari_kerja->toArray());
    }


    public function store(Request $request){
        $this->validate($request,[
            'tanggal' => 'required',
            'id_status_hari' => 'required|in:'.$this->getStatusHari()
        ]);
        $input = $request->input();
        $input['uuid'] = (string)Str::uuid();
        $input['bulan'] = (int)date('m',strtotime($request->input('tanggal')));
        $input['tahun'] = date('Y',strtotime($request->input('tanggal')));
        $input['hari'] = date('N',strtotime($request->input('tanggal')));
        $agama = HariKerja::create($input);
        return response()->json($agama->toArray());
    }

    public function update(Request $request,$id){
        $this->validate($request,[
            'tanggal' => 'required',
            'id_status_hari' => 'required|in:'.$this->getStatusHari()
        ]);
        $hari_kerja = HariKerja::where('id',$id)->orWhere('uuid',$id)->firstOrFail();
        $input = $request->input();
        $input['bulan'] = (int)date('m',strtotime($request->input('tanggal')));
        $input['tahun'] = date('Y',strtotime($request->input('tanggal')));
        $input['hari'] = date('N',strtotime($request->input('tanggal')));
        $hari_kerja->update($input);
        return response()->json($hari_kerja->toArray());
    }

    public function delete($id){
        $hari_kerja = HariKerja::whereId($id)->orWhere('uuid',$id)->firstOrFail();
        try {
            $hari_kerja->delete();
        } catch (\Exception $exception){}
        return response()->json([
            'message' => 'berhasil menghapus data'
        ]);
    }

    private function getStatusHari(){
        return implode(',',StatusHari::select('id')->pluck('id')->all());
    }
}
