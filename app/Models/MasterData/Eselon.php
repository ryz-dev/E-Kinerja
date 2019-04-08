<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Eselon extends Model
{
    protected $table = 'eselon';
    protected $fillable = [
        'eselon', 'tunjangan', 'keterangan', 'uuid'
    ];
    protected $appends = ['detail_uri', 'delete_uri', 'edit_uri', 'update_uri', 'tunjangan_rp'];

    public function jabatan()
    {
        return $this->hasMany(Jabatan::class, 'id_eselon');
    }

    public function getDetailUriAttribute()
    {
        return route('eselon.detail', ['id' => $this->id]);
    }

    public function getDeleteUriAttribute()
    {
        return route('api.web.master-data.eselon.delete', ['id' => $this->id]);
    }

    public function getEditUriAttribute()
    {
        return route('eselon.edit', ['id' => $this->id]);
    }

    public function getUpdateuriAttribute()
    {
        return route('api.web.master-data.eselon.update', ['id' => $this->id]);
    }

    public function getTunjanganRpAttribute()
    {
        return number_format($this->tunjangan, '0', ',', '.');
    }
}
