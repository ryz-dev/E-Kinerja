<?php

namespace App\Http\Controllers\APIMobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiController extends Controller
{
    public function __construct()
    {
    }

    public function formatDate($date){
        return date('d/m/Y',strtotime($date));
    }
}
