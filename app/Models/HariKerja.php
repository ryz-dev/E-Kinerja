<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HariKerja extends Model
{
    protected $table = 'hari_kerja';
    protected $fillable = [
        'tanggal','bulan','tahun','hari','id_status_hari'
    ];

    public function statusHari(){
        return $this->belongsTo(StatusHari::class,'id_status_hari');
    }

    public function Bulan(){
        return $this->belongsTo(Bulan::class,'bulan');
    }

    public function Hari(){
        return $this->belongsTo(Hari::class,'hari');
    }
}
