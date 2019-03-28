<?php

namespace App\Models\MasterData;

use App\Models\MasterData\Golongan;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    protected $table = 'jabatan';
    protected $fillable = [
        'jabatan','id_golongan','id_atasan','keterangan','uuid'
    ];
    protected $appends = ['detail_uri','delete_uri','edit_uri','update_uri'];

    public function golongan(){
        return $this->belongsTo(Golongan::class,'id_golongan');
    }

    public function atasan(){
        return $this->belongsTo(Jabatan::class,'id_atasan');
    }

    public function bawahan(){
        return $this->hasMany(Jabatan::class,'id_atasan');
    }

    public function pegawai(){
        return $this->hasMany(Pegawai::class,'id_jabatan');
    }

    public function pegawai_bawahan(){
        return $this->hasManyThrough(Pegawai::class,Jabatan::class,'id_atasan','id_jabatan');
    }

    public function getDetailUriAttribute(){
        return route('jabatan.detail',['id' => $this->id]);
    }

    public function getDeleteUriAttribute(){
        return route('api.web.master-data.jabatan.delete',['id' => $this->id]);
    }

    public function getEditUriAttribute(){
        return route('jabatan.edit',['id' => $this->id]);
    }

    public function getUpdateuriAttribute(){
        return route('api.web.master-data.jabatan.update',['id' => $this->id]);
    }
}
