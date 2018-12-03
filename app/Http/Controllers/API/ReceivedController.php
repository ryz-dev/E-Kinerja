<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Checkinout;
use Illuminate\Support\Str;

class ReceivedController extends ApiController
{
	public function receiver(Request $req){
		$var = '{"userId":3,"badgenumber":"000000003","defaultdeptid":0,"name":"Dirham","Card":"0","Privilege":0,"AccGroup":0,"SECURITYFLAGAS":0,"DelTag":0,"RegisterOT":0,"AutoSchPlan":0,"MinAutoSchInterval":0,"Image_id":0,"checkinout":{"userid":3,"checktime":"2018-12-03 04:29:05","checktype":"0","verifycode":0,"sn":"4225553031055","sensorid":"2"}}'
		
		$to_array = json_decode($var, true);
		// $to_array = json_decode($req->all(), true);
		$pegawai = Pegawai::where('nip', $to_array['Card'])->first();
		$checkinout = Checkinout::where('nip', $to_array['Card'])->first();
		if(empty($pegawai)){
			$peg = Pegawai::create([
				'uuid' => (string)Str::uuid(),'nip' => $to_array['Card'], 
				'nama' => $to_array['name'] ? $to_array['name'] : "No Name",
				'badgenumber' => $to_array['badgenumber']
			]);

			$check = $to_array['checkinout']['userid'] = $pe

			Checkinout::
		}else{

		}

		return response()->json($req->all());
	}
}