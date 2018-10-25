<?php

use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role_seed = [
            [
                'nama_role' => 'Bos',
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'permissions' => json_encode([
                    'master-data'=> true,
                    'kinerja'=> true,
                    'etika'=> true,
                    'penilaiaan-kinerja'=> true,
                    'monitoring-absen'=> true
                ])
            ],
            [
                'nama_role' => 'Wakil Bos',
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'permissions' => json_encode([
                    'master-data'=> true,
                    'kinerja'=> true,
                    'etika'=> true,
                    'monitoring-absen'=> true
                ])
            ],
            [
                'nama_role' => 'Staff Wakil Bos',
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'permissions' => json_encode([
                    'monitoring-absen'=> true,
                ])
            ]            
        ];

        try {
            DB::table('role')->insert($role_seed);
        } catch(\Exception $exception){
        }
    }
}
