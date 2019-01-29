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
    private $jam_masuk_upacara = '07.30.59';
    private $status_hari = true;
    
    public function dataAbsensi(Request $request){
        $this->show_limit_mobile = $request->has('s') ? $request->input('s') : $this->show_limit_mobile;
        $skpd = $request->input('skpd');
        $date = \Carbon\Carbon::parse($request->input('d'));
        $search = $request->has('search')? $request->input('search'):'';
        $user = auth('api')->user();
        $status_hari = $this->getStatusHariKerja($date);
        $raw_date = $request->input('d');
        $page = $request->input('page');
        $min_date = HariKerja::whereHas('statusHari', function ($query){
            $query->where('status_hari', 'kerja');
        })->select('tanggal')->orderBy('tanggal')->first();

        $cekLibur = HariKerja::whereDate('tanggal', $date)->whereHas('statusHari', function ($query){
            $query->where('status_hari', 'libur');
        })->select('tanggal')->orderBy('tanggal')->first();

        $pegawai = Pegawai::with(['checkinout' => function($query) use ($date){
                $query->select('nip', 'checktime', 'checktype')->whereDate('checktime', '=', $date);
            },
                'kinerja' => function($query) use ($date){
                $query->select('nip', 'jenis_kinerja')
                ->where('approve',2)
                ->whereDate('tgl_mulai', '<=', $date)
                ->whereDate('tgl_selesai', '>=', $date);
            }
        ])->leftJoin('jabatan','pegawai.id_jabatan','=','jabatan.id');
        
        try {
            if (in_array($user->role()->pluck('id_role')->max(), $this->special_user) == false) {
                if ($user->role()->pluck('id_role')->max() != 5) {
                    $pegawai->whereHas('jabatan', function($query) use ($user){
                        $query->where('id_atasan', '=', $user->id_jabatan);
                    });
                } else {
                    $pegawai->whereHas('jabatan', function($query) use ($user){
                        $query->where('id_skpd', $user->id_skpd);
                    });
                }
            }

            if ($skpd > 0) {
                $pegawai->where('id_skpd', $skpd);
            }

            if ($skpd == -1){
                $pegawai->where('id_jabatan', 3);
            }

            if ($search) {
                $pegawai->where(function($query) use ($search){
                    $query->where('nip', 'like', '%'.$search.'%')->orWhere('nama', 'like', '%'.$search.'%');
                });
            }


            $pegawai->orderBy('jabatan.id_golongan');
            $sum = $this->summary($pegawai, $raw_date, $status_hari->id_status_hari);
            $total = (int) $pegawai->count();
            
            if ($page) {
                $pegawai = $pegawai->paginate($this->show_limit_mobile);
            } else {
                $pegawai = $pegawai->get();
            }
            
            $data = [];
            foreach($pegawai as $p) {
                $apel = false;
                if (count($p->checkinout)) {
                    if ($p['checkinout']->contains('checktype', 0)) {
                        $time = $p['checkinout']->where('checktype', 0)->first()->checktime;
                        
                        if(Carbon::parse($time) >= Carbon::parse($this->jam_masuk)){
                            $k = (['data' => 'alpa']);
                        }
                        else{
                            $k = (['data' => 'hadir']);
                            if (Carbon::parse($time) <= Carbon::parse($this->jam_masuk_upacara)) {
                                $apel = true;
                            }
                        }
                    }
                    else{
                        $k = (['data' => 'alpa']);
                    }
                }
                else{
                    if ($cekLibur) {
                        $k = (['data' => 'libur']);
                    } elseif (count($p->kinerja)) {
                        $k = (['data' => $p['kinerja'][0]['jenis_kinerja']]);
                    } else {
                        $k = (['data' => 'alpa']);
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
                    'apel' => $apel,
                    'created_at' => $p->created_at
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

    private function summary($pegawai,$date,$status_hari){
        $data = $pegawai->get();

        if ($status_hari == 1 && strtotime(date('Y-m-d')) >= strtotime($date)) {
            $summary = $data->map(function($item, $key) use($date) {
                if (count($item['checkinout']) > 0) {
                    if (count($item['checkinout']) < 2 && strtotime(date('Y-m-d')) >= strtotime($date)) {
                        return collect(['data'=>'alpa']);
                    }
                    else {
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
        }
        else{
            $hadir =0;
            $cuti = 0;
            $perjalanan_dinas = 0;
            $izin = 0;
            $sakit = 0;
            $alpha = 0;
        }

        return [
            'hadir' => $hadir,
            'cuti' => $cuti,
            'perjalanan_dinas' => $perjalanan_dinas,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpha' => $alpha,
        ];
    }

    private function getStatusHariKerja($date){
        return \DB::table('hari_kerja')->where('tanggal',date('Y-m-d', strtotime($date)))->first();
    }
}
