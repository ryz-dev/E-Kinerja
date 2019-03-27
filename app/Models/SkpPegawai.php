<?php

namespace App\Models;

use App\Models\MasterData\Pegawai;
use Illuminate\Database\Eloquent\Model;

class SkpPegawai extends Model
{
    protected $table = 'skp_pegawai';
    protected $fillable = [
        'uuid','nip_pegawai','id_skp','periode','status','tanggal_selesai','nip_update'
    ];

    public function pegawai(){
        return $this->belongsTo(Pegawai::class,'nip_pegawai','nip');
    }

    public function atasanUpdate(){
        return $this->belongsTo(Pegawai::class,'nip_update','nip');
    }

    public function skpTask(){
        return $this->belongsTo(SkpRepository::class,'id_skp');
    }

}
