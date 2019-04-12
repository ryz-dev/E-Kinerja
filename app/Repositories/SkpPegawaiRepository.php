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
            $sasaranKerja = $this->where('nip_pegawai', $pegawai->nip);
            
            if ($periode != null) {
                if (strtotime($periode) == false) {
                    $periode = date('Y-m-d', $periode);
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
        $this->whereMonth('periode','=', month($date))->whereYear('periode','=', year($date));
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
        
        
        if ($sasaranKerjaAtasan->count()) {
            $sasaranKerjaAtasan = $sasaranKerjaAtasan
                ->get()
                ->map(function($value, $key){
                    $data['id'] = $value->id;
                    $data['id_skp'] = $value->id_skp;
                    $data['task'] = $value->skpTask->task;
                    return $data;
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
                    $data['id_skp_pegawai'] = $value->id;
                    $data['id_skp'] = $value->id_skp;
                    $data['task'] = $value->skpTask->task;
                    return $data;
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

    public function store($data)
    {
        $new_skp =  $this->saveNewSkp($data['skp']);
        $skp_pegawai = array();
        
        foreach ($new_skp as $key => $value) {
            $skp_pegawai[$key]['uuid'] = (string) Str::uuid();
            $skp_pegawai[$key]['nip_pegawai'] = $data['nip'];
            $skp_pegawai[$key]['periode'] = \Carbon\Carbon::parse($data['periode']);
            $skp_pegawai[$key]['id_skp'] = $value;
            $skp_pegawai[$key]['created_at'] = \Carbon\Carbon::now();
        }

        if ($this->model::insert($skp_pegawai)) {
            return true;
        }

        return false;
    }

    private function saveNewSkp($skp)
    {
        $skp_id = array();

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

        return $skp_id;
    }
}