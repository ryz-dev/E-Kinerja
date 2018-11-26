<?php

namespace App\Models\MasterData;

use Illuminate\Database\Eloquent\Model;

class FormulaVariable extends Model
{
    protected $table = 'formula_variable';
    protected $fillable = [
        'variable','persentase_nilai','keterangan_variable'
    ];
}
