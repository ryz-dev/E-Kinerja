<?php

namespace App\Models;

use App\Models\Absen\Kinerja;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';
    protected $fillable = [
        'id_kinerja', 'media', 'uuid','nama_media'
    ];

    public function kinerja()
    {
        return $this->belongsTo(Kinerja::class, 'id_kinerja');
    }
}
