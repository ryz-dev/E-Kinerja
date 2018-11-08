<?php

namespace App\Http\Controllers\API;

use App\Models\Absen\Checkinout;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Http\Resources\Checkinout as resourceCheckinout;
use Illuminate\Support\Str;

class CheckinoutController extends ApiController
{
	public function index(){
		$checkinout = Checkinout::paginate(10);
		return new resourceCheckinout($checkinout);
	}
}