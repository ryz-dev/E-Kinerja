<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Controllers\Controller;

class ReceivedController extends ApiController
{
	public function receiver(Request $req){
		return response()->json($req->all());
	}
}