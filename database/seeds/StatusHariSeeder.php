<?php

use Illuminate\Database\Seeder;

class StatusHariSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $status_hari_seeder = [
            [
                'id' => 1,
                'status_hari' => 'kerja'
            ],[
                'id' => 2,
                'status_hari' => 'libur'
            ],
        ];

        try {
            DB::table('status_hari')->insert($status_hari_seeder);
        } catch (\Exception $exception){
        }
    }
}
