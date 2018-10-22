<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hari extends Model
{
    protected $table = 'hari';
    protected $fillable = [
        'nama_hari'
    ];

    public function hariKerja(){
        return $this->hasMany(HariKerja::class,'hari');
    }
}
