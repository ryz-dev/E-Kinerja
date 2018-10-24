<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Agama extends Model
{
    protected $table = 'agama';
    protected $fillable = [
        'agama','uuid'
    ];

    public function pegawai(){
        return $this->hasMany(Pegawai::class,'id_agama');
    }
}
