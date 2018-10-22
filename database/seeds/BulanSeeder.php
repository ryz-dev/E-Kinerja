<?php

use Illuminate\Database\Seeder;

class BulanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bulan_seeder = [
            [
                'id' => 1,
                'kode' => '01',
                'nama_bulan' => 'januari'
            ],[
                'id' => 2,
                'kode' => '02',
                'nama_bulan' => 'februari'
            ],[
                'id' => 3,
                'kode' => '03',
                'nama_bulan' => 'maret'
            ],[
                'id' => 4,
                'kode' => '04',
                'nama_bulan' => 'april'
            ],[
                'id' => 5,
                'kode' => '05',
                'nama_bulan' => 'mei'
            ],[
                'id' => 6,
                'kode' => '06',
                'nama_bulan' => 'juni'
            ],[
                'id' => 7,
                'kode' => '07',
                'nama_bulan' => 'juli'
            ],[
                'id' => 8,
                'kode' => '08',
                'nama_bulan' => 'agustus'
            ],[
                'id' => 9,
                'kode' => '09',
                'nama_bulan' => 'september'
            ],[
                'id' => 10,
                'kode' => '10',
                'nama_bulan' => 'oktober'
            ],[
                'id' => 11,
                'kode' => '11',
                'nama_bulan' => 'november'
            ],[
                'id' => 12,
                'kode' => '12',
                'nama_bulan' => 'desember'
            ],
        ];

        try {
            DB::table('bulan')->insert($bulan_seeder);
        } catch (\Exception $exception){
        }
    }
}
