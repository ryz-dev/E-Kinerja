<?php
namespace App\Repositories;


use App\Models\MasterData\Agama;
use App\Models\MasterData\Jabatan;

class PegawaiRepository extends BaseRepository
{
    public function model()
    {
        return 'App\Models\MasterData\Pegawai';
    }

    public function search(array $parameters, $perPage = 10)
    {
        if (!empty($parameters['deleted'])){
            $this->withTrashed();
            $this->whereNotNull('deleted_at');
        }
        if (!empty($parameters['q'])) {
            $this->model = $this->model->where(function($query)use($parameters){
                $query->where('nama', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('nip', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('tanggal_lahir', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('jns_kel', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('tempat_lahir', 'like', '%' . $parameters['q'] . '%');
                $query->orWhereHas('jabatan',function($query)use($parameters){
                    $query->where('jabatan','like','%'.$parameters['q'].'%');
                });
            });
        }
        return $this->paginate($perPage);
    }

    public function getPage(array $parameters){
        if (!empty($parameters['deleted'])){
            $this->withTrashed();
            $this->whereNotNull('deleted_at');
        }
        if (!empty($parameters['q'])) {
            $this->model = $this->model->where(function($query)use($parameters){
                $query->where('nama', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('nip', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('tanggal_lahir', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('jns_kel', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('tempat_lahir', 'like', '%' . $parameters['q'] . '%');
                $query->orWhereHas('jabatan',function($query)use($parameters){
                    $query->where('jabatan','like','%'.$parameters['q'].'%');
                });
            });
        }
        return $this->count();
    }

    public function updatePassword($nip,$password){
        $pegawai = $this->model->whereNip($nip)->first();
        $pegawai->password = bcrypt($password);
        return $pegawai->save();
    }

    public function required($nip = null){
        return [
            'nip' => 'required|unique:pegawai,nip'.($nip ? ','.$nip.',nip' : ''),
            'foto' => 'image',
            'nama' => 'required',
            'tanggal_lahir' => 'required|date',
            'id_agama' => 'required|in:' . $this->getListAgama(),
            'id_jabatan' => 'in:' . $this->getListJabatan(),
            'jns_kel' => 'required|in:laki-laki,perempuan',
            'tempat_lahir' => 'required',
            'id_skpd' => 'required',
            'status_upacara' => 'required'
        ];
    }

    public function uploadFoto($file)
    {
        /*todo : handle untuk upload foto*/
        return str_replace('public/', '', $file->store('public/upload'));
    }

    private function getListAgama()
    {
        return implode(',', Agama::select('id')->pluck('id')->all());
    }

    private function getListJabatan()
    {
        return implode(',', Jabatan::select('id')->pluck('id')->all());
    }

}
