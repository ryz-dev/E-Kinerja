<?php

use Illuminate\Database\Seeder;

class SkpdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $skpd = [
            [
                'nama_skpd' => 'Contoh Dinas 1',
                'keterangan' => 'Keterangan Contoh Dinas 1'
            ],
            [
                'nama_skpd' => 'Contoh Dinas 2',
                'keterangan' => 'Keterangan Contoh Dinas 2'
            ],
            [
                'nama_skpd' => 'Contoh Dinas 3',
                'keterangan' => 'Keterangan Contoh Dinas 3'
            ],
            [
                'nama_skpd' => 'Contoh Dinas 4',
                'keterangan' => 'Keterangan Contoh Dinas 4'
            ],
            [
                'nama_skpd' => 'Contoh Dinas 5',
                'keterangan' => 'Keterangan Contoh Dinas 5'
            ],
            [
                'nama_skpd' => 'Contoh Dinas 6',
                'keterangan' => 'Keterangan Contoh Dinas 6'
            ],
            [
                'nama_skpd' => 'Contoh Dinas 7',
                'keterangan' => 'Keterangan Contoh Dinas 7'
            ],
            [
                'nama_skpd' => 'Contoh Dinas 8',
                'keterangan' => 'Keterangan Contoh Dinas 8'
            ],
            [
                'nama_skpd' => 'Contoh Dinas 9',
                'keterangan' => 'Keterangan Contoh Dinas 9'
            ],
        ];
        foreach ($skpd AS $s) {
            $s['uuid'] = (string)\Illuminate\Support\Str::uuid();
            \Illuminate\Support\Facades\DB::table('skpd')->insert($s);
        }
        $skpd_id = \Illuminate\Support\Facades\DB::table('skpd')->pluck('id')->toArray();
        $pegawai = \App\Models\MasterData\Pegawai::all();
        foreach ($pegawai as $item) {
            $item->id_skpd = array_random($skpd_id);
            $item->save();
        }
    }
}
