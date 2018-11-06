<?php

use Illuminate\Database\Seeder;
use Faker\Generator as Faker;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Checkinout;
use Carbon\Carbon;

class CheckinoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(Faker $faker)
    {
        $pegawais = Pegawai::all();
        
        foreach ($pegawais as $pegawai) {

            $absendatang = new Checkinout(
                ['checktime' => $faker->dateTimeBetween(Carbon::now()->toDateString().' 07:00:00', Carbon::now()->toDateString().' 10:00:00')]
            );
            $absenpulang = new Checkinout(
                ['checktime' => $faker->dateTimeBetween(Carbon::now()->toDateString().' 17:00:00', Carbon::now()->toDateString().' 18:00:00')]
            );
            
            $pegawai->checkinout()->save($absendatang);
            $pegawai->checkinout()->save($absenpulang);
        }
        
    }
}
