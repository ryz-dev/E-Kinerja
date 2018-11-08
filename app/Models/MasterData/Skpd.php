<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Skpd extends Model
{
    protected $table = 'skpd';
    protected $fillable = ['nama_skpd','keterangan','uuid'];
    protected $appends = ['detail_uri','delete_uri','edit_uri','update_uri'];

    public function pegawai(){
        return $this->hasMany('App\Models\MasterData\Pegawai','id_skpd');
    }

    public function getDetailUriAttribute(){
        return route('skpd.detail',['id' => $this->id]);
    }

    public function getDeleteUriAttribute(){
        return route('api.web.master-data.skpd.delete',['id' => $this->uuid]);
    }

    public function getEditUriAttribute(){
        return route('skpd.edit',['id' => $this->id]);
    }

    public function getUpdateuriAttribute(){
        return route('api.web.master-data.skpd.update',['id' => $this->uuid]);
    }
}
