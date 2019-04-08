<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class AbsenUpacara extends Model
{
    protected $fillable = ['uuid', 'SN', 'status'];
    protected $table = 'absen_upacara';

    public function setSnAttribute($value)
    {
        $this->attributes['SN'] = strtoupper($value);
    }
}
