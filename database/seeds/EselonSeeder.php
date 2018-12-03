<?php

use Illuminate\Database\Seeder;

class EselonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*berdasarkan :
            PERATURAN PRESIDEN REPUBLIK INDONESIA
            NOMOR 26 TAHUN 2007
            TENTANG TUNJANGAN JABATAN STRUKTURAL
        */
        /*$eselon_seed = [
            [
                'id' => 1,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => 'IA',
                'tunjangan' => 5500000
            ],[
                'id' => 2,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => 'IB',
                'tunjangan' => 4375000
            ],[
                'id' => 3,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => 'IIA',
                'tunjangan' => 3250000
            ],[
                'id' => 4,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => 'IIB',
                'tunjangan' => 2025000
            ],[
                'id' => 5,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => 'IIIA',
                'tunjangan' => 1260000
            ],[
                'id' => 6,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => 'IIIB',
                'tunjangan' => 980000
            ],[
                'id' => 7,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => 'IVA',
                'tunjangan' => 540000
            ],[
                'id' => 8,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => 'IVB',
                'tunjangan' => 490000
            ],[
                'id' => 9,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => 'VA',
                'tunjangan' => 360000
            ],
        ];*/
        $eselon_seed = [
            [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '1',
                'tunjangan' => 190
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '2',
                'tunjangan' => 0
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '3',
                'tunjangan' => 340
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '4',
                'tunjangan' => 440
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '5a',
                'tunjangan' => 520
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '5b',
                'tunjangan' => 490
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '6a',
                'tunjangan' => 770
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '6b',
                'tunjangan' => 755
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '6c',
                'tunjangan' => 740
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '6d',
                'tunjangan' => 690
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '7a',
                'tunjangan' => 1035
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '7b',
                'tunjangan' => 1020
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '7c',
                'tunjangan' => 1005
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '7d',
                'tunjangan' => 890
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '8a',
                'tunjangan' => 1320
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '8b',
                'tunjangan' => 1310
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '8c',
                'tunjangan' => 1295
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '8d',
                'tunjangan' => 1280
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '8e',
                'tunjangan' => 1260
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '8f',
                'tunjangan' => 1245
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '8g',
                'tunjangan' => 1230
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '8h',
                'tunjangan' => 1190
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '8i',
                'tunjangan' => 1165
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '9a',
                'tunjangan' => 1960
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '9b',
                'tunjangan' => 1570
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '9c',
                'tunjangan' => 1555
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '9d',
                'tunjangan' => 1540
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '9e',
                'tunjangan' => 1535
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '9f',
                'tunjangan' => 1455
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '9g',
                'tunjangan' => 1430
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '9h',
                'tunjangan' => 1385
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '9i',
                'tunjangan' => 1370
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '9j',
                'tunjangan' => 1355
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '10a',
                'tunjangan' => 1720
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '10b',
                'tunjangan' => 1670
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '11a',
                'tunjangan' => 2090
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '11b',
                'tunjangan' => 2045
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '11c',
                'tunjangan' => 1990
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '11d',
                'tunjangan' => 1930
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '11e',
                'tunjangan' => 1960
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '12a',
                'tunjangan' => 2315
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '12b',
                'tunjangan' => 2225
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '12c',
                'tunjangan' => 2140
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '12d',
                'tunjangan' => 2115
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '13',
                'tunjangan' => 2715
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '14a',
                'tunjangan' => 3030
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '14b',
                'tunjangan' => 2865
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '15',
                'tunjangan' => 3555
            ],
        ];

        try {
            DB::table('eselon')->insert($eselon_seed);
        } catch (\Exception $exception){}
    }
}
