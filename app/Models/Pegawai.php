<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{   use SoftDeletes;

    protected $table = 'pegawai';
    protected $fillable = [
        'id_fp','nama','tanggal_lahir','unit_kerja','status_upload','agama','kode_jabatan','jns_kel','tempat_lahir'
    ];
}
