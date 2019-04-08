<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateAbsenPegawai extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:absen {hari?} {tanggal_mulai?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate data absen pegawai dummy';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $hari = $this->argument('hari') ?: 10;
        $tanggal_mulai = $this->argument('tanggal_mulai') ?: date('Y-m-d');

        $pegawai = DB::table('pegawai')->get();
        foreach ($pegawai AS $p) {
            for ($i = 0; $i < $hari; $i++) {
                $date = new Carbon($tanggal_mulai);
                $now_date = $date->addDays($i);
                $cek_hari_kerja = DB::table('hari_kerja')->where('tanggal', $now_date)->where('id_status_hari', 1)->first();
                if ($cek_hari_kerja) {
                    $chekin = (new Carbon($tanggal_mulai))->addDays($i)->setTime(random_int(7, 10), random_int(0, 59), random_int(0, 59));
                    $in = [
                        'nip' => $p->nip,
                        'checktime' => $chekin,
                        'checktype' => '0'
                    ];
                    $chekout = (new Carbon($tanggal_mulai))->addDays($i)->setTime(random_int(15, 19), random_int(0, 59), random_int(0, 59));
                    $out = [
                        'nip' => $p->nip,
                        'checktime' => $chekout,
                        'checktype' => '1'
                    ];
                    $kinerja = [
                        'nip' => $p->nip,
                        'tgl_mulai' => $now_date,
                        'tgl_selesai' => $now_date,
                        'jenis_kinerja' => 'hadir',
                        'rincian_kinerja' => str_random(100),
                        'approve' => random_int(0, 2),
                        'keterangan_approve' => str_random(100)
                    ];
                    try {
                        DB::table('checkinout')->insert([
                            $in, $out
                        ]);
                        DB::table('kinerja')->insert($kinerja);
                    } catch (Exception $exception) {
                    }
                }
            }
        }
    }
}
