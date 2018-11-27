<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Kinerja;
use Carbon\Carbon;

class MonitoringAbsenController extends Controller
{
    private $special_user = ['Bupati','Wakil Bupati','Sekda'];

    public function dataAbsensi(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $skpd = $request->input('skpd');
        $date = \Carbon\Carbon::parse($request->input('d'));
        $search = $request->has('search')? $request->input('search'):'';
        $user = auth('web')->user();

        $summary = Kinerja::select(\DB::raw('distinct(userid),jenis_kinerja'))
                            ->whereDate('tgl_mulai','<=',$date)
                            ->whereDate('tgl_selesai','>=',$date)
                            ->where('approve',2);

        $pegawai = Pegawai::with(['checkinout' => function($query) use ($date){
                                    $query->select('userid','checktime','checktype')->whereDate('checktime','=',$date);
                                },
                                    'kinerja' => function($query) use ($date){
                                    $query->select('userid','jenis_kinerja')->where('approve',2)
                                    ->whereDate('tgl_mulai','<=',$date)
                                    ->whereDate('tgl_selesai','>=',$date);
                                }
                            ]);

        try {
            if (in_array($user->role()->first()->nama_role,$this->special_user) == false) {
                $summary->whereHas('jabatan', function($query) use($user){
                    $query->where('id_atasan','=',$user->id_jabatan);
                });

                $pegawai->whereHas('jabatan', function($query) use ($user){
                    $query->where('id_atasan','=',$user->id_jabatan);
                });
            }

            if ($skpd > 0) {
                $pegawai->where('id_skpd',$skpd);

                $summary->whereHas('pegawai', function($query) use ($skpd){
                    $query->where('id_skpd','=',$skpd);
                });
            }

            if ($search) {
                $pegawai->where(function($query) use ($search){
                    $query->where('nip','like','%'.$search.'%')->orWhere('nama','like','%'.$search.'%');
                });

                $summary->whereHas('pegawai', function($query) use($search){
                    $query->where('nip','like','%'.$search.'%')->orWhere('nama','like','%'.$search.'%');
                });
            }
            $pegawai->orderBy('nama','asc');
            $total = (int) $pegawai->count();
            $pegawai = $pegawai->paginate($this->show_limit);
            $res = $summary->get();

            return $this->ApiSpecResponses(
                [
                    'pegawai' => $pegawai,
                    'dayBefore' => Carbon::parse($date)->addDays(-1)->format('m/d/Y'),
                    'dayAfter' => Carbon::parse($date)->addDays(1)->format('m/d/Y'),
                    'today' => Carbon::parse($date)->format('m/d/Y'),
                    'dateString' => Carbon::parse($date)->format('d F Y'),
                    'summary' => $this->summary($total,$res)
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

    private function summary($pegawai,$kinerja){
        $hadir = (int) $kinerja->where('jenis_kinerja','hadir')->count();
        $cuti = (int) $kinerja->where('jenis_kinerja','cuti')->count();
        $perjalanan_dinas = (int) $kinerja->where('jenis_kinerja','perjalanan_dinas')->count();
        $izin = (int) $kinerja->where('jenis_kinerja','izin')->count();
        $sakit = (int) $kinerja->where('jenis_kinerja','sakit')->count();
        $alpha = $pegawai - ($hadir + $cuti + $perjalanan_dinas + $izin + $sakit);

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
