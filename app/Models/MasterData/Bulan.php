<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Bulan extends Model
{
    protected $table = 'bulan';
    protected $fillable = [
        'kode','nama_bulan','uuid'
    ];

    public function hariKerja(){
        return $this->hasMany(HariKerja::class,'bulan');
    }
}
