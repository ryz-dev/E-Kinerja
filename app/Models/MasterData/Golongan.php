<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Golongan extends Model
{
    protected $table = 'golongan';
    protected $fillable = [
        'golongan','keterangan','tunjangan','uuid','kriteria'
    ];

    protected $appends = ['detail_uri','delete_uri','edit_uri','update_uri','tunjangan_rp'];

    public function jabatan(){
        return $this->hasMany(Jabatan::class,'id_golongan');
    }

    public function getDetailUriAttribute(){
        return route('golongan.detail',['id' => $this->id]);
    }

    public function getDeleteUriAttribute(){
        return route('api.web.master-data.golongan.delete',['id' => $this->id]);
    }

    public function getEditUriAttribute(){
        return route('golongan.edit',['id' => $this->id]);
    }

    public function getUpdateuriAttribute(){
        return route('api.web.master-data.golongan.update',['id' => $this->id]);
    }

    public function getTunjanganRpAttribute(){
        return number_format($this->tunjangan,'0',',','.');
    }
}
