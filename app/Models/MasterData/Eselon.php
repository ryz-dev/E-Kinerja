<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Eselon extends Model
{
    protected $table = 'eselon';
    protected $fillable = [
        'eselon','tunjangan','keterangan','uuid'
    ];

    public function jabatan(){
        return $this->hasMany(Jabatan::class,'id_eselon');
    }
}
