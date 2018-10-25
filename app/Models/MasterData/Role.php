<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $casts = ['permissions'=> 'array'];
    protected $table = 'role';
    protected $fillable = ['uuid','nama_role','permissions'];
    
    public function Pegawai(){
        return $this->belongsToMany(Pegawai::class,'role_pegawai');
    }

    public function hasAccess(array $permissions){
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    public function hasPermission(string $permission){
        return $this->permissions[$permission]??false;
    }
}
