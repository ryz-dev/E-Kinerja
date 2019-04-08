<?php

namespace App\Repositories;


class SkpdRepository extends BaseRepository
{
    public function model()
    {
        return 'App\Models\MasterData\Skpd';
    }

    public function search(array $parameters, $perPage = 10)
    {

        if (!empty($parameters['q'])) {
            $this->where('nama_skpd', 'like', '%' . $parameters['q'] . '%', 'or');
        }

        return $this->paginate($perPage);
    }

    public function getPage(array $parameters)
    {
        if (!empty($parameters['q'])) {
            $this->where('nama_skpd', 'like', '%' . $parameters['q'] . '%', 'or');
        }

        return $this->count();
    }

    public function required()
    {
        return [
            'nama_skpd' => 'required',
        ];
    }
}
