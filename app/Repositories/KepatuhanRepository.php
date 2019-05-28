<?php

namespace App\Repositories;


use App\Models\Absen\Kepatuhan;
use App\Models\MasterData\Pegawai;

class KepatuhanRepository extends BaseRepository
{

    protected $pegawai;

    protected $periode;

    protected $kepatuhan;

    public function __construct($pegawai = null, $periode = null)
    {
        parent::__construct();

        $pegawai ? $this->pegawai = PegawaiRepository::dataPegawai($pegawai) : null;
        $periode ? $this->periode = $periode : $this->periode = date('Y-m-d');

        $this->dataKepatuhan();
    }

    public function model()
    {
        return 'App\Models\Absen\Kepatuhan';
    }

    public function dataKepatuhan($pegawai = null, $periode = null)
    {
        $pegawai = $pegawai ? $pegawai : $this->pegawai;
        $periode = $periode ? $periode : $this->periode;
//        $kepatuhan = $this->model->where('nip', $pegawai->nip)->wherePeriode($periode)->first();
        $kepatuhan = $this->model->where('nip', $pegawai->nip)->whereMonth('periode', '=', (int)month($periode))->whereYear('periode', '=', (int)year($periode));
        if ($kepatuhan) {
            $this->kepatuhan = $kepatuhan->first();
        } else {
            $this->kepatuhan = $kepatuhan;
        }
    }

    public function save($data)
    {
        $kepatuhan = $this->kepatuhan;
        if ($kepatuhan == null) {
            $kepatuhan = $this->model;
            $kepatuhan->uuid = (string)\Illuminate\Support\Str::uuid();
            $kepatuhan->nip = $this->pegawai->nip;
            $kepatuhan->periode = $this->periode;
            $kepatuhan->lkpn = $data['lkpn'] ? $data['lkpn'] : 0;
            $kepatuhan->bmd = $data['bmd'] ? $data['bmd'] : 0;
            $kepatuhan->tptgr = $data['tptgr'] ? $data['tptgr'] : 0;

            if ($kepatuhan->save()) {
                return true;
            } else {
                return false;
            }
        } else {
            $kepatuhan->lkpn = $data['lkpn'] ? $data['lkpn'] : 0;
            $kepatuhan->bmd = $data['bmd'] ? $data['bmd'] : 0;
            $kepatuhan->tptgr = $data['tptgr'] ? $data['tptgr'] : 0;

            if ($kepatuhan->save()) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function wherePeriode($date)
    {
        $this->whereMonth('periode', '=', (int)month($date))->whereYear('periode', '=', (int)year($date));
        return $this;
    }

    public function search($array){}

    public function getKepatuhan()
    {
        return $this->kepatuhan;
    }

    public function getListKepatuhanPegawai()
    {
        $pegawai = Pegawai::with('role')->where('nip',$this->pegawai->nip)->first();
        $min_date = null;
        if ($kepatuhan = Kepatuhan::where('nip',$pegawai->nip)->orderBy('periode','asc')->first()){
            $min_date = $kepatuhan->periode;
        }
        if ($pegawai->role->isNotEmpty()) {
            $list = [
                [
                    'key' => 'bmd',
                    'text' => 'BMD (Barang Milik Daerah)',
                    'status' => $this->kepatuhan ? $this->kepatuhan->bmd : 0,
                    'persen' => 50
                ], [
                    'key' => 'tptgr',
                    'text' => 'TPTGR (Tuntutan Perbendaharaan dan Tuntutan Ganti Rugi)',
                    'status' => $this->kepatuhan ? $this->kepatuhan->tptgr : 0,
                    'persen' => 50
                ]
            ];

            if (!$pegawai->role->contains('nama_role', 'Staf')) {
                $list = array_merge($list, [
                    [
                        'key' => 'lkpn',
                        'text' => 'LHKPN (Laporan Harta Kekayaan Penyelenggara Negara)',
                        'status' => $this->kepatuhan ? $this->kepatuhan->lkpn : 0,
                        'persen' => 40
                    ]
                ]);
                $list[0]['persen'] = 30;
                $list[1]['persen'] = 30;
            }
            $total = collect($list)->where('status',1)->reduce(function($total,$val){
                return $total + $val['persen'];
            });
            return [
                'min_date' => $min_date,
                'kepatuhan' => $list,
                'total' => $total
            ];
        }
        return [];
    }
}

