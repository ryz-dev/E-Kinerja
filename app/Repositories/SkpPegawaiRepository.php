<?php

namespace App\Repositories;

use App\Repositories\PegawaiRepository;
use App\Repositories\SkpRepository;
use Illuminate\Support\Str;

class SkpPegawaiRepository extends BaseRepository
{
    protected $pegawai;
    protected $sasaranKerja;
    protected $periode;

    public function __construct($pegawai = null, $periode = null){
        parent::__construct();

        if ($pegawai != null) {
            $pegawai = PegawaiRepository::dataPegawai($pegawai);
            $sasaranKerja = $this->model->where('nip_pegawai', $pegawai->nip);
            if ($periode != null) {

                if (strtotime($periode) == false) {
                    $periode = date('Y-m-d', $periode);
                }
                else{
                    $periode = date('Y-m-d', strtotime($periode));
                }
                $this->periode = $periode;


                $sasaranKerja = $sasaranKerja
                ->wherePeriode($periode);
            }
            $this->sasaranKerja = $sasaranKerja->count() > 0?$sasaranKerja->get():null;
            $this->pegawai = $pegawai;
        }

        return $this;
    }

    public function model()
    {
        return 'App\Models\SkpPegawai';
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

    public function wherePeriode($date)
    {
        $this->whereMonth('periode','=', (int)month($date))->whereYear('periode','=', (int)year($date));
        return $this;
    }

    public function getAtasan()
    {
        return \Auth::user();
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

    public function sasaranKerjaAtasan()
    {
        $sasaranKerjaAtasan = new SkpPegawaiRepository($this->getAtasan()->nip, $this->periode);
        $sasaranKerja = $this->sasaranKerja();

        $daftarSasaranKinerja = $this->model
                                    ->where('nip_pegawai','!=', $sasaranKerjaAtasan->pegawai->nip)
                                    ->whereMonth('periode', '=', month($this->periode))
                                    ->whereYear('periode', '=', year($this->periode))->get();

        if ($sasaranKerjaAtasan->count()) {
            $sasaranKerjaAtasan = $sasaranKerjaAtasan
                ->sasaranKerja
                ->map(function($value, $key) use($daftarSasaranKinerja) {
                    if (!$daftarSasaranKinerja->whereIn('id_skp', $value->id_skp)->count()) {
                        // dd($value);
                        $data['id'] = $value->id;
                        $data['id_skp'] = $value->id_skp;
                        $data['task'] = $value->skpTask->task;
                        return collect($data);
                    }

                })->reject(function($name){
                    return empty($name);
                });
        }
        else{
            $sasaranKerjaAtasan = null;
        }

        return $sasaranKerjaAtasan;

    }

    public function sasaranKerja()
    {
        $sasaranKerja = $this->sasaranKerja;

        if ($sasaranKerja) {
            $sasaranKerja = $sasaranKerja
                ->map(function($value, $key){
                    $data['id'] = $value->id;
                    $data['id_skp'] = $value->id_skp;
                    $data['task'] = $value->skpTask->task;
                    return (object)$data;
                });
        }
        else{
            $sasaranKerja = null;
        }
        return $sasaranKerja;
    }

    public function getIndexData($date = null)
    {
        if ($date == null) {
            $date = date('Y-m-d');
        }

        $pegawai = new PegawaiRepository(\Auth::user()->nip);
        $result = $pegawai->getBawahanLangsung()->map(function($value, $key) use ($date) {
            $skp = $value->skp()->whereMonth('periode', date('m', strtotime($date)))
                            ->whereYear('periode', date('Y', strtotime($date)))
                            ->count();
            return [
                'uuid' => $value->uuid,
                'nip' => $value->nip,
                'foto' => $value->foto,
                'nama' => $value->nama,
                'jumlah_skp' => $skp,
                'f_edit' => $skp>0?true:false,
                'periode' => strtotime($date)
            ];
        });

        return $result;
    }

    public function sasaranKerjaData()
    {
        return [
            'sasaranKerjaAtasan' => $this->sasaranKerjaAtasan($this->periode),
            'dataPegawai' => $this->pegawai,
            'sasaranKerja' => $this->sasaranKerja(),
            'periode' => namaBulan((int)month($this->periode)).' '.year($this->periode)
        ];
    }

    public function save($data)
    {

        if ($this->saveSkpPegawai($data)) {
            return true;
        }

        return false;
    }

    private function saveSkpPegawai($data){
        $newSkp =  $this->saveSkp($data);
        $skp_pegawai = array();

        if (count($newSkp)) {
            foreach ($newSkp as $key => $value) {
                $skp_pegawai[$key]['uuid'] = (string) Str::uuid();
                $skp_pegawai[$key]['nip_pegawai'] = $this->pegawai->nip;
                $skp_pegawai[$key]['periode'] = \Carbon\Carbon::parse($this->periode);
                $skp_pegawai[$key]['id_skp'] = $value;
                $skp_pegawai[$key]['created_at'] = \Carbon\Carbon::now();
            }

            if ($this->model->insert($skp_pegawai)) {
                return true;
            }
            else{
                return false;
            }
        }

        return true;
    }

    private function saveSkp($data)
    {
        $skp = $data['skp']?$data['skp']:[];
        $skpDistribusi = $data['id_skpDistribusi']?$data['id_skpDistribusi']:[];
        $skp_id = array();

        if ($skp) {
            foreach ($skp as $key => $task) {
                $id = \DB::table('skp')->insertGetId(
                    [
                        'uuid' => (string) Str::uuid(),
                        'task' => $task,
                        'created_at' => \Carbon\Carbon::now()
                    ]
                );
                $skp_id[] = $id;
            }
        }

        // dd($skpDistribusi);

        return array_merge($skp_id, $skpDistribusi);
    }

    public function edit($data){
        $this->handleOldSkp($data['skpPegawai_id'], $data['skp_id'], $data['oldSkp']);
        if ($this->save($data)) {
            return true;
        }

        return false;
    }

    public function deleteSkp($skp = null){
        if ($skp == null) {
            $res = $this->sasaranKerja->map(function($value, $key){
                $jumlah = $this->model->where('id_skp', $value->id_skp)->count();
                if ($jumlah < 2 ) {
                    \DB::table('skp')->where('id', $value->id_skp)->delete();
                }

                return $this->model->where('id', $value->id)->delete();
            });
        }
        else{
            $res = $skp->map(function($value, $key){
                $jumlah = $this->model->where('id_skp', $value->id_skp)->count();
                if ($jumlah < 2 ) {
                    \DB::table('skp')->where('id', $value->id_skp)->delete();
                }

                return $this->model->where('id', $value->id)->delete();
            });
        }

        if ($res->count()) {
            return true;
        }

        return false;
    }

    private function handleOldSkp($skpPegawai, $skp, $task){
        // get deleted items
        $deletedSkp = $this->sasaranKerja->pluck('id')->diff($skpPegawai);

        // delete items
        $this->deleteSkp($this->sasaranKerja->whereIn('id', $deletedSkp));

        // update task
        if (count($skp)) {
            foreach ($skp as $key => $value) {
                \DB::table('skp')->where('id', $value)->update(['task' => $task[$key]]);
            }
        }
    }

}