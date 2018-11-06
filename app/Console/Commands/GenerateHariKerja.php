<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateHariKerja extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:hari-kerja {hari?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Hari Kerja Default';

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
        $hari = $this->argument('hari') ?: 100;
        for ($i = 0; $i < $hari; $i++) {
            $date = new Carbon(date('Y-m-d'));
            $now_date = $date->addDays($i);
            $input['uuid'] = (string)Str::uuid();
            $input['tanggal'] = $now_date;
            $input['bulan'] = (int)date('m',strtotime($now_date));
            $input['tahun'] = date('Y',strtotime($now_date));
            $input['hari'] = date('N',strtotime($now_date));
            if ($input['hari'] == 6 || $input['hari'] == 7){
                $input['id_status_hari'] = 2;
            } else {
                $input['id_status_hari'] = 1;
            }
            DB::table('hari_kerja')->insert($input);
        }
    }
}
