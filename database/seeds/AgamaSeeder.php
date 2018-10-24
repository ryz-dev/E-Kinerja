<?php

use Illuminate\Database\Seeder;

class AgamaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $agama_seed = [
            [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'agama' => 'islam'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'agama' => 'kristen'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'agama' => 'protestan'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'agama' => 'hindu'
            ],[
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'agama' => 'budha'
            ],
        ];

        try {
            DB::table('agama')->insert($agama_seed);
        } catch (\Exception $exception){

        }
    }
}
