<?php

namespace App\Models\Absen;

use Illuminate\Database\Eloquent\Model;

class Kepatuhan extends Model
{
    protected $table = 'kepatuhan';

    protected $fillable = [
        'uuid', 'nip', 'periode', 'lkpn', 'bmd', 'tptgr', 'status'
    ];


    public function pegawai(){
        return $this->belongsTo('App\Models\MasterData\Pegawai', 'nip', 'nip');
    }
}
