<?php

namespace App\Repositories;


class RoleRepository extends BaseRepository
{
    public function model()
    {
        return 'App\Models\Role';
    }

    public function search(array $parameters, $perPage = 10)
    {
        if (!empty($parameters['q'])) {
            $q = $parameters['q'];
            $this->model = $this->model->orWhereHas('pegawai', function ($query) use ($q) {
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

    public function getPage(array $parameters)
    {
        if (!empty($parameters['q'])) {
            $q = $parameters['q'];
            $this->model = $this->model->orWhereHas('pegawai', function ($query) use ($q) {
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

    public function required($id = null)
    {
        if ($id) {
            return [
                'nip_pegawai' => '',
                'id_skp' => '',
                'periode' => 'date',
                'status' => 'required',
                'tanggal_selesai' => '',
                'nip_update' => ''
            ];
        }
        return [
            'nip_pegawai' => 'required',
            'id_skp' => 'required',
            'periode' => 'date',
            'status' => '',
            'tanggal_selesai' => '',
            'nip_update' => ''
        ];
    }
}
