<?php

namespace App\Repositories;

use App\Repositories\PegawaiRepository;

class KepatuhanRepository extends BaseRepository
{

    protected  $pegawai;

    protected $periode;

    protected $kepatuhan;

    public function __construct($pegawai = null, $periode = null){
        parent::__construct();

        $pegawai?$this->pegawai = PegawaiRepository::dataPegawai($pegawai):null;
        $periode?$this->periode = $periode:$this->periode = date('Y-m-d');

        $this->dataKepatuhan();
    }

    public function model(){
        return 'App\Models\Absen\Kepatuhan';
    }

    public function dataKepatuhan($pegawai = null, $periode = null){
        $pegawai = $pegawai?$pegawai:$this->pegawai;
        $periode = $periode?$periode:$this->periode;

        $kepatuhan = $this->model->where('nip', $pegawai->nip)->wherePeriode($periode)->first();

        if ($kepatuhan) {
            $this->kepatuhan = $kepatuhan->first();
        }else{
            $this->kepatuhan = $kepatuhan;
        }
    }

    public function save($data){
        $kepatuhan = $this->kepatuhan;
        if ($kepatuhan == null) {
            $kepatuhan = $this->model;
            $kepatuhan->uuid = (string)\Illuminate\Support\Str::uuid();
            $kepatuhan->nip = $this->pegawai->nip;
            $kepatuhan->periode = $this->periode;
            $kepatuhan->lkpn = $data['lkpn']?$data['lkpn']:0;
            $kepatuhan->bmd = $data['bmd']?$data['bmd']:0;
            $kepatuhan->tptgr = $data['tptgr']?$data['tptgr']:0;

            if ($kepatuhan->save()) {
                return true;
            }else{
                return false;
            }
        }
        else{
            $kepatuhan->lkpn = $data['lkpn']?$data['lkpn']:0;
            $kepatuhan->bmd = $data['bmd']?$data['bmd']:0;
            $kepatuhan->tptgr = $data['tptgr']?$data['tptgr']:0;

            if ($kepatuhan->save()) {
                return true;
            }else{
                return false;
            }
        }
    }

    public function wherePeriode($date)
    {
        $this->whereMonth('periode','=', (int)month($date))->whereYear('periode','=', (int)year($date));
        return $this;
    }

    public function search($array){}
}

