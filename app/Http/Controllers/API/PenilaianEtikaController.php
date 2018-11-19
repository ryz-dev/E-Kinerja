<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Pegawai;

class PenilaianEtikaController extends ApiController
{
    public function getPegawai(){
        $user = auth('web')->user();
        $pegawai = Pegawai::wherehas('jabatan', function($query) use($user){ 
                                        $query->where('id_atasan','>',$user->id_jabatan ); })
                                    ->where('id_skpd',$user->id_skpd)
                                    ->with(['etika' => function($query){
                                        $query->whereDate('tanggal','=',date('Y-m-d'));
                                    }]);
        
        
        return $this->ApiSpecResponses($pegawai->get());
    }

    public function storePenilaian(Request $request){
        $this->validate($request, [
            'nip' => 'required',
            'persentase' => 'required',
            'keterangan' => 'required'
        ]);
        $data = $request->input();
        $data['tanggal'] = \Carbon\Carbon::now();
        $pegawai = Pegawai::find($data['nip']);

        return $this->ApiSpecResponses($pegawai->etika()->create($data));

    }
}
