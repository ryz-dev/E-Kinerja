<?php

use Illuminate\Database\Seeder;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        factory(\App\Models\MasterData\Pegawai::class,100)->create();
        $admin_master_data = [
            [
                'nip' => '987654321',
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'password' => bcrypt('qwerty12345'),
                'nama' => 'Master Data Admin',
                'tanggal_lahir' => '1990-01-01',
                'id_agama' => 1,
                'jns_kel' => 'laki-laki',
                'tempat_lahir' => 'makassar',
            ]
        ];

        try {
            DB::table('pegawai')->insert($admin_master_data);
        } catch (\Exception $e){}
    }
}
