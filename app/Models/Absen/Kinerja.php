<?php

namespace App\Models\Absen;

use App\Models\MasterData\Pegawai;
use Illuminate\Database\Eloquent\Model;

class Kinerja extends Model
{
    protected $table = 'kinerja';
    protected $fillable = [
        'userid','tgl_mulai','tgl_selesai','jenis_kinerja','rincian_kinerja','approve'
    ];

    public function pegawai(){
        return $this->belongsTo(Pegawai::class,'userid','userid');
    }

    public function jabatan(){
        return $this->hasManyThrough('App\Models\MasterData\Jabatan','App\Models\MasterData\Pegawai','userid','id','userid','id_jabatan');
    }

    public function scopeTerbaru($query){
        return $query->orderBy('created_at','desc');
    }
    
}
