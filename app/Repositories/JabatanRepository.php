<?php
namespace App\Repositories;


use App\Models\MasterData\Golongan;

class JabatanRepository extends BaseRepository
{
    public function model()
    {
        return 'App\Models\MasterData\Jabatan';
    }

    public function search(array $parameters, $perPage = 10)
    {

        if (!empty($parameters['q']))
        {
            $this->where('jabatan', 'like', '%' . $parameters['q'] . '%','or');
            $this->model = $this->model->orWhereHas('golongan',function($query)use($parameters){
                $query->where('golongan','like','%'.$parameters['q'].'%');
                $query->orWhere('tunjangan','like','%'.$parameters['q'].'%');
            });
            $this->model = $this->model->orWhereHas('atasan',function($query)use($parameters){
                $query->where('jabatan','like','%'.$parameters['q'].'%');
            });
        }

        return $this->paginate($perPage);
    }

    public function getPage(array $parameters){
        if(!empty($parameters['q'])){
            $this->where('jabatan', 'like', '%' . $parameters['q'] . '%','or');
            $this->model = $this->model->orWhereHas('golongan',function($query)use($parameters){
                $query->where('golongan','like','%'.$parameters['q'].'%');
                $query->orWhere('tunjangan','like','%'.$parameters['q'].'%');
            });
            $this->model = $this->model->orWhereHas('atasan',function($query)use($parameters){
                $query->where('jabatan','like','%'.$parameters['q'].'%');
            });
        }

        return $this->count();
    }

    public function required(){
        return [
            'jabatan' => 'required',
            'id_golongan' => 'required|in:'.$this->getListGolongan(),
//            'id_atasan' => 'in:'.$this->getListJabatan(),
        ];
    }

    private function getListGolongan(){
        return implode(',',Golongan::select('id')->pluck('id')->all());
    }
}
