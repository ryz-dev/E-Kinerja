<?php

namespace App\Models\Absen;

use App\Models\MasterData\Pegawai;
use Illuminate\Database\Eloquent\Model;

class Kinerja extends Model
{
    protected $table = 'kinerja';
    protected $fillable = [
        'nip','tgl_mulai','tgl_selesai','jenis_kinerja','rincian_kinerja','approve'
    ];

    public function pegawai(){
        return $this->belongsTo(Pegawai::class,'nip','nip');
    }

    public function jabatan(){
        return $this->hasManyThrough('App\Models\MasterData\Jabatan','App\Models\MasterData\Pegawai','nip','id','nip','id_jabatan');
    }

    public function scopeTerbaru($query){
        return $query->orderBy('created_at','desc');
    }
    
}
