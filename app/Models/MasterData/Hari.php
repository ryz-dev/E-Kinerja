<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Hari extends Model
{
    protected $table = 'hari';
    protected $fillable = [
        'nama_hari','uuid'
    ];

    public function hariKerja(){
        return $this->hasMany(HariKerja::class,'hari');
    }
}
