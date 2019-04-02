<?php
namespace App\Repositories;


use App\Models\Absen\Checkinout;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\Agama;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Jabatan;
use App\Models\MasterData\Pegawai;
use App\User;

class PegawaiRepository extends BaseRepository
{
    private $special_user = ['Bupati', 'Wakil Bupati', 'Sekretaris Daerah'];
    private $special_user_id = [2,3,4];
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

    public function getBawahan($nip,$skpd = null){
        $skpd = $skpd ? $skpd : null;
        $user = Pegawai::with('role')->where('nip',$nip)->first();
        $pegawai = $this->model->where('nip','!=',$user->nip);
        if ($skpd > 0) {
            $pegawai->where('id_skpd',$skpd);
        }

        if ($skpd < 0) {
            $pegawai->where('id_jabatan',3);
        }

        $pegawai = $pegawai->leftJoin('jabatan','pegawai.id_jabatan','=','jabatan.id');
        $pegawai = $pegawai->leftJoin('golongan','jabatan.id_golongan','=','golongan.id');
        $pegawai = $pegawai->orderBy('golongan.tunjangan','desc');
        $pegawai = $pegawai->orderBy('pegawai.nama');

        if (in_array($user->role()->pluck('id_role')->max(),$this->special_user_id) == false) {
            if ($user->role()->pluck('id_role')->max() != 5) {
                $pegawai->whereHas('jabatan', function($query) use ($user){
                    $query->where('id_atasan','=',$user->id_jabatan);
                });
            }
        }
        return $pegawai->get();
    }

    public function getRekap($nip_user,$nip,$bulan = null,$tahun = null){
        $user = $this->model->with('role')->where('nip',$nip_user)->first();
        $bulan = (int)($bulan ?: date('m'));
        $tahun = ($tahun ?: date('Y'));
        $hari_kerja = HariKerja::where('bulan', $bulan)->where('tahun', $tahun)->whereHas('statusHari', function ($query) {
            $query->where('status_hari', 'kerja');
        })->orderBy('tanggal', 'asc')->get();
        try {
            if (in_array($user->role()->first()->nama_role, $this->special_user) == false) {
                if ($user->role()->first()->nama_role == 'Kepala Dinas') {
                    $pegawai = $this->model->whereNip($nip)->where('id_skpd',$user->id_skpd)->firstOrFail();
                } else{
                    $pegawai = $this->model->whereNip($nip)->whereHas('jabatan.atasan.pegawai', function ($query)use($nip_user) {
                        $query->where('nip', $nip_user);
                    })->firstOrFail();
                }
            } else {
                $pegawai = $this->model->whereNip($nip)->firstOrFail();
            }
        } catch (\Exception $exception) {
            abort('404');
        }
        $data_inout = [];
        foreach ($hari_kerja AS $key => $hk) {
            $kinerja = $pegawai->kinerja()->where('tgl_mulai', '<=', $hk->tanggal)->where('tgl_selesai', '>=', $hk->tanggal)->terbaru()->first();
            $kehadiran['inout'] = $pegawai->checkinout()->where('checktime', 'like', '%' . $hk->tanggal . '%')->orderBy('checktype', 'desc')->get()->toArray();
            $kehadiran['status'] = '';
            if (count($kehadiran['inout']) > 0) {
                $masuk = $pulang = null;
                foreach ($kehadiran['inout'] AS $kh) {
                    if (isset($kh['checktype'])) {
                        if ($kh['checktype'] == 0) {
                            $masuk = $kh['checktime'];
                        }
                        if ($kh['checktype'] == 1) {
                            $pulang = $kh['checktime'];
                        }
                    }
                }
                if ($masuk){
                    $kehadiran['status'] = '';
                } else {
                    $kehadiran['status'] = 'alpa';
                }
                if (strtotime($masuk) <= strtotime($hk->tanggal . " 09:00:00")) {
                    if ($masuk && $pulang) {
                        if ((strtotime($pulang) - (strtotime($masuk))) >= (8 * 3600)) {
                            $kehadiran['status'] = 'hadir';
                        } else {
                            $kehadiran['status'] = 'alpa';
                        }
                    }
                } else {
                    $kehadiran['status'] = 'alpa';
                }
            }

            if (strtotime($hk->tanggal) < strtotime(date('Y-m-d'))){
                if ($kehadiran['status'] == ''){
                    $kehadiran['status'] = 'alpa';
                }
            }

            $status = ucfirst(str_replace('_', ' ', isset($kinerja->jenis_kinerja) ? $kinerja->jenis_kinerja : ''));
            if ($status == 'Hadir' || $status == '') {
                $status = ucfirst($kehadiran['status']);
            }
            $data_inout[] = [
                'tgl_prev' => isset($hari_kerja[$key - 1]->tanggal) ? $hari_kerja[$key - 1]->tanggal : '',
                'tgl_next' => isset($hari_kerja[$key + 1]->tanggal) ? $hari_kerja[$key + 1]->tanggal : '',
                'tgl' => $hk->tanggal,
                'tanggal' => $this->formatDate($hk->tanggal),
                'hari' => ucfirst($hk->Hari->nama_hari),
                'checkinout' => $kehadiran,
                'status' => $status,
                'approve' => isset($kinerja->approve) ? $kinerja->approve : ''
            ];
        }
        return [
            'tanggal_sekarang' => $this->formatDate(date('Y-m-d')),
            'rekap_bulanan' => $data_inout
        ];
    }

