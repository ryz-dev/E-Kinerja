<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skp extends Model
{
    protected $table = 'skp';
    protected $fillable = [
        'task','uuid'
    ];

    public function skpPegawai(){
        return $this->hasMany(SkpPegawai::class,'id_skp');
    }
}
