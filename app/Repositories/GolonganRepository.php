<?php

namespace App\Repositories;


class GolonganRepository extends BaseRepository
{
    public function model()
    {
        return 'App\Models\MasterData\Golongan';
    }

    public function search(array $parameters, $perPage = 10)
    {

        if (!empty($parameters['q'])) {
            $this->where('golongan', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->where('kriteria', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->where('tunjangan', 'like', '%' . $parameters['q'] . '%', 'or');
        }

        return $this->paginate($perPage);
    }

    public function getPage(array $parameters)
    {
        if (!empty($parameters['q'])) {
            $this->where('golongan', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->where('kriteria', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->where('tunjangan', 'like', '%' . $parameters['q'] . '%', 'or');
        }

        return $this->count();
    }

    public function required()
    {
        return [
            'golongan' => 'required',
            'tunjangan' => 'required'
        ];
    }
}
