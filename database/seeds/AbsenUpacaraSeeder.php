<?php

use Illuminate\Database\Seeder;

class AbsenUpacaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data_mesin_absen  = [
            [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'SN' => 'SN2012201801',
            ],
            [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'SN' => 'SN2012201802',
            ],
            [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'SN' => 'SN2012201803',
            ],
            [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'SN' => 'SN2012201804',
            ],
        ];

        DB::table('absen_upacara')->insert($data_mesin_absen);
    }
}
