<?php

use Illuminate\Database\Seeder;

class SkpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Skp::class,10)->create();
    }
}
