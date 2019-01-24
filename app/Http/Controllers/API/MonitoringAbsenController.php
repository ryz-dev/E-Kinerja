<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Kinerja;
use Carbon\Carbon;

class MonitoringAbsenController extends Controller
{
    private $special_user = [2,3,4];
    private $jam_masuk = '09:00:59';
    private $jam_masuk_upacara = '07.30.59';

    public function dataAbsensi(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $skpd = $request->input('skpd');
        $date = \Carbon\Carbon::parse($request->input('d'));
        $raw_date = $request->input('d');
        $search = $request->has('search')? $request->input('search'):'';
        $user = auth('web')->user();

        $pegawai = Pegawai::with(['checkinout' => function($query) use ($date){
                                    $query->select('nip','checktime','checktype','sn')->whereDate('checktime','=',$date);
                                },
                                    'kinerja' => function($query) use ($date){
                                    $query->select('nip','jenis_kinerja')->where('approve',2)
                                    ->whereDate('tgl_mulai','<=',$date)
                                    ->whereDate('tgl_selesai','>=',$date);
                                }
                            ])->leftJoin('jabatan','pegawai.id_jabatan','=','jabatan.id');
        
        try {
            if (in_array($user->role()->pluck('id_role')->max(),$this->special_user) == false) {
                if ($user->role()->pluck('id_role')->max() != 5) {
                    $pegawai->whereHas('jabatan', function($query) use ($user){
                        $query->where('id_atasan','=',$user->id_jabatan);
                    });
                }
            }

            if ($skpd > 0) {
                $pegawai->where('id_skpd',$skpd);
            }

            if ($skpd < 0) {
                $pegawai->where('id_jabatan',3);
            }

            if ($search) {
                $pegawai->where(function($query) use ($search){
                    $query->where('nip','like','%'.$search.'%')->orWhere('nama','like','%'.$search.'%');
                });
            }

            $pegawai->groupBy('pegawai.uuid','nip','id_skpd');
            $pegawai->orderBy('jabatan.id_golongan','desc');
            $sum = $this->summary($pegawai,$raw_date);
            $total = (int) $pegawai->count();
            $pegawai = $pegawai->paginate($this->show_limit);

            return $this->ApiSpecResponses(
                [
                    'pegawai' => $pegawai,
                    'dayBefore' => Carbon::parse($date)->addDays(-1)->format('m/d/Y'),
                    'dayAfter' => Carbon::parse($date)->addDays(1)->format('m/d/Y'),
                    'today' => Carbon::parse($date)->format('m/d/Y'),
                    'dateString' => ucfirst(\App\Models\MasterData\Hari::find(date('N'))->nama_hari).' , '.date('d').' '.ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan).' '.date('Y'),
                    'jam_masuk_timestamp' => Carbon::parse($raw_date.' '.$this->jam_masuk)->toDateTimeString(),
                    'jam_masuk_upacara_timestamp' => Carbon::parse($raw_date.' '.$this->jam_masuk_upacara)->toDateTimeString(),
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
        $user = auth('web')->user();
        $search = $request->has('search')?$request->input('search'):'';

        $data = Pegawai::where('nip','<>','');

        if(in_array($user->role()->pluck('id_role')->max(),$this->special_user) == false){
            if ($user->role()->pluck('id_role')->max() != 5) {
                $data->whereHas('jabatan', function($query) use($user){
                    $query->where('id_atasan','=',$user->id_jabatan);
                });
            }
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

    private function summary($pegawai,$date){
        $data = $pegawai->get();
        $summary = $data->map(function($item, $key) use($date) {
            if (count($item['checkinout']) > 0) {
                if ($item['checkinout']->contains('checktype',0)) {
                    
                    $time = $item['checkinout']->where('checktype',0)->first()->checktime;
                    
                    if(Carbon::parse($time) >= Carbon::parse($date.' '.$this->jam_masuk)){
                        return collect(['data'=>'alpa']);
                    }
                    else{
                        return collect(['data' => 'hadir']);
                    }
                }
                else{
                    return collect(['data' => 'alpa']);
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
