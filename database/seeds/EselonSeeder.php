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
                'id' => 1,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '1',
                'tunjangan' => 1000
            ],[
                'id' => 2,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '2',
                'tunjangan' => 2000
            ],[
                'id' => 3,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '3',
                'tunjangan' => 3000
            ],[
                'id' => 4,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '4',
                'tunjangan' => 4000
            ],[
                'id' => 5,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '5',
                'tunjangan' => 5000
            ],[
                'id' => 6,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '6',
                'tunjangan' => 6000
            ],[
                'id' => 7,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '7',
                'tunjangan' => 7000
            ],[
                'id' => 8,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '8',
                'tunjangan' => 8000
            ],[
                'id' => 9,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '9',
                'tunjangan' => 9000
            ],[
                'id' => 10,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '10',
                'tunjangan' => 10000
            ],[
                'id' => 11,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '11',
                'tunjangan' => 11000
            ],[
                'id' => 12,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '12',
                'tunjangan' => 12000
            ],[
                'id' => 13,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '13',
                'tunjangan' => 13000
            ],[
                'id' => 14,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '14',
                'tunjangan' => 14000
            ],[
                'id' => 15,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'eselon' => '15',
                'tunjangan' => 15000
            ],
        ];

        try {
            DB::table('eselon')->insert($eselon_seed);
        } catch (\Exception $exception){}
    }
}
