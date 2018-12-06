<?php

namespace App\Imports;

use App\Models\MasterData\Pegawai;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;

class PegawaiImport implements ToCollection
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        $input = [];
        foreach ($collection AS $index => $row){
            if ($index == 0){
                $input = $row;
            } else {
                $data = [];
                foreach ($row AS $key => $value){
                    if ($input[$key] == 'password'){
                        $value = bcrypt($value);
                    }
                    if ($input[$key] == 'tanggal_lahir'){
                        $UNIX_DATE = ($value - 25569) * 86400;
                        $value = gmdate("Y-m-d", $UNIX_DATE);
                    }
                    if (in_array($input[$key],['userid','nip'])){
                        $value = (string)$value;
                    }
                    $data = array_merge([
                        $input[$key] => $value
                    ],$data);
                }
                $data['uuid'] = (string)Str::uuid();
                try {
                    Pegawai::create($data);
                } catch (\Exception $exception){

                }
            }
        }
    }
}
