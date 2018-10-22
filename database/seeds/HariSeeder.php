<?php

use Illuminate\Database\Seeder;

class HariSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hari_seeder = [
          [
              'id' => 1,
              'nama_hari' => 'senin'
          ],[
              'id' => 2,
              'nama_hari' => 'selasa'
          ],[
              'id' => 3,
              'nama_hari' => 'rabu'
          ],[
              'id' => 4,
              'nama_hari' => 'kamis'
          ],[
              'id' => 5,
              'nama_hari' => 'jumat'
          ],[
              'id' => 6,
              'nama_hari' => 'sabtu'
          ],[
              'id' => 7,
              'nama_hari' => 'minggu'
          ],
        ];

        try {
            DB::table('hari')->insert($hari_seeder);
        } catch (\Exception $exception){
        }
    }
}
