<?php

namespace App\Http\Controllers\API;

use App\Models\Absen\Checkinout;
use App\Models\MasterData\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use function Faker\Provider\pt_BR\check_digit;
use App\Http\Resources\Checkinout as AppCheckinout;

class ReceivedController extends ApiController
{
    public function receiver(Request $req)
    {
        // $js = response()->json($req->all());
        $js = $req->all();
        // $to_array = json_decode($js, true);
        // var_dump($to_array);
        $nip = empty($js['Card'])?"null_used_badge_" . $js['badgenumber']:$js['Card'];

        $pegawai = Pegawai::where('nip', $nip)->first();
        if (empty($pegawai)) {
            $peg = Pegawai::create([
                'uuid' => (string)Str::uuid(), 'nip' => $js['Card'] ? $js['Card'] : "null_used_badge_" . $js['badgenumber'],
                'nama' => $js['name'] ? $js['name'] : "No Name",
                'password' => Hash::make($js['Card']),
                'badgenumber' => $js['badgenumber']]);

            $absen = Checkinout::create(['nip' => $js['Card'] ? $js['Card'] : "null_used_badge_" . $js['badgenumber'], 'checktime' => $js['checkinout']['checktime'], 'checktype' => $js['checkinout']['checktype'], 'verifycode' => $js['checkinout']['verifycode'], 'sn' => $js['checkinout']['sn'], 'sensorid' => $js['checkinout']['sensorid']]);

            return response()->json(['status' => 'Sukses', 'message' => 'Berhasil data di terima dan di simpan ke server', 'data' => $peg]);
        } else {
            // cek data
            $nip = empty($js['Card'])?"null_used_badge_" . $js['badgenumber']:$js['Card'];
            $absensi = Checkinout::where('nip',$nip )->where('checktime', $js['checkinout']['checktime'])->first();
            if (empty($absensi)) {
                $absen = Checkinout::create([
                    'nip' => $nip,
                    'checktime' => $js['checkinout']['checktime'],
                    'checktype' => $js['checkinout']['checktype'],
                    'verifycode' => $js['checkinout']['verifycode'],
                    'sn' => $js['checkinout']['sn'], 'sensorid' => $js['checkinout']['sensorid']
                ]);
                return response()->json(['status' => 'Sukses', 'message' => 'Berhasil data diterima dan di simpan ke server', 'data' => $pegawai]);
            }

        }
    }
}
