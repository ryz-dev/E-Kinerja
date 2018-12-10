<?php

use Illuminate\Database\Seeder;

class GolonganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $golongan = [
            [
                'golongan' => 'Eselon II.a',
                'tunjangan' => 10000000,
                'kriteria' => 'Pejabat Struktural'
            ],[
                'golongan' => 'Eselon II.b',
                'tunjangan' => 5000000,
                'kriteria' => 'Pejabat Struktural'
            ],[
                'golongan' => 'Eselon III.a',
                'tunjangan' => 2310000,
                'kriteria' => 'Pejabat Struktural'
            ],[
                'golongan' => 'Eselon III.b',
                'tunjangan' => 2156000,
                'kriteria' => 'Pejabat Struktural'
            ],[
                'golongan' => 'Eselon IV.a sebagai Perencanaan',
                'tunjangan' => 1859000,
                'kriteria' => 'Pejabat Struktural'
            ],[
                'golongan' => 'Eselon IV.a',
                'tunjangan' => 1540000,
                'kriteria' => 'Pejabat Struktural'
            ],[
                'golongan' => 'Eselon IV.b',
                'tunjangan' => 1430000,
                'kriteria' => 'Pejabat Struktural'
            ],[
                'golongan' => 'Golongan IV',
                'tunjangan' => 715000,
                'kriteria' => 'Pejabat Fungsional Tertentu (Tunjangan Non Sertifikasi)'
            ],[
                'golongan' => 'Golongan III',
                'tunjangan' => 611000,
                'kriteria' => 'Pejabat Fungsional Tertentu (Tunjangan Non Sertifikasi)'
            ],[
                'golongan' => 'Golongan II',
                'tunjangan' => 517000,
                'kriteria' => 'Pejabat Fungsional Tertentu (Tunjangan Non Sertifikasi)'
            ],[
                'golongan' => 'Golongan I',
                'tunjangan' => 429000,
                'kriteria' => 'Pejabat Fungsional Tertentu (Tunjangan Non Sertifikasi)'
            ],[
                'golongan' => 'CPNS semua Golongan',
                'tunjangan' => 308000,
                'kriteria' => 'Pejabat Fungsional Tertentu (Tunjangan Non Sertifikasi)'
            ],[
                'golongan' => 'Auditor / Pengawas Madya',
                'tunjangan' => 1500000,
                'kriteria' => 'Pejabat Fungsional Tertentu (Tunjangan Non Sertifikasi)'
            ],[
                'golongan' => 'Autditor / Pengawas Muda',
                'tunjangan' => 1300000,
                'kriteria' => 'Pejabat Fungsional Tertentu (Tunjangan Non Sertifikasi)'
            ],[
                'golongan' => 'Autditor / Pengawas Pertama',
                'tunjangan' => 1000000,
                'kriteria' => 'Pejabat Fungsional Tertentu (Tunjangan Non Sertifikasi)'
            ],[
                'golongan' => 'Golongan IV',
                'tunjangan' => 858000,
                'kriteria' => 'Pejabat Fungsional Umum'
            ],[
                'golongan' => 'Golongan III',
                'tunjangan' => 715000,
                'kriteria' => 'Pejabat Fungsional Umum'
            ],[
                'golongan' => 'Golongan II',
                'tunjangan' => 616000,
                'kriteria' => 'Pejabat Fungsional Umum'
            ],[
                'golongan' => 'Golongan I',
                'tunjangan' => 550000,
                'kriteria' => 'Pejabat Fungsional Umum'
            ],[
                'golongan' => 'CPNS semua Golongan',
                'tunjangan' => 308000,
                'kriteria' => 'Pejabat Fungsional Umum'
            ],
        ];
        foreach ($golongan as $gol) {
            $gol['uuid'] = (string)\Illuminate\Support\Str::uuid();
            try {
                DB::table('golongan')->insert($gol);
            } catch (\Exception $exception) {
            }
        }
    }
}
