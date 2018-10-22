<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatusHari extends Model
{
    protected $table = 'status_hari';
    protected $fillable = [
        'status_hari'
    ];

    public function hariKerja(){
        return $this->hasMany(HariKerja::class,'id_status_hari');
    }
}
