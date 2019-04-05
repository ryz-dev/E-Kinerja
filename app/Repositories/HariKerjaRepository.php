<?php
namespace App\Repositories;


use App\Models\Absen\Checkinout;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\Agama;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Jabatan;
use App\Models\MasterData\Pegawai;
use App\User;

class HariKerjaRepository extends BaseRepository
{
    public function model()
    {
        return 'App\Models\MasterData\HariKerja';
    }

    public function search(array $parameters, $perPage = 10)
    {
        if (!empty($parameters['q'])) {
            $this->model = $this->model->where(function($query)use($parameters){
                $query->where('tanggal','like','%'.$parameters['q'].'%')
                    ->orWhere('bulan','like','%'.$parameters['q'].'%')
                    ->orWhere('hari','like','%'.$parameters['q'].'%')
                    ->orWhere('id_status_hari','like','%'.$parameters['q'].'%')
                    ->orWhere('tahun','like','%'.$parameters['q'].'%');
            });
        }
        return $this->paginate($perPage);
    }

    public function getPage(array $parameters){
        if (!empty($parameters['q'])) {
            $this->model = $this->model->where(function($query)use($parameters){
                $query->where('tanggal','like','%'.$parameters['q'].'%')
                    ->orWhere('bulan','like','%'.$parameters['q'].'%')
                    ->orWhere('hari','like','%'.$parameters['q'].'%')
                    ->orWhere('id_status_hari','like','%'.$parameters['q'].'%')
                    ->orWhere('tahun','like','%'.$parameters['q'].'%');

            });
        }
        return $this->count();
    }
}
