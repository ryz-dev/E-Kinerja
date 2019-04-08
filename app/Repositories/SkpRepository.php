<?php

namespace App\Repositories;


class SkpRepository extends BaseRepository
{
    public function model()
    {
        return 'App\Models\Skp';
    }

    public function search(array $parameters, $perPage = 10)
    {

        if (!empty($parameters['q'])) {
            $this->where('task', 'like', '%' . $parameters['q'] . '%');
        }


        return $this->paginate($perPage);
    }

    public function getPage(array $parameters)
    {
        if (!empty($parameters['q'])) {
            $this->where('task', 'like', '%' . $parameters['task'] . '%');
        }

        return $this->count();
    }

    public function required()
    {
        return [
            'task' => 'required'
        ];
    }
}
