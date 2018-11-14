<?php

namespace App\Models\Absen;

use App\Models\MasterData\Pegawai;
use Illuminate\Database\Eloquent\Model;

class Etika extends Model
{
    protected $table = 'etika';
    protected $fillable = [
        'userid','tanggal','persentase','keterangan'
    ];

    protected $casts = ['persentase' => 'integer'];

    public function pegawai(){
        return $this->belongsTo(Pegawai::class,'userid','userid');
    }

}
