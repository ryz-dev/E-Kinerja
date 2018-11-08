<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Pegawai;
use Carbon\Carbon;

class MonitoringAbsenController extends Controller
{

    public function dataAbsensi(Request $request){
        $this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        $skpd = $request->input('skpd');
        $date = \Carbon\Carbon::parse($request->input('d'));
        try {
            if ($skpd == 0) {
                $pegawai = Pegawai::wherehas('jabatan', function($query){ 
                                            $query->where('id_atasan','=',2 /** TODO : Ganti dengan user yang login */ ); })
                                        ->with(['checkinout' => function($query) use ($date){
                                                $query->select('userid','checktime','checktype')
                                                      ->whereDate('checktime','=',$date)->get();
                                                }])
                                        ->orderBy('nama','asc');
            }
            else{
                $pegawai = Pegawai::where('id_skpd',$skpd)->with(['checkinout' => function($query) use ($date){
                                                    $query->select('userid','checktime','checktype')
                                                        ->whereDate('checktime','=',$date)->get();
                                                    }])
                                            ->orderBy('nama','asc');
            }
            
            $pegawai = $pegawai->paginate($this->show_limit);

            return $this->ApiSpecResponses(
                [
                    'pegawai' => $pegawai,
                    'dayBefore' => Carbon::parse($date)->addDays(-1)->format('m/d/Y'),
                    'dayAfter' => Carbon::parse($date)->addDays(1)->format('m/d/Y'),
                    'today' => Carbon::parse($date)->format('m/d/Y'),
                    'dateString' => Carbon::parse($date)->format('d M Y')
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
        if ($skpd==0) {
            $data = Pegawai::wherehas('jabatan', function($query){
                $query->where('id_atasan','=',2 /** TODO : Ganti dengan user yang login */);
            })->count();    
        }else{
            $data = Pegawai::where('id_skpd',$skpd)->count();
        }
        
        
        $data = ceil($data / $this->show_limit);

        return response()->json([ 'page'=> $data ]);
    }
}
