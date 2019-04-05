<?php

namespace App\Models\MasterData;

use App\Models\Absen\Kinerja;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class Pegawai extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;
    use HasApiTokens;

    protected $table = 'pegawai';
    protected $primaryKey = 'nip';
    public $incrementing = false;
    protected $fillable = [
        'status_upacara','nip','nama','tanggal_lahir','id_agama','id_jabatan','jns_kel','tempat_lahir','foto','uuid','id_skpd','password','userid'
    ];
    protected $appends = ['detail_uri','delete_uri','edit_uri','update_uri','update_password_uri'];
    protected $hidden = ['password'];

    public function agama()
    {
        return $this->belongsTo(Agama::class, 'id_agama');
    }

    public function jabatan()
    {
        return $this->belongsTo(Jabatan::class, 'id_jabatan');
    }

    public function role()
    {
        return $this->belongsToMany(Role::class, 'role_pegawai', 'nip_pegawai', 'id_role');
    }

    public function checkinout()
    {
        return $this->hasMany('App\Models\Absen\Checkinout', 'nip', 'nip');
    }

    public function kinerja()
    {
        return $this->hasMany(Kinerja::class, 'nip', 'nip');
    }

    public function hasAccess(array $permissions)
    {
        foreach ($this->role as $role) {
            if ($role->hasAccess($permissions)) {
                return true;
            }
        }
    }

    public function skpd()
    {
        return $this->belongsTo('App\Models\MasterData\Skpd', 'id_skpd', 'id');
    }

    public function getDetailUriAttribute()
    {
        return route('pegawai.detail', ['id' => $this->nip]);
    }

    public function getDeleteUriAttribute()
    {
        return route('api.web.master-data.pegawai.delete', ['id' => $this->nip]);
    }

    public function getEditUriAttribute()
    {
        return route('pegawai.edit', ['id' => $this->nip]);
    }

    public function getUpdateuriAttribute()
    {
        return route('api.web.master-data.pegawai.update', ['id' => $this->nip]);
    }

    public function findForPassport($username)
    {
        return self::where('nip', $username)->first(); // change column name whatever you use in credentials
    }

    public function getUpdatePasswordUriAttribute()
    {
        return route('api.web.master-data.pegawai.update-password');
    }
}
