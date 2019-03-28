<?php
namespace App\Repositories;


class SkpPegawaiRepository extends BaseRepository
{
    public function model()
    {
        return 'App\Models\SkpPegawai';
    }

    public function search(array $parameters, $perPage = 10)
    {
        if (!empty($parameters['q'])) {
            $q = $parameters['q'];
            $this->model = $this->model->whereHas('pegawai', function ($query) use ($q) {
                $query->where('nama', 'like', '%' . $q . '%');
            })
                ->orWhereHas('skpTask', function ($query) use ($q) {
                    $query->where('task', 'like', '%' . $q . '%');
                })
                ->orWhereHas('atasanUpdate', function ($query) use ($q) {
                    $query->where('nama', 'like', '%' . $q . '%');
                })
                ->orWhere('nip_pegawai', 'like', '%' . $q . '%')
                ->orWhere('nip_updtae', 'like', '%' . $q . '%');
        }
        return $this->paginate($perPage);
    }

    public function getPage(array $parameters){
        if(!empty($parameters['q'])){
            $q = $parameters['q'];
            $this->model = $this->model->whereHas('pegawai', function ($query) use ($q) {
                $query->where('nama', 'like', '%' . $q . '%');
            })
                ->orWhereHas('skpTask', function ($query) use ($q) {
                    $query->where('task', 'like', '%' . $q . '%');
                })
                ->orWhereHas('atasanUpdate', function ($query) use ($q) {
                    $query->where('nama', 'like', '%' . $q . '%');
                })
                ->orWhere('nip_pegawai', 'like', '%' . $q . '%')
                ->orWhere('nip_updtae', 'like', '%' . $q . '%');
        }

        return $this->count();
    }
}
