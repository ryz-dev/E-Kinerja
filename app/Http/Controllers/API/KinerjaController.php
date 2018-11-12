<?php

namespace App\Http\Controllers\API;

use App\Models\Absen\Checkinout;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use Carbon\Carbon;
use http\Env\Response;
use Illuminate\Http\Request;

class KinerjaController extends ApiController
{
    public function inputKinerja(Request $request){
        $input = $request->input();
        /*todo : ubah jadi dynamic user id menurut user yg login*/
        $input['userid'] = Pegawai::first()->userid;
        if (in_array($input['jenis_kinerja'],['hadir','sakit'])){
            $input['tgl_mulai'] = Carbon::now();
            $input['tgl_selesai'] = Carbon::now();
        } else {
            $tgl_mulai = explode('/',$input['tgl_mulai']);
            $tgl_selesai = explode('/',$input['tgl_selesai']);

            $input['tgl_mulai'] = $tgl_mulai[2].'-'.$tgl_mulai[0].'-'.$tgl_mulai[1];
            $input['tgl_selesai'] = $tgl_selesai[2].'-'.$tgl_selesai[0].'-'.$tgl_selesai[1];
            if (strtotime($input['tgl_mulai']) > strtotime($input['tgl_selesai'])){
                return response()->json([
                    'diagnostic' => [
                        'code' => '403',
                        'message' => 'gagal menambah kinerja, tanggal berakhir lebih kecil dari tanggal mulai'
                    ]
                ]);
            }
        }
        $cek_kinerja = Kinerja::where('userid',$input['userid'])->whereDate('tgl_mulai',$input['tgl_mulai'])->first();
        if (!$cek_kinerja){
            if ($input['jenis_kinerja'] == 'hadir'){
                $cek_hari_kerja = HariKerja::whereDate('tanggal',date('Y-m-d'))->first();
                if ($cek_hari_kerja){
                    $cek_hadir_kerja = Checkinout::whereDate('checktime',date('Y-m-d'))->where('checktype','i')->where('userid',$input['userid'])->first();
                    if ($cek_hadir_kerja){
                        $kinerja = Kinerja::create($input);
                        return $this->ApiSpecResponses($kinerja);
                    } else {
                        return response()->json([
                                'diagnostic' => [
                                    'code' => '403',
                                    'message' => 'gagal menambah kinerja, tidak tercata hadir pada absen'
                                ]
                            ]);
                    }
                }
                return response()->json([
                    'diagnostic' => [
                        'code' => '403',
                        'message' => 'gagal menambah kinerja, bukan hari kerja'
                    ]
                ]);
            } else {
                $kinerja = Kinerja::create($input);
                return $this->ApiSpecResponses($kinerja);
            }
        } else {
            return response()->json([
                'diagnostic' => [
                    'code' => '403',
                    'message' => 'gagal menambahkan kinerja, sdh ada kinerja pada hari yang sama untuk user ini'
                ]
            ]);
        }
    }
}
