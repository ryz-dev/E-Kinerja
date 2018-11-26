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
                'nama_role' => 'Super Admin',
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'permissions' => json_encode([
                    'master-data'=> true
                ])
            ],
            [
                'nama_role' => 'Bupati',
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'permissions' => json_encode([
                    'monitoring-absen'=> true,
                    'penilaian-kinerja' => true,
                    'penilaian-etika' => true,
                ])
            ],
            [
                'nama_role' => 'Wakil Bupati',
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'permissions' => json_encode([
                    'monitoring-absen'=> true,
                ])
            ],
            [
                'nama_role' => 'Sekertaris Daerah',
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'permissions' => json_encode([
                    'monitoring-absen'=> true,
                    'input-kinerja' => true,
                    'penilaian-kinerja' => true,
                    'penilaian-etika' => true,
                    'tunjangan-kinerja' => true
                ])
            ],
            [
                'nama_role' => 'Kepala Dinas',
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'permissions' => json_encode([
                    'monitoring-absen'=> true,
                    'input-kinerja' => true,
                    'penilaian-kinerja' => true,
                    'penilaian-etika' => true,
                    'tunjangan-kinerja' => true
                ])
            ],
            [
                'nama_role' => 'Atasan',
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'permissions' => json_encode([
                    'monitoring-absen'=> true,
                    'input-kinerja' => true,
                    'penilaian-kinerja' => true,
                    'penilaian-etika' => true,
                    'tunjangan-kinerja' => true
                ])
            ],[
                'nama_role' => 'Staff',
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'permissions' => json_encode([
                    'input-kinerja' => true,
                    'tunjangan-kinerja' => true
                ])
            ]

        ];

        try {
            DB::table('role')->insert($role_seed);
        } catch(\Exception $exception){
        }
    }
}
