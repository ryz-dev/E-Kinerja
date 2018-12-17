<?php

namespace App\Http\Controllers\APIMobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Pegawai;
use App\Models\MasterData\HariKerja;
use App\Models\Absen\Kinerja;
use Carbon\Carbon;

class MonitoringAbsenController extends Controller
{
    private $special_user = [2,3,4];
    private $jam_masuk = '09:00:59';
    
    public function dataAbsensi(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $skpd = $request->input('skpd');
        $date = \Carbon\Carbon::parse($request->input('d'));
        $search = $request->has('search')? $request->input('search'):'';
        $user = auth('api')->user();
        $page = $request->input('page');
        $min_date = HariKerja::whereHas('statusHari', function ($query){
            $query->where('status_hari', 'kerja');
        })->select('tanggal')->orderBy('tanggal')->first();

        $pegawai = Pegawai::with(['checkinout' => function($query) use ($date){
                $query->select('nip','checktime','checktype')->whereDate('checktime','=',$date);
            },
                'kinerja' => function($query) use ($date){
                $query->select('nip','jenis_kinerja')
                ->where('approve',2)
                ->whereDate('tgl_mulai','<=',$date)
                ->whereDate('tgl_selesai','>=',$date);
            }
        ]);
        
        try {
            if (in_array($user->role()->first()->id,$this->special_user) == false) {
                $pegawai->whereHas('jabatan', function($query) use ($user){
                    $query->where('id_atasan','=',$user->id_jabatan);
                });
            }

            if ($skpd > 0) {
                $pegawai->where('id_skpd',$skpd);
            }

            if ($search) {
                $pegawai->where(function($query) use ($search){
                    $query->where('nip','like','%'.$search.'%')->orWhere('nama','like','%'.$search.'%');
                });
            }

            $pegawai->orderBy('nama','asc');
            $sum = $this->summary($pegawai);
            $total = (int) $pegawai->count();
            
            if ($page) {
                $pegawai = $pegawai->paginate($this->show_limit);
            } else {
                $pegawai = $pegawai->get();
            }
            
            $data = [];
            foreach($pegawai as $p) {
                if (count($p->checkinout)) {
                    if ($p['checkinout']->contains('checktype',0)) {
                        $time = $p['checkinout']->where('checktype',0)->first()->checktime;
                        
                        if(Carbon::parse($time) >= Carbon::parse($this->jam_masuk)){
                            $k = (['data'=>'alpa']);
                        }
                        else{
                            $k = (['data' => 'hadir']);
                        }
                    }
                    else{
                        $k = (['data' => 'hadir']);
                    }
                }
                else{
                    if (count($p->kinerja)) {
                        $k = (['data' => $p['kinerja'][0]['jenis_kinerja']]);
                    } else {
                        $k = (['data'=>'alpa']);
                    }
                }

                $data[] = [
                    'uuid' => $p->uuid,
                    'nama' => $p->nama,
                    'nip' => $p->nip,
                    'foto' => $p->foto,
                    'checkinout' => [
                        'in' => (count($p->checkinout)) ? $p->checkinout[0]->checktime : "",
                        'out' => (count($p->checkinout) > 1) ? $p->checkinout[1]->checktime : "",
                    ],
                    'kinerja' => $k['data'],
                    'created_at' => $p->created_at,
                ];
            }

            return $this->ApiSpecResponses(
                [
                    'pegawai' => $data,
                    'min_date' => $min_date->tanggal,
                    // 'jam_masuk_timestamp' => Carbon::parse($this->jam_masuk)->toDateTimeString(),
                    'summary' => $sum
                ]
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
    }

    public function getPage(Request $request){
        $skpd = $request->input('skpd');
        $user = auth('api')->user();
        $search = $request->has('search')?$request->input('search'):'';

        $data = Pegawai::where('nip','<>','');

        if(in_array($user->role()->first()->nama_role,$this->special_user) == false){
            $data->whereHas('jabatan', function($query) use($user){
                $query->where('id_atasan','=',$user->id_jabatan);
            });
        }

        if ($skpd > 0) {
            $data = $data->where('id_skpd',$skpd);
        }

        if ($search) {
            $data->where('nip','like','%'.$search.'%')->orWhere('nama','like','%'.$search.'%');
        }
        
        $data = ceil($data->count() / $this->show_limit);

        return response()->json([ 'page'=> $data ]);
    }

    private function summary($pegawai){
        $data = $pegawai->get();
        $summary = $data->map(function($item, $key) {
            if (count($item['checkinout']) > 0) {
                if ($item['checkinout']->contains('checktype',0)) {
                    
                    $time = $item['checkinout']->where('checktype',0)->first()->checktime;
                    
                    if(Carbon::parse($time) >= Carbon::parse($this->jam_masuk)){
                        return collect(['data'=>'alpa']);
                    }
                    else{
                        return collect(['data' => 'hadir']);
                    }
                }
                else{
                    return collect(['data' => 'hadir']);
                }
            }
            else{
                if (count($item['kinerja']) > 0) {
                    return collect(['data' => $item['kinerja'][0]['jenis_kinerja']]);
                }
                return collect(['data'=>'alpa']);
            }
        });

        $hadir =(int) $summary->where('data','hadir')->count();
        $cuti = (int) $summary->where('data','cuti')->count();
        $perjalanan_dinas = (int) $summary->where('data','perjalanan_dinas')->count();
        $izin = (int) $summary->where('data','izin')->count();
        $sakit = (int) $summary->where('data','sakit')->count();
        $alpha = (int) $summary->where('data','alpa')->count();
        
        return [
            'hadir' => $hadir,
            'cuti' => $cuti,
            'perjalanan_dinas' => $perjalanan_dinas,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpha' => $alpha,
        ];
    }
}
