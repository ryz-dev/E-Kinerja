<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class StatusHari extends Model
{
    protected $table = 'status_hari';
    protected $fillable = [
        'status_hari', 'uuid'
    ];

    public function hariKerja()
    {
        return $this->hasMany(HariKerja::class, 'id_status_hari');
    }
}
