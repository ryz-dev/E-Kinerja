<?php

use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jabatan_seed = [
            [
                'id' => 1,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'bos',
                'id_eselon' => 1,
                'id_atasan' => null
            ],[
                'id' => 2,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'wakil bos1',
                'id_eselon' => 2,
                'id_atasan' => 1
            ],[
                'id' => 3,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'wakil bos2',
                'id_eselon' => 2,
                'id_atasan' => 1
            ],[
                'id' => 4,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'wakil bos3',
                'id_eselon' => 2,
                'id_atasan' => 1
            ],[
                'id' => 5,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'staff wakil bos1',
                'id_eselon' => 3,
                'id_atasan' => 2
            ],[
                'id' => 6,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'staff wakil bos2',
                'id_eselon' => 3,
                'id_atasan' => 3
            ],[
                'id' => 7,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'staff wakil bos3',
                'id_eselon' => 3,
                'id_atasan' => 4
            ],
        ];
        foreach ($jabatan_seed AS $j) {
            try {
                $id = DB::table('jabatan')->insertGetId($j);
                if (in_array($id,[1,2,3,4])) {
                    $x = 1;
                } else {
                    $x = 40;
                }
                factory(\App\Models\MasterData\Pegawai::class,$x)->create([
                    'id_jabatan' => $id
                ]);
            } catch (\Exception $exception) {
            }
        }
    }
}
