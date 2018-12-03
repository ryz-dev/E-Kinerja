<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Checkinout;
use Illuminate\Support\Str;

class ReceivedController extends ApiController
{
	public function receiver(Request $req){
		$js = response()->json($req->all());
		$to_array = json_decode($js, true);
		$pegawai = Pegawai::where('nip', $to_array['Card'])->first();
		if(empty($pegawai)){
			$peg = Pegawai::create([
				'uuid' => (string)Str::uuid(),'nip' => $to_array['Card'],
				'nama' => $to_array['name'] ? $to_array['name'] : "No Name",
				'badgenumber' => $to_array['badgenumber']]);

			$absen = Checkinout::create(['userid' => $to_array['checkinout']['userid'],'nip' => $to_array['Card'] ? $to_array['Card'] : "null_used_badge_". $to_array['badgenumber'],'checktime' => $to_array['checktime'],'checktype' => $to_array['checktype'],'verifycode'=> $to_array['verifycode'],'sn' => $to_array['sn'],'sensorid'=> $to_array['sensorid']]);

			// return response()->json( ['status' => 'Sukses', 'message' => 'Berhasil mengirim data ke server'] );
		}else{
			$absen = Checkinout::create([
				'userid' => $to_array['checkinout']['userid'],
				'nip' => $to_array['Card'],'checktime' => $to_array['checktime'],
				'checktype' => $to_array['checktype'],
				'verifycode'=> $to_array['verifycode'],
				'sn' => $to_array['sn'],'sensorid'=> $to_array['sensorid']
			]);

			return response()->json( ['status' => 'Sukses', 'message' => 'Berhasil mengirim data ke server'] );
		}
	}
}