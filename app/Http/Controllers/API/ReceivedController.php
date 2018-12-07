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
		// $js = response()->json($req->all());
		$js = $req->all();
		// $to_array = json_decode($js, true);
		// var_dump($to_array);
		$pegawai = Pegawai::where('nip', $js['Card'])->orWhere('nip', "null_used_badge_". $js['badgenumber'])->first();
		if(empty($pegawai)){
			$peg = Pegawai::create([
				'uuid' => (string)Str::uuid(),'nip' => $js['Card'] ? $js['Card'] : "null_used_badge_". $js['badgenumber'],
				'nama' => $js['name'] ? $js['name'] : "No Name",
				'badgenumber' => $js['badgenumber']]);

			$absen = Checkinout::create(['nip' => $js['Card'] ? $js['Card'] : "null_used_badge_". $js['badgenumber'],'checktime' => $js['checkinout']['checktime'],'checktype' => $js['checkinout']['checktype'],'verifycode'=> $js['checkinout']['verifycode'],'sn' => $js['checkinout']['sn'],'sensorid'=> $js['checkinout']['sensorid']]);

			return response()->json( ['status' => 'Sukses', 'message' => 'Berhasil data di terima dan di simpan ke server', 'data' => $peg] );
		}else{
			$absen = Checkinout::create([
				'nip' => $js['Card'] ? $js['Card'] : "null_used_badge_". $js['badgenumber'],
				'checktime' => $js['checkinout']['checktime'],
				'checktype' => $js['checkinout']['checktype'],
				'verifycode'=> $js['checkinout']['verifycode'],
				'sn' => $js['checkinout']['sn'],'sensorid'=> $js['checkinout']['sensorid']
			]);

			return response()->json( ['status' => 'Sukses', 'message' => 'Berhasil data diterima dan di simpan ke server', 'data' => $pegawai] );
		}
	}
}