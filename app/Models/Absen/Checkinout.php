<?php

namespace App\Models\Absen;

use Illuminate\Database\Eloquent\Model;

class Checkinout extends Model
{
    protected $table = 'checkinout';

    protected $fillable = [
        'nip', 'checktime', 'checktype', 'verifycode', 'sensorid', 'workcode', 'sn', 'userextmft'
    ];
    protected $appends = ['absen_time','detail_uri','delete_uri','edit_uri','update_uri'];

    public function pegawai(){
        return $this->belongsTo('App\Models\MasterData\Pegawai','userid','userid');
    }

    public function getAbsenTimeAttribute(){
        return date('H:i',strtotime($this->checktime));
    }

    public function getDetailUriAttribute(){
        return route('checkinout.show',['id' => $this->id]);
    }

    public function getDeleteUriAttribute(){
        return route('api.checkinout.delete-absen',['id' => $this->id]);
    }

    public function getEditUriAttribute(){
        return route('checkinout.edit',['id' => $this->id]);
    }

    public function getUpdateuriAttribute(){
        return route('checkinout.update',['id' => $this->id]);
    }
}
