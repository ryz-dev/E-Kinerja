<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Skpd extends Model
{
    protected $table = 'skpd';
    protected $fillable = ['nama_skpd','keterangan'];

    public function pegawai(){
        return $this->belongsTo('App\Models\MasterData\Pegawai','id','id_skpd');
    }
}
