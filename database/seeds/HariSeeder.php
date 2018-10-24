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
        $hari_seed = [
          [
              'uuid' => (string)\Illuminate\Support\Str::uuid(),
              'id' => 1,
              'nama_hari' => 'senin'
          ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
              'id' => 2,
              'nama_hari' => 'selasa'
          ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
              'id' => 3,
              'nama_hari' => 'rabu'
          ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
              'id' => 4,
              'nama_hari' => 'kamis'
          ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
              'id' => 5,
              'nama_hari' => 'jumat'
          ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
              'id' => 6,
              'nama_hari' => 'sabtu'
          ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
              'id' => 7,
              'nama_hari' => 'minggu'
          ],
        ];

        try {
            DB::table('hari')->insert($hari_seed);
        } catch (\Exception $exception){
        }
    }
}
