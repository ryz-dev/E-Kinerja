<?php

use Illuminate\Database\Seeder;

class JabatanBackupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $jabatan_pimpinan = [
            [
                'id' => 1,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'BUPATI',
                'id_eselon' => 48,
                'id_atasan' => null
            ],[
                'id' => 2,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'WAKIL BUPATI',
                'id_eselon' => 46,
                'id_atasan' => null
            ],[
                'id' => 3,
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'SEKERTARIS DAERAH',
                'id_eselon' => 45,
                'id_atasan' => 1
            ]
        ];

        foreach ($jabatan_pimpinan as $key) {
            $id = DB::table('jabatan')->insertGetId($key);

            factory(\App\Models\MasterData\Pegawai::class,1)->create([
                'id_jabatan' => $id
            ]);
        }

        $skpd = DB::table('skpd')->get()->toArray();
        foreach ($skpd as $key => $value) {
            $kepala_dinas = [
                    'uuid' => (string)\Illuminate\Support\Str::uuid(),
                    'jabatan' => 'KEPALA DINAS '.strtoupper($value->nama_skpd),
                    'id_eselon' => 41,
                    'id_atasan' => 3 ];

            $id_kepala_dinas = DB::table('jabatan')->insertGetId($kepala_dinas);
            factory(\App\Models\MasterData\Pegawai::class,1)->create([
                'id_jabatan' => $id_kepala_dinas,
                'id_skpd' => $value->id
            ]);


            $sekertaris_dinas = [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'SEKERTARIS DINAS '.strtoupper($value->nama_skpd),
                'id_eselon' => 36,
                'id_atasan' => $id_kepala_dinas
            ];
            $id_sekertaris_dinas = DB::table('jabatan')->insertGetId($sekertaris_dinas);

            factory(\App\Models\MasterData\Pegawai::class,1)->create([
                'id_jabatan' => $id_sekertaris_dinas,
                'id_skpd' => $value->id
            ]);


            $kepala_bagian = [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'KEPALA BAGIAN '.strtoupper($value->nama_skpd),
                'id_eselon' => 34,
                'id_atasan' => $id_kepala_dinas
            ];
            $id_kepala_bagian = DB::table('jabatan')->insertGetId($kepala_bagian);
            factory(\App\Models\MasterData\Pegawai::class,1)->create([
                'id_jabatan' => $id_kepala_bagian,
                'id_skpd' => $value->id
            ]);


            $staff_bagian = [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'STAFF BAGIAN '.strtoupper($value->nama_skpd),
                'id_eselon' => 15,
                'id_atasan' => $id_kepala_bagian
            ];
            $id_staff_bagian = DB::table('jabatan')->insertGetId($staff_bagian);
            factory(\App\Models\MasterData\Pegawai::class,5)->create([
                'id_jabatan' => $id_staff_bagian,
                'id_skpd' => $value->id
            ]);


            $kepala_sub_bagian = [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'KEPALA SUB BAGIAN'.strtoupper($value->nama_skpd),
                'id_eselon' => 24,
                'id_atasan' => $id_kepala_dinas
            ];
            $id_kepala_sub_bagian = DB::table('jabatan')->insertGetId($kepala_sub_bagian);
            factory(\App\Models\MasterData\Pegawai::class,1)->create([
                'id_jabatan' => $id_kepala_sub_bagian,
                'id_skpd' => $value->id
            ]);


            $staff_sub_bagian = [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'STAFF SUB BAGIAN '.strtoupper($value->nama_skpd),
                'id_eselon' => 15,
                'id_atasan' => $id_kepala_sub_bagian
            ];
            $id_staff_sub_bagian = DB::table('jabatan')->insertGetId($staff_sub_bagian);
            factory(\App\Models\MasterData\Pegawai::class,5)->create([
                'id_jabatan' => $id_staff_sub_bagian,
                'id_skpd' => $value->id
            ]);


            $kepala_bidang = [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'KEPALA BIDANG '.strtoupper($value->nama_skpd),
                'id_eselon' => 34,
                'id_atasan' => $id_kepala_dinas
            ]; 
            $id_kepala_bidang = DB::table('jabatan')->insertGetId($kepala_bidang);
            factory(\App\Models\MasterData\Pegawai::class,1)->create([
                'id_jabatan' => $id_kepala_bidang,
                'id_skpd' => $value->id
            ]);


            $staff_bidang = [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'STAFF BIDANG '.strtoupper($value->nama_skpd),
                'id_eselon' => 15,
                'id_atasan' => $id_kepala_bidang
            ];
            $id_staff_bidang = DB::table('jabatan')->insertGetId($staff_bidang);
            factory(\App\Models\MasterData\Pegawai::class,5)->create([
                'id_jabatan' => $id_staff_bidang,
                'id_skpd' => $value->id
            ]);

            $kepala_seksi = [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'KEPALA SEKSI '.strtoupper($value->nama_skpd),
                'id_eselon' => 35,
                'id_atasan' => $id_kepala_dinas
            ];
            $id_kepala_seksi = DB::table('jabatan')->insertGetId($kepala_seksi);
            factory(\App\Models\MasterData\Pegawai::class,1)->create([
                'id_jabatan' => $id_kepala_seksi,
                'id_skpd' => $value->id
            ]);


            $staff_seksi = [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'STAFF SEKSI '.strtoupper($value->nama_skpd),
                'id_eselon' => 15,
                'id_atasan' => $id_kepala_seksi
            ];
            $id_staff_seksi = DB::table('jabatan')->insertGetId($staff_seksi);
            factory(\App\Models\MasterData\Pegawai::class,5)->create([
                'id_jabatan' => $id_staff_seksi,
                'id_skpd' => $value->id
            ]);


            $kepala_unit = [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'KEPALA UNIT '.strtoupper($value->nama_skpd),
                'id_eselon' => 34,
                'id_atasan' => $id_kepala_dinas
            ];
            $id_kepala_unit = DB::table('jabatan')->insertGetId($kepala_unit);
            factory(\App\Models\MasterData\Pegawai::class,1)->create([
                'id_jabatan' => $id_kepala_unit,
                'id_skpd' => $value->id
            ]);

            $staff_unit = [
                'uuid' => (string)\Illuminate\Support\Str::uuid(),
                'jabatan' => 'STAFF UNIT '.strtoupper($value->nama_skpd),
                'id_eselon' => 15,
                'id_atasan' => $id_kepala_unit
            ];
            $id_staff_unit = DB::table('jabatan')->insertGetId($staff_unit);
            factory(\App\Models\MasterData\Pegawai::class,5)->create([
                'id_jabatan' => $id_staff_unit,
                'id_skpd' => $value->id
            ]);

        }

        // foreach ($jabatan_seed AS $j) {
        //     try {
        //         $id = DB::table('jabatan')->insertGetId($j);
        //         if ($id == 11) {
        //             $x = 30;
        //         } else {
        //             $x = 1;
        //         }

        //         factory(\App\Models\MasterData\Pegawai::class,$x)->create([
        //             'id_jabatan' => $id
        //         ]);

        //     } catch (\Exception $exception) {
        //     }
        // }
    }
}
