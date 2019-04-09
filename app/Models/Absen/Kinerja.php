<?php

namespace App\Models\Absen;

use App\Models\MasterData\Pegawai;
use App\Models\Media;
use App\Models\SkpPegawai;
use Illuminate\Database\Eloquent\Model;

class Kinerja extends Model
{
    protected $table = 'kinerja';
    protected $fillable = [
        'nip', 'tgl_mulai', 'tgl_selesai', 'jenis_kinerja', 'rincian_kinerja', 'approve'
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'nip', 'nip');
    }

    public function jabatan()
    {
        return $this->hasManyThrough('App\Models\MasterData\Jabatan', 'App\Models\MasterData\Pegawai', 'nip', 'id', 'nip', 'id_jabatan');
    }

    public function scopeTerbaru($query)
    {
        return $query->where('approve', '<>', '5')->orderBy('created_at', 'desc');
    }

    public function media()
    {
        return $this->hasMany(Media::class, 'id_kinerja');
    }

    public function skp_pegawai(){
        return $this->belongsToMany(SkpPegawai::class,'skp_kinerja','id_kinerja','id_skp_pegawai');
    }

}
