<?php

namespace App\Models\Absen;

use Illuminate\Database\Eloquent\Model;

class Checkinout extends Model
{
    protected $table = 'checkinout';
    protected $appends = ['absen_time'];

    protected $fillable = [
        'userid','checktime', 'checktype', 'verifycode', 'sensorid', 'workcode', 'sn', 'userextmft'
    ];

    public function pegawai(){
        return $this->belongsTo('App\Models\MasterData\Pegawai','userid','userid');
    }

    public function getAbsenTimeAttribute(){
        return date('H:i',strtotime($this->checktime));
    }
}
