<?php

namespace App\Http\Controllers\API;

use App\Models\Absen\Checkinout;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Checkinout as resourceCheckinout;
use Illuminate\Support\Str;
use App\Models\MasterData\Pegawai;

class CheckinoutController extends ApiController
{
	public function index(){
		$checkinout = Checkinout::paginate(10);
		return new resourceCheckinout($checkinout);
	}

	public function list(Request $request){
		$this->show_limit = $request->has('s') ? $request->input('s') : $this->show_limit;
        try {
            $checkinout = Checkinout::orderBy('created_at', 'DESC');
            if ($request->has('q')) {
                $checkinout = $checkinout->where('nip', 'like', '%' . $request->input('q') . '%');
            }
            $checkinout = $checkinout->paginate($this->show_limit);
            
            return $this->ApiSpecResponses($checkinout);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
        }
	}

	public function getPage(Request $request)
    {
        if ($request->has('q')) {
            $data = Checkinout::where('nip', 'like', '%' . $request->input('q') . '%')->count();
        } else {
            $data = checkinout::count();
        }
        $data = ceil($data / $this->show_limit);
        return response()->json([
            'halaman' => $data
        ]);
    }

    public function getPegawai(Request $req){
        if ($req->has('term')) {
            $data = Pegawai::where('nip', 'like', '%' . $req->input('term') . '%')->get();
        }

        return response()->json($data);
    }
}