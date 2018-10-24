<?php

namespace App\Http\Controllers\MasterData;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MasterDataController extends Controller
{
    protected $show_limit = 10;
    protected $query = null;
    public function __construct()
    {
    }
}