    public function getDetailRekap($nip,$tgl){
        $date = new HariKerja;

        /* Tarik tanggal sebelumnya */
        $date_prev = $date->whereDate('tanggal', '<', $tgl)
            ->whereIdStatusHari(1)
            ->orderBy('tanggal', 'desc')
            ->first();

        /* Tarik tanggal setelahnya */
        $date_next = $date->whereDate('tanggal', '>', $tgl)
            ->whereIdStatusHari(1)
            ->orderBy('tanggal', 'asc')
            ->first();

        /* Data kinerja */
        $pegawai = $this->model->where('nip', $nip)->first();
        $kinerja = Kinerja::where('nip', $pegawai->nip)
            ->whereDate('tgl_mulai', '<=', $tgl)
            ->whereDate('tgl_selesai', '>=', $tgl)
            ->terbaru()
            ->first();

        /* Data checkinout */
        $checkinout = Checkinout::where("nip", $pegawai->nip)
            ->whereDate("checktime", $tgl)
            ->get();
        $status = '';
        if (count($checkinout) > 0) {
            $masuk = $pulang = null;
            foreach ($checkinout->toArray() AS $kh) {
                if (isset($kh['checktype'])) {
                    if ($kh['checktype'] == 0) {
                        $masuk = $kh['checktime'];
                    }
                    if ($kh['checktype'] == 1) {
                        $pulang = $kh['checktime'];
                    }
                }
            }
            if ($masuk){
                $status = '';
            } else {
                $status = 'alpa';
            }
            if (strtotime($masuk) <= strtotime($tgl . " 09:00:00")) {
                if ($masuk && $pulang) {
                    if ((strtotime($pulang) - (strtotime($masuk))) >= (8 * 3600)) {
                        $status = 'hadir';
                    } else {
                        $status = 'alpa';
                    }
                }
            } else {
                $status = 'alpa';
            }
        }

        if (strtotime($tgl) < strtotime(date('Y-m-d'))){
            if ($status == ''){
                $status = 'alpa';
            }
        }

        /* Data array */
        if ($kinerja){
            if ($kinerja->jenis_kinerja != 'hadir'){
                $status = $kinerja->jenis_kinerja;
            }
        }
        $result = [
            "kinerja" => $kinerja,
            "checkinout" => $checkinout,
            "tanggal" => $this->formatDate2($tgl),
            "status" => ucwords(str_replace('_',' ',$status))
        ];

        return [array_merge($result, [
            'prev' => isset($date_prev->tanggal) == false ? '' : $date_prev->tanggal,
            'next' => isset($date_next->tanggal) == false ? '' : $date_next->tanggal
        ])];
    }

    public function storeRole($nip,$role){
        $pegawai = $this->model->find($nip);
        $data = $pegawai->role()->attach($role,['uuid'=>(string)\Illuminate\Support\Str::uuid()]);
        return $data;
    }

    public function deleteRole($nip){
        $pegawai = $this->model->find($nip);
        $data = $pegawai->role()->detach();
        return $data;
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
