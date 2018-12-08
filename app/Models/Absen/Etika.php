<?php

namespace App\Models\Absen;

use App\Models\MasterData\Pegawai;
use Illuminate\Database\Eloquent\Model;

class Etika extends Model
{
    protected $table = 'etika';
    protected $fillable = [
        'userid','tanggal','persentase','mengikuti_upacara','perilaku_kerja','kegiatan_kebersamaan','keterangan'
    ];

    protected $casts = ['persentase' => 'integer'];

    public function pegawai(){
        return $this->belongsTo(Pegawai::class,'nip','nip');
    }

}
