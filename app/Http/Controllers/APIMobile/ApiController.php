<?php

namespace App\Http\Controllers\APIMobile;

use App\Http\Controllers\Controller;
use App\Models\MasterData\Bulan;

class ApiController extends Controller
{
    public function __construct()
    {
    }

    public function formatDate($date)
    {
        return date('Y/m/d', strtotime($date));
    }

    public function formatDate2($date)
    {
        $d = date('d', strtotime($date));
        $bulan = (int)date('m', strtotime($date));
        $m = ucfirst(Bulan::where('id', $bulan)->first()->nama_bulan);
        $y = date('Y', strtotime($date));
        return $d . " " . $m . " " . $y;
    }
}
