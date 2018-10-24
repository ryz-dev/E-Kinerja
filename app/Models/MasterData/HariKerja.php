<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class HariKerja extends Model
{
    protected $table = 'hari_kerja';
    protected $fillable = [
        'tanggal','bulan','tahun','hari','id_status_hari','uuid'
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
