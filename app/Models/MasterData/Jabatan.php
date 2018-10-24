<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $fillable = [
        'jabatan','id_eselon','id_atasan','keterangan','uuid'
    ];

    public function eselon(){
        return $this->belongsTo(Eselon::class,'id_eselon');
    }

    public function atasan(){
        return $this->belongsTo(Jabatan::class,'id_atasan');
    }

    public function bawahan(){
        return $this->hasMany(Jabatan::class,'id_atasan');
    }

    public function pegawai(){
        return $this->hasMany(Pegawai::class,'id_jabatan');
    }

    public function pegawai_bawahan(){
        return $this->hasManyThrough(Pegawai::class,Jabatan::class,'id_atasan','id_jabatan');
    }
}
