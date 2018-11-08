<?php

namespace App\Models\Absen;

use App\Models\MasterData\Pegawai;
use Illuminate\Database\Eloquent\Model;

class Kinerja extends Model
{
    protected $table = 'kinerja';
    protected $fillable = [
        'userid','tgl_mulai','tgl_selesai','jenis_kinerja','rincian_kinerja'
    ];

    public function pegawai(){
        return $this->belongsTo(Pegawai::class,'userid','userid');
    }
}
