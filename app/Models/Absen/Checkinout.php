<?php

namespace App\Models\Absen;

use Illuminate\Database\Eloquent\Model;

class Checkinout extends Model
{
    protected $table = 'checkinout';

    protected $fillable = [
        'userid','checktime', 'checktype', 'verifycode', 'sensorid', 'workcode', 'sn', 'userextmft'
    ];

    public function pegawai(){
        return $this->belongsTo('App\Models\MasterData\Pegawai','userid','userid');
    }
}
