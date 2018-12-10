<?php

namespace App\Http\Controllers\APIMobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Etika;

class PenilaianEtikaController extends Controller
{
    public function getPegawai(Request $request){
        $user = auth('api')->user();
        $search = $request->has('search')? $request->input('search'):'';
        $pegawai = Pegawai::wherehas('jabatan', function($query) use ($user){ 
        $query->where('id_atasan','=', $user->id_jabatan ); })
            ->with(['etika' => function($query){
                 $query->select('nip' ,'tanggal', 'persentase', 'keterangan')->whereDate('tanggal','=',date('Y-m-d'));
            }]);

        if ($search) {
            $pegawai->where(function($query) use ($search){
                $query->where('nip','like','%'.$search.'%')->orWhere('nama','like','%'.$search.'%');
            });
        }
        
        $pegawai = $pegawai->get();

        $data = [];
        foreach($pegawai as $p){
            $data[] = [
                'uuid' => $p->uuid,
                'nip' => $p->nip,
                'foto' => $p->foto,
                'nama' => $p->nama,
                'etika' => count($p->etika) ? $p->etika[0]->persentase : 0,
            ];
        }
        
        return $this->ApiSpecResponses($data);
    }

    public function getEtika($nip){
        $pegawai = Pegawai::where('nip', $nip)->first();
        $etika = Etika::where('nip',$pegawai->nip)
            ->select('id', 'nip', 'persentase', 'mengikuti_upacara', 'perilaku_kerja', 'kegiatan_kebersamaan','keterangan')
            ->whereDate('tanggal','=',date('Y-m-d'))
            ->first();

        $data = [
            'uuid' => $pegawai->uuid,
            'nip' => $pegawai->nip,
            'foto' => $pegawai->foto,
            'nama' => $pegawai->nama,
            'mengikuti_upacara' => $etika ? $etika->mengikuti_upacara : 0,   
            'perilaku_kerja' => $etika ? $etika->perilaku_kerja : 0,   
            'kegiatan_kebersamaan' => $etika ? $etika->kegiatan_kebersamaan : 0,   
            'persentase' => $etika ? $etika->persentase : 0,   
            'keterangan' => $etika ? $etika->keterangan : "",   
        ];

        return $this->ApiSpecResponses($data);
    }

    public function storePenilaian(Request $request){
        $this->validate($request, [
            'nip' => 'required',
            'persentase' => 'required',
            'keterangan' => 'required',
            'mengikuti_upacara' => 'required',
            'perilaku_kerja' => 'required',
            'kegiatan_kebersamaan' => 'required'
        ]);
        $e = Etika::where('nip',$request->nip)
            ->whereMonth('tanggal','=',date('m'))
            ->first();

        if (!$e) {
            $data = $request->input();
            $data['tanggal'] = \Carbon\Carbon::now();
            $pegawai = Pegawai::find($data['nip']);
            return $this->ApiSpecResponses($pegawai->etika()->create($data));
        } else {
            return response()->json([
                'diagnostic' => [
                    'code' => '403',
                    'message' => 'sudah ada etika yg diinput'
                ]
            ]);
        }
    }
}
