<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterData\Pegawai;

class MonitoringAbsenController extends Controller
{
    public function index(Request $request){
        // dd($request->input('d'));
        $date = $request->has('d')?\Carbon\Carbon::parse($request->input('d')):date('Y-m-d');
        $pegawai = Pegawai::wherehas('jabatan', function($query){ 
                                        $query->where('id_atasan','=',\Auth::user()->id_jabatan ); })
                                    ->with(['checkinout' => function($query) use ($date){
                                            $query->select('userid','checktime')->whereDate('checktime','=',$date);
                                        }])
                                    ->orderBy('nama','asc')
                                    ->get();

        // dd($pegawai);
        return view('layouts.users.monitoringabsen.index',compact('pegawai','date'));
    }
}
