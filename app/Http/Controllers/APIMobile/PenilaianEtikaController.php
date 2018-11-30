<?php

namespace App\Http\Controllers\APIMobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Pegawai;

class PenilaianEtikaController extends Controller
{
    public function getPegawai(){
        $user = auth('api')->user();
        $pegawai = Pegawai::wherehas('jabatan', function($query) use ($user){ 
        $query->where('id_atasan','=', $user->id_jabatan ); })
            ->with(['etika' => function($query){
                 $query->whereDate('tanggal','=',date('Y-m-d'));
            }])->get();
        
        $data = [];
        foreach($pegawai as $p){
            $data[] = [
                'uuid' => $p->uuid,
                'nip' => $p->nip,
                'foto' => $p->foto,
                'nama' => $p->nama,
                'etika' => $p->etika ? [
                    'tanggal' => $p->etika[0]->tanggal ? $p->etika[0]->tanggal : "",
                    'persentase' => $p->etika[0]->persentase ? $p->etika[0]->persentase : 0,
                    'keterangan' => $p->etika[0]->keterangan ? $p->etika[0]->keterangan : "",
                ] : [],
            ];
        }
        
        return $this->ApiSpecResponses($data);
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
