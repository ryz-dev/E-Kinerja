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
                                        $query->where('id_atasan','=',$user->id_jabatan ); })
                                    ->with(['etika' => function($query){
                                        $query->whereMonth('tanggal','=',date('m'));
                                    }]);
        if ($user->id_jabtan > 1) {
            $pegawai->where('id_skpd',$user->id_skpd);
        }
        
        return $this->ApiSpecResponses($pegawai->get());
    }

    public function storePenilaian(Request $request){
        // dd($request->input());
        $this->validate($request, [
            'nip' => 'required',
            'persentase' => 'required',
            'keterangan' => 'required',
            'mengikuti_upacara' => 'required',
            'perilaku_kerja' => 'required',
            'kegiatan_kebersamaan' => 'required'
        ]);
        $data = $request->input();
        $data['tanggal'] = \Carbon\Carbon::now();
        $pegawai = Pegawai::find($data['nip']);

        return $this->ApiSpecResponses($pegawai->etika()->create($data));

    }
}
