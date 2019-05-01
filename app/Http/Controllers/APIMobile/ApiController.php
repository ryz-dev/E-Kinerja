<?php

namespace App\Http\Controllers\APIMobile;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Bulan;

class ApiController extends Controller
{
    public function __construct()
    {
    }

    public function error400($message = 'Invalid Request'){
        return apiResponse(null,[
            'code' => 400,
            'status' => 'ERROR',
            'message' => $message
        ]);
    }

    public function error404($message = 'Not Found'){
        return apiResponse(null,[
            'code' => 404,
            'status' => 'ERROR',
            'message' => $message
        ]);
    }

    public function error403($message = 'Access Forbidden'){
        return apiResponse(null,[
            'code' => 403,
            'status' => 'ERROR',
            'message' => $message
        ]);
    }

    public function error500($message = 'Internal Server Error'){
        return apiResponse(null,[
            'code' => 500,
            'status' => 'ERROR',
            'message' => $message
        ]);
    }

    public function error422($required = [],$message = 'Missing Required Parameter'){
        return apiResponse([
            'required' => $required
        ],[
            'code' => 422,
            'status' => 'ERROR',
            'message' => $message
        ]);
    }

}
