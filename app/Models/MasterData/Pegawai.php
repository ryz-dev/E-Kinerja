<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{   use SoftDeletes;

    protected $table = 'pegawai';
    protected $primaryKey = 'nip';
    public $incrementing = false;
    protected $fillable = [
        'nip','nama','tanggal_lahir','id_agama','id_jabatan','jns_kel','tempat_lahir','foto','uuid'
    ];

    public function agama(){
        return $this->belongsTo(Agama::class,'id_agama');
    }

    public function jabatan(){
        return $this->belongsTo(Jabatan::class,'id_jabatan');
    }

}
