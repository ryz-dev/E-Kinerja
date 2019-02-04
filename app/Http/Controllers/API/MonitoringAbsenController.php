<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Kinerja;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MonitoringAbsenController extends Controller
{
    private $special_user = [2,3,4];
    private $jam_masuk = '09:00:59';
    private $jam_masuk_upacara = '07.30.59';
    private $status_hari = true;

    public function dataAbsensi(Request $request){
        
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $page = $request->has('page')?$request->input('page'):1;
        $skpd = $request->input('skpd');
        $date = Carbon::parse($request->input('d'));
        $raw_date = $request->input('d');
        $search = $request->has('search')? $request->input('search'):'';
        $user = auth('web')->user();
        $status_hari = $this->getStatusHariKerja($date);
        $pegawai = Pegawai::with(['checkinout' => function($query) use ($date){
                                    $query->select('nip','checktype','checktime','sn')
                                          ->whereDate('checktime','=',$date);
                                    // $query->select(\DB::raw('DISTINCT(checktype),nip, date(checktime),checktime'))
                                    //       ->whereDate('checktime','=',$date);
                                },
                                    'kinerja' => function($query) use ($date){
                                    $query->select('nip','jenis_kinerja')->where('approve',2)
                                    ->whereDate('tgl_mulai','<=',$date)
                                    ->whereDate('tgl_selesai','>=',$date);
                                }
                            ])->leftJoin('jabatan','pegawai.id_jabatan','=','jabatan.id')
                            ->leftJoin('golongan','jabatan.id_golongan','=','golongan.id');
        
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
            
            $pegawai->orderBy('golongan.tunjangan','desc');
            $pegawai->orderBy('pegawai.nama');
            $data_absen_pegawai = $this->parseAbsensi($pegawai,$date,$status_hari->id_status_hari);
            $sum = $this->summary($data_absen_pegawai,$raw_date,$status_hari->id_status_hari);
            $total = (int) $data_absen_pegawai->count();
            
            $data_absen_pegawai = $this->paginate($data_absen_pegawai,$this->show_limit,$page);

            return $this->ApiSpecResponses(
                [
                    'pegawai' => $data_absen_pegawai,
                    'dayBefore' => Carbon::parse($date)->addDays(-1)->format('m/d/Y'),
                    'dayAfter' => Carbon::parse($date)->addDays(1)->format('m/d/Y'),
                    'today' => Carbon::parse($date)->format('m/d/Y'),
                    'current_date' => Carbon::now()->format('m/d/Y'),
                    'dateString' => ucfirst(\App\Models\MasterData\Hari::find(date('N', strtotime($date)))->nama_hari).' , '.date('d',strtotime($date)).' '.ucfirst(\App\Models\MasterData\Bulan::find((int)date('m',strtotime($date)))->nama_bulan).' '.date('Y',strtotime($date)),
                    'jam_masuk_timestamp' => Carbon::parse($raw_date.' '.$this->jam_masuk)->toDateTimeString(),
                    'jam_masuk_upacara_timestamp' => Carbon::parse($raw_date.' '.$this->jam_masuk_upacara)->toDateTimeString(),
                    'summary' => $sum,
                    'status_hari' => $status_hari
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

    private function summary($pegawai,$date,$status_hari){
        if ($status_hari == 1 && strtotime(date('Y-m-d')) >= strtotime($date)) {
            $hadir =(int) $pegawai->where('absensi','hadir')->count();
            $cuti = (int) $pegawai->where('absensi','cuti')->count();
            $perjalanan_dinas = (int) $pegawai->where('absensi','perjalanan_dinas')->count();
            $izin = (int) $pegawai->where('absensi','izin')->count();
            $sakit = (int) $pegawai->where('absensi','sakit')->count();
            $alpha = (int) $pegawai->where('absensi','alpa')->count();
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

    private function parseAbsensi($pegawai,$date,$status_hari){
        $pegawai = $pegawai->get();

        $jam_masuk = $this->jam_masuk;
        $jam_sekarang = date('Y-m-d H:i:s');
        $tanggal_pilihan = $date;

        $data = $pegawai->map(function($item,$key) use($jam_masuk,$jam_sekarang,$tanggal_pilihan,$status_hari) {
            $data['absen_in'] = '';
            $data['absen_out'] = '';

            $raw_absensi = $item['checkinout'];
            $absensi = null;

            $tanggal_sekarang = date('Y-m-d', strtotime($jam_sekarang));
            $tanggal_pilihan_date = date('Y-m-d', strtotime($tanggal_pilihan));

            $absen_in = $raw_absensi->contains('checktype',0)?$raw_absensi->where('checktype',0)->min()->checktime:false;
            $absen_out = $raw_absensi->contains('checktype',1)?$raw_absensi->where('checktype',1)->max()->checktime:false;

            if ($status_hari == 1) {
                if (strtotime($tanggal_sekarang) > strtotime($tanggal_pilihan_date) ) {
                    if ($absen_in && $absen_out) {
                        if (strtotime($absen_in) <= strtotime($tanggal_pilihan_date.' '.$jam_masuk) ) {
                            if ((strtotime($absen_out) - strtotime($absen_in)) >= (8*3600) ) {
                                $absensi = 'hadir';
                            }
                            else{
                                $absensi = 'alpa';
                            }
                        }
                        else{
                            $absensi= 'alpa';
                        }
                    }
                    else{
                        if ($item['kinerja']->count()) {
                            $absensi = $item['kinerja']->first()->jenis_kinerja;
                        }else{
                            $absensi = 'alpa';
                        }
                    }
    
                }
                elseif (strtotime($tanggal_sekarang)==strtotime($tanggal_pilihan_date)) {
                    
    
                    if (strtotime($jam_sekarang) < strtotime($tanggal_sekarang.' '.$jam_masuk) && $raw_absensi->count() < 1 ) {
                        $absensi = 'uncount';
                    }
                    else{
                        $absensi = 'hadir';
                    }
    
                    if (strtotime($jam_sekarang) > strtotime($tanggal_sekarang.$jam_masuk) ) {
                        if ($absen_in) {
                            if ($absen_out) {
                                $absensi = 'hadir';
                            }
                            else{
                                // $absensi = date('H:i', strtotime($absen_in)).'<span> - </span>';
                                $absensi = 'hadir';
                            }
                        }
                        else{
                            if ($item['kinerja']->count()) {
                                $absensi = $item['kinerja']->first()->jenis_kinerja;
                            }else{
                                $absensi = 'alpa';
                            }
                        }
                    }
                }
                else{
                    $absensi = 'uncount';
                }
            }
            else{
                $absensi = 'libur';
            }

            $data['absen_in'] = $absen_in?date('H:i', strtotime($absen_in)):'';
            $data['absen_out'] = $absen_out?date('H:i', strtotime($absen_out)):'';
            $data['absensi'] = $absensi;
            $data['nama'] = $item->nama;
            $data['nip'] = $item->nip;
            $data['foto'] = $item->foto;

            return $data;

        });
        
        return $data;


    }

    private function paginate($items, $perPage = 6, $page = null, $options = []){
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator(array_values($items->forPage($page, $perPage)->toArray()), $items->count(), $perPage, $page, $options);
    }
}