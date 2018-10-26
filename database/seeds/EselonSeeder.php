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
        $eselon_seed = [
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
        ];

        try {
            DB::table('eselon')->insert($eselon_seed);
        } catch (\Exception $exception){}
    }
}
