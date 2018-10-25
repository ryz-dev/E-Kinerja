<?php

namespace App\Models\MasterData;


use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pegawai extends Authenticatable
{   
    use SoftDeletes;
    use Notifiable;

    protected $table = 'pegawai';
    protected $primaryKey = 'nip';
    public $incrementing = false;
    protected $fillable = [
        'nip','nama','tanggal_lahir','id_agama','id_jabatan','jns_kel','tempat_lahir','foto','uuid'
    ];
    protected $hidden = ['password'];

    public function agama(){
        return $this->belongsTo(Agama::class,'id_agama');
    }

    public function jabatan(){
        return $this->belongsTo(Jabatan::class,'id_jabatan');
    }

    public function role(){
        return$this->belongsToMany(Role::class,'role_pegawai','nip_pegawai','id_role');
    }

    public function hasAccess(array $permissions){
        foreach ($this->role as $role) {
            if ($role->hasAccess($permissions)) {
                return true;
            }
        }
    }

}
