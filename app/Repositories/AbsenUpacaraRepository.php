<?php

namespace App\Repositories;


class AbsenUpacaraRepository extends BaseRepository
{
    public function model()
    {
        return 'App\Models\MasterData\AbsenUpacara';
    }

    public function search(array $parameters, $perPage = 10)
    {
        $this->where('SN', '<>', '');
        if (!empty($parameters['q'])) {
            $this->where('SN', 'like', '%' . $parameters['q'] . '%');
        }
        return $this->paginate($perPage);
    }

    public function getPage(array $parameters)
    {
        $this->where('SN', '<>', '');
        if (!empty($parameters['q'])) {
            $this->where('SN', 'like', '%' . $parameters['q'] . '%');
        }
        return $this->count();
    }

    public function required()
    {
        return [
            'SN' => 'required'
        ];
    }

    public function updateAbsenUpacara($uuid, array $parameter)
    {
        if ($data = $this->model->where('uuid', $uuid)->first()) {
            return $data->update($parameter);
        }
        return false;
    }

    public function deleteAbsenUpcara($uuid)
    {
        if ($data = $this->model->where('uuid', $uuid)->first()) {
            return $data->delete();
        }
        return false;
    }
}