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
        $bulan_seed = [
            [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'id' => 1,
                'kode' => '01',
                'nama_bulan' => 'januari'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'id' => 2,
                'kode' => '02',
                'nama_bulan' => 'februari'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'id' => 3,
                'kode' => '03',
                'nama_bulan' => 'maret'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'id' => 4,
                'kode' => '04',
                'nama_bulan' => 'april'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'id' => 5,
                'kode' => '05',
                'nama_bulan' => 'mei'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'id' => 6,
                'kode' => '06',
                'nama_bulan' => 'juni'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'id' => 7,
                'kode' => '07',
                'nama_bulan' => 'juli'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'id' => 8,
                'kode' => '08',
                'nama_bulan' => 'agustus'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'id' => 9,
                'kode' => '09',
                'nama_bulan' => 'september'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'id' => 10,
                'kode' => '10',
                'nama_bulan' => 'oktober'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'id' => 11,
                'kode' => '11',
                'nama_bulan' => 'november'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'id' => 12,
                'kode' => '12',
                'nama_bulan' => 'desember'
            ],
        ];

        try {
            DB::table('bulan')->insert($bulan_seed);
        } catch (\Exception $exception){
        }
    }
}
