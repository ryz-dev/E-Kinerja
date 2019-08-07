<?php

namespace App\Repositories;


use App\Models\Absen\Checkinout;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\AbsenUpacara;
use App\Models\MasterData\Agama;
use App\Models\MasterData\Bulan;
use App\Models\MasterData\FormulaVariable;
use App\Models\MasterData\Hari;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Jabatan;
use App\Models\MasterData\Pegawai;
use App\Models\MasterData\Skpd;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PDF;

class PegawaiRepository extends BaseRepository
{
    public $pegawai;
    private $special_user = ['Bupati', 'Wakil Bupati', 'Sekretaris Daerah'];
    private $special_user_id = [2, 3, 4];
    private $jam_masuk = '09:00:59';
    private $jam_masuk_upacara = '07.30.59';

    public function __construct($nip = null)
    {
        parent::__construct();

        if ($nip) {
            $this->pegawai = $this->model::where('nip', $nip)->orWhere('uuid', $nip)->first();
        }
    }

    public static function dataPegawai($pegawai)
    {
//        return app()->make(self::model())->where('nip', $pegawai)->orWhere('uuid', $pegawai)->first();
        $self = new self();
        return app()->make($self->model())->where('nip', $pegawai)->orWhere('uuid', $pegawai)->first();
    }

    public function model()
    {
        return 'App\Models\MasterData\Pegawai';
    }

    public function search(array $parameters, $perPage = 10)
    {
        if (!empty($parameters['deleted'])) {
            $this->withTrashed();
            $this->whereNotNull('deleted_at');
        }
        if (!empty($parameters['q'])) {
            $this->model = $this->model->where(function ($query) use ($parameters) {
                $query->where('nama', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('nip', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('tanggal_lahir', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('jns_kel', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('tempat_lahir', 'like', '%' . $parameters['q'] . '%');
                $query->orWhereHas('jabatan', function ($query) use ($parameters) {
                    $query->where('jabatan', 'like', '%' . $parameters['q'] . '%');
                });
            });
        }
        if (isset($parameters['skpd'])){
            if ($parameters['skpd'] != 0){
                $this->model = $this->model->where('id_skpd',$parameters['skpd']);
            }
        }
        return $this->paginate($perPage);
    }

    public function setPassword($nip, $password)
    {
        $this->model->where('nip', $nip)->update([
            'password' => bcrypt($password)
        ]);
    }

    public function getPage(array $parameters)
    {
        if (!empty($parameters['deleted'])) {
            $this->withTrashed();
            $this->whereNotNull('deleted_at');
        }
        if (!empty($parameters['q'])) {
            $this->model = $this->model->where(function ($query) use ($parameters) {
                $query->where('nama', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('nip', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('tanggal_lahir', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('jns_kel', 'like', '%' . $parameters['q'] . '%');
                $query->orWhere('tempat_lahir', 'like', '%' . $parameters['q'] . '%');
                $query->orWhereHas('jabatan', function ($query) use ($parameters) {
                    $query->where('jabatan', 'like', '%' . $parameters['q'] . '%');
                });
            });
        }
        if (isset($parameters['skpd'])){
            if ($parameters['skpd'] != 0){
                $this->model = $this->model->where('id_skpd',$parameters['skpd']);
            }
        }
        return $this->count();
    }

    public function getPageMonitoringAbsen($nip, $skpd, $search)
    {
        $user = Pegawai::where('nip', $nip)->first();

        $data = Pegawai::where('nip', '<>', '');

        if (in_array($user->role()->pluck('id_role')->max(), $this->special_user_id) == false) {
            if ($user->role()->pluck('id_role')->max() > 5) {
                $data->whereHas('jabatan', function ($query) use ($user) {
                    $query->where('id_atasan', '=', $user->id_jabatan);
                });
            } else {
                $data->where('id_skpd',$user->id_skpd)->where('nip','!=',$user->nip);
            }
        }

        if ($skpd > 0) {
            $data = $data->where('id_skpd', $skpd);
        }

        if ($search) {
            $data->where('nip', 'like', '%' . $search . '%')->orWhere('nama', 'like', '%' . $search . '%');
        }
        return $data->count();

    }

    public function updatePassword($nip, $password)
    {
        $pegawai = $this->model->whereNip($nip)->first();
        $pegawai->password = bcrypt($password);
        return $pegawai->save();
    }

    public function getBawahan($nip, $skpd = null, $search = null, $page = null, $show_limit = null)
    {
        $skpd = $skpd ? $skpd : null;
        $user = $this->model::with('role')->where('nip', $nip)->first();
        $pegawai = $this->model->where('nip', '!=', $user->nip);
        if ($skpd > 0) {
            $pegawai->where('id_skpd', $skpd);
        }
        if ($skpd < 0) {
            $pegawai->where('id_jabatan', 3);
        }
        $pegawai = $pegawai->leftJoin('jabatan', 'pegawai.id_jabatan', '=', 'jabatan.id');
        $pegawai = $pegawai->leftJoin('golongan', 'jabatan.id_golongan', '=', 'golongan.id');
        $pegawai = $pegawai->orderBy('golongan.tunjangan', 'desc');
        $pegawai = $pegawai->orderBy('pegawai.nama');
        if (in_array($user->role()->pluck('id_role')->max(), $this->special_user_id) == false) {
            if ($user->role()->pluck('id_role')->max() > 5) {
                $pegawai->whereHas('jabatan', function ($query) use ($user) {
                    $query->where('id_atasan', '=', $user->id_jabatan);
                });
            } else {
                $pegawai->where('id_skpd',$user->id_skpd)->where('nip','!=',$user->nip);
            }
        }
        if ($search) {
            $pegawai->where(function ($query) use ($search) {
                $query->where('nip', 'like', '%' . $search . '%')->orWhere('nama', 'like', '%' . $search . '%');
            });
        }
        if ($page) {
            $pegawai = $pegawai->paginate($show_limit);
        } else {
            $pegawai = $pegawai->get();
        }
        return $pegawai;
    }

    public function getRekap($nip_user, $nip, $bulan = null, $tahun = null, $is_mobile = false)
    {
        $user = $this->model->with('role')->where('nip', $nip_user)->firstOrFail();
        $bulan = (int)($bulan ?: date('m'));
        $tahun = ($tahun ?: date('Y'));
        $hari_kerja = HariKerja::where('bulan', $bulan)->where('tahun', $tahun)->where('tanggal', '<=', date('Y-m-d'))->whereHas('statusHari', function ($query) {
            $query->where('status_hari', 'kerja');
        })->orderBy('tanggal', 'asc')->get();
        try {
            if (in_array($user->role()->first()->nama_role, $this->special_user) == false) {
                if ($user->role()->first()->nama_role == 'Kepala Dinas') {
                    $pegawai = $this->model->whereNip($nip)->where('id_skpd', $user->id_skpd)->firstOrFail();
                } else {
                    $pegawai = $this->model->whereNip($nip)->whereHas('jabatan.atasan.pegawai', function ($query) use ($nip_user) {
                        $query->where('nip', $nip_user);
                    })->firstOrFail();
                }
            } else {
                $pegawai = $this->model->whereNip($nip)->firstOrFail();
            }
        } catch (Exception $exception) {
            abort('404');
        }
        $data_inout = [];
        foreach ($hari_kerja AS $key => $hk) {
            $apel = false;
            $kinerja = $pegawai->kinerja()->where('tgl_mulai', '<=', $hk->tanggal)->where('tgl_selesai', '>=', $hk->tanggal)->terbaru()->first();

            $kehadiran['inout'] = $pegawai->checkinout()->where('checktime', 'like', '%' . $hk->tanggal . '%')->orderBy('checktype', 'desc')->get()->toArray();
            $kehadiran['status'] = '';
            $is_upacara = false;
            if (date('N', strtotime($hk->tanggal)) == 1) {
                $is_upacara = true;
            }
            $wajib_upacara = $pegawai->status_upacara ? true : false;
            $upacara = false;
            if ($is_upacara) {
                if ($wajib_upacara) {
                    $mesin_upacara = AbsenUpacara::select('SN')->pluck('SN')->all();
                    if ($absen_upacara = Checkinout::where('nip', $pegawai->nip)->where('checktype', 0)->whereDate('checktime', $hk->tanggal)->whereIn('sn', $mesin_upacara)->whereTime('checktime', '<=', $this->jam_masuk_upacara)->first()) {
                        $upacara = true;
                    }
                }
            }
            $masuk = $pulang = null;
            if (count($kehadiran['inout']) > 0) {
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
                if ($masuk) {
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
            } else {
                if (strtotime($hk->tanggal) <= strtotime(date('Y-m-d'))) {
                    $kehadiran['status'] = 'alpa';
                }
            }

            if (strtotime($hk->tanggal) < strtotime(date('Y-m-d'))) {
                if ($kehadiran['status'] == '') {
                    $kehadiran['status'] = 'alpa';
                }
            }

            if ($kehadiran['status'] != 'alpa') {
                if ($masuk != null) {
                    if (strtotime($masuk) <= strtotime($hk->tanggal . " 07:30:00")) {
                        $apel = true;
                    }
                }
                if ($is_upacara && $wajib_upacara) {
                    $apel = $upacara;
                }
            }
            $status = ucfirst(str_replace('_', ' ', isset($kinerja->jenis_kinerja) ? $kinerja->jenis_kinerja : ''));
            if ($status == 'Hadir' || $status == '') {
                $status = ucfirst($kehadiran['status']);
            } else {
                if (isset($kinerja->approve) ? $kinerja->approve == 0 : false){
                    $status = 'Alpa';
                }
            }
            if (!$is_mobile) {
                $data_inout[] = [
                    'tgl_prev' => isset($hari_kerja[$key - 1]->tanggal) ? $hari_kerja[$key - 1]->tanggal : '',
                    'tgl_next' => isset($hari_kerja[$key + 1]->tanggal) ? $hari_kerja[$key + 1]->tanggal : '',
                    'tgl' => $hk->tanggal,
                    'tanggal' => formatDate($hk->tanggal),
                    'hari' => ucfirst($hk->Hari->nama_hari),
                    'checkinout' => $kehadiran,
                    'status' => $status,
                    'apel' => $apel,
                    'approve' => isset($kinerja->approve) ? $kinerja->approve : ''
                ];
            } else {
                $nilai_kinerja = null;
                if (isset($kinerja->approve)) {
                    if ($kinerja->approve == 2) {
                        $nilai_kinerja = $kinerja->nilai_kinerja;
                    }
                }

                $data_inout[] = [
                    'tanggal' => $hk->tanggal,
                    'hari' => ucfirst($hk->Hari->nama_hari),
                    'status' => $status,
                    'apel' => $apel,
//                      'approve' => isset($kinerja->approve) ? $kinerja->approve : 0
                    'nilai_kinerja' => $nilai_kinerja
                ];
            }
        }
        if (!$is_mobile) {
            return [
                'tanggal_sekarang' => $this->formatDate(date('Y-m-d')),
                'rekap_bulanan' => $data_inout
            ];
        } else {
            $min_date = HariKerja::whereHas('statusHari', function ($query) {
                $query->where('status_hari', 'kerja');
            })->select('tanggal')->orderBy('tanggal')->first();
            return [
                'uuid' => $pegawai->uuid,
                'nama' => $pegawai->nama,
                'nip' => $pegawai->nip,
                'foto' => $pegawai->foto,
                'rekap_bulanan' => $data_inout,
                'min_date' => $min_date->tanggal
            ];
        }
    }

    public function getDetailRekap($nip, $tgl, $is_mobile = false)
    {
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
        $pegawai = $this->model->where('nip', $nip)->firstOrFail();
        $kinerja = Kinerja::with(['skp_pegawai' => function ($query) {
            $query->select('skp_pegawai.id', 'id_skp', 'status');
            $query->with(['skpTask' => function ($query) {
                $query->select('task', 'id');
            }]);
        }, 'media' => function ($query) {
            $query->select('id_kinerja', 'media', 'nama_media');
        }])->where('nip', $pegawai->nip)
            ->whereDate('tgl_mulai', '<=', $tgl)
            ->whereDate('tgl_selesai', '>=', $tgl)
            ->terbaru()
            ->first();
        /* Data checkinout */
        $checkinout = Checkinout::where("nip", $pegawai->nip)
            ->whereDate("checktime", $tgl)
            ->get();
        $status = '';
        $apel = false;
        $in = ($checkinout->contains('checktype', 0)) ? $checkinout->where('checktype', 0)->min()->checktime : '';
        $out = ($checkinout->contains('checktype', 1)) ? $checkinout->where('checktype', 1)->max()->checktime : '';
        $is_upacara = false;
        if (date('N', strtotime($tgl)) == 1) {
            if (HariKerja::where('tanggal', $tgl)->where('id_status_hari', 1)->first()) {
                $is_upacara = true;
            }
        }
        $wajib_upacara = $pegawai->status_upacara ? true : false;
        $upacara = false;
        if ($is_upacara) {
            if ($wajib_upacara) {
                $mesin_upacara = AbsenUpacara::select('SN')->pluck('SN')->all();
                if ($absen_upacara = Checkinout::where('nip', $pegawai->nip)->where('checktype', 0)->whereDate('checktime', $tgl)->whereIn('sn', $mesin_upacara)->whereTime('checktime', '<=', $this->jam_masuk_upacara)->first()) {
                    $upacara = true;
                }
            }
        }
        $masuk = $pulang = null;
        if (count($checkinout) > 0) {
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
            if ($masuk) {
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

        if (strtotime($tgl) < strtotime(date('Y-m-d'))) {
            if ($status == '') {
                $status = 'alpa';
            }
        }

        if ($status != 'alpa') {
            if ($masuk != null) {
                if (strtotime($masuk) <= strtotime($tgl . " 07:30:00")) {
                    $apel = true;
                }
            }
            if ($is_upacara && $wajib_upacara) {
                $apel = $upacara;
            }
        }
        /* Data array */
        if ($kinerja) {
            if ($kinerja->jenis_kinerja != 'hadir') {
                $status = $kinerja->jenis_kinerja;
            }
        }
        if (!$is_mobile) {
            $result = [
                "apel" => $apel,
                "kinerja" => $kinerja,
                "checkinout" => $checkinout,
                "tanggal" => formatDate2($tgl),
                "status" => ucwords(str_replace('_', ' ', $status))
            ];

            return array_merge($result, [
                'prev' => isset($date_prev->tanggal) == false ? '' : $date_prev->tanggal,
                'next' => isset($date_next->tanggal) == false ? '' : $date_next->tanggal
            ]);
        } else {
            $min_date = HariKerja::whereHas('statusHari', function ($query) {
                $query->where('status_hari', 'kerja');
            })->select('tanggal')->orderBy('tanggal')->first();
            /*if ($kinerja) {
                if (isset($kinerja->skp_pegawai)) {
                    $skp_pegawai = $kinerja->skp_pegawai->map(function ($val) {
                        if ($val->skpTask) {
                            return [
                                'task' => $val->skpTask->task,
                                'status' => $val->status
                            ];
                        }
                    });
                }
                if (isset($kinerja->media)) {
                    $media = $kinerja->media->map(function ($val) {
                        return [
                            'title' => $val->nama_media,
                            'link_file' => $val->media
                        ];
                    });
                }
                $kinerja = $kinerja->toArray();
                if (isset($skp_pegawai))
                    $kinerja['skp_pegawai'] = $skp_pegawai;
                if (isset($media))
                    $kinerja['media'] = $media;
            }*/

            return [
                'uuid' => $pegawai->uuid,
                'nama' => $pegawai->nama,
                'nip' => $pegawai->nip,
                'foto' => $pegawai->foto,
                'kinerja' => $kinerja,
                'checkinout' => [
                    'in' => $in,
                    'out' => $out,
                ],
                'apel' => $apel,
                'status' => $status,
                'min_date' => $min_date->tanggal
            ];
        }
    }

    public function storeRole($nip, $role)
    {
        $pegawai = $this->model->find($nip);
        $data = $pegawai->role()->attach($role, ['uuid' => (string)Str::uuid()]);
        return $data;
    }

    public function deleteRole($nip)
    {
        $pegawai = $this->model->find($nip);
        $data = $pegawai->role()->detach();
        return $data;
    }

    public function required($nip = null)
    {
        return [
            'nip' => 'required|unique:pegawai,nip' . ($nip ? ',' . $nip . ',nip' : ''),
            'foto' => 'image',
            'nama' => 'required',
            'tanggal_lahir' => 'required|date',
            'id_agama' => 'required|in:' . $this->getListAgama(),
            'id_jabatan' => 'in:' . $this->getListJabatan(),
            'jns_kel' => 'required|in:laki-laki,perempuan',
            'tempat_lahir' => 'required',
            'id_skpd' => 'required',
            'status_upacara' => 'required',
            'password' => 'confirmed'
        ];
    }

    private function getListAgama()
    {
        return implode(',', Agama::select('id')->pluck('id')->all());
    }

    private function getListJabatan()
    {
        return implode(',', Jabatan::select('id')->pluck('id')->all());
    }

    public function uploadFoto($file)
    {
        /*todo : handle untuk upload foto*/
        return str_replace('public/', '', $file->store('public/upload'));
    }

    public function downloadRekapBulanan($nip, $skpd, $d_id_skpd, $periode_rekap, $download = false)
    {
        if (str_contains($periode_rekap,'/')){
            $bulan = explode('/',$periode_rekap)[1];
            $tahun = explode('/',$periode_rekap)[2];
            $periode_rekap = $tahun.'-'.$bulan;
        } else {
            // dd($request);
            $bulan = (int)($periode_rekap ? date('m', strtotime($periode_rekap)) : date('m'));
            $tahun = (int)($periode_rekap ? date('Y', strtotime($periode_rekap)) : date('Y'));
        }
        $user = Pegawai::where('nip', $nip)->first();
        $hari_kerja = HariKerja::whereHas('statusHari', function ($query) use ($bulan, $tahun) {
            $query->where('status_hari', 'kerja');
        })->where('bulan', $bulan)->where('tahun', $tahun)->where('tanggal', '<=', date('Y-m-d'))->orderBy('tanggal', 'asc')->get();

        $formula = FormulaVariable::all();
        $persen['kinerja'] = $formula->where('variable', 'kinerja')->first()->persentase_nilai;
        $persen['absen'] = $formula->where('variable', 'absen')->first()->persentase_nilai;
        $persen['kepatuhan'] = $formula->where('variable', 'kepatuhan')->first()->persentase_nilai;
        try {
            $pegawai = $this->getDataPegawai($user, $bulan, $tahun, $d_id_skpd);
            $data = $this->parseDataRekap($pegawai, $persen, $hari_kerja);
            $skpd = Skpd::where('id', $skpd)->first();
            $namaSkpd = $skpd ? $skpd->nama_skpd : 'PEMERINTAH KABUPATEN KOLAKA';
            $periode = ucfirst(Bulan::find((int)date('m', strtotime($periode_rekap)))->nama_bulan . ' ' . date('Y', strtotime($periode_rekap)));
            $tanggal_cetak = date('d') . ' ' . ucfirst(Bulan::find((int)date('m'))->nama_bulan) . ' ' . date('Y');
            $pdf = PDF::loadView('pdf.rekap-bulanan', compact('data', 'namaSkpd', 'periode', 'tanggal_cetak'));
            $pdf->setPaper('legal', 'landscape');
            if ($download) {
                return $pdf->download('rekap_bulanan.pdf');
            }
            return $pdf->stream('rekap_bulanan.pdf');
        } catch (\Exception $exception){
            Log::info($exception->getMessage());
        }
    }

    private function getDataPegawai($user, $bulan, $tahun, $id_skpd)
    {
        $pegawai = Pegawai::where('nip', '!=', '')->whereNotNull('id_jabatan');
        if ($id_skpd > 0) {
            $pegawai->where('id_skpd', $id_skpd);
        }

        if ($id_skpd < 0) {
            $pegawai->where('id_jabatan', 3);
        }

        $pegawai = $pegawai->leftJoin('jabatan', 'pegawai.id_jabatan', '=', 'jabatan.id');
        $pegawai = $pegawai->leftJoin('golongan', 'jabatan.id_golongan', '=', 'golongan.id');
        $pegawai = $pegawai->orderBy('golongan.tunjangan', 'desc');
        $pegawai = $pegawai->orderBy('pegawai.nama');
        if (in_array($user->role()->pluck('id_role')->max(), $this->special_user_id) == false) {
            if ($user->role()->pluck('id_role')->max() > 5) {
                $pegawai->whereHas('jabatan', function ($query) use ($user) {
                    $query->where('id_atasan', '=', $user->id_jabatan);
                });
            } else {
                $pegawai->where('id_skpd',$user->id_skpd)->where('nip','!=',$user->nip);
            }
        }

        $pegawai = $pegawai->with(
            [

                'checkinout' => function ($query) use ($bulan, $tahun) {
                    $query->select('nip', 'checktime', 'checktype')->whereMonth('checktime', $bulan)->whereYear('checktime', $tahun);
                },
                'kinerja' => function ($query) use ($bulan, $tahun) {
                    $query->select('nip', 'approve', 'jenis_kinerja', 'tgl_mulai', 'tgl_selesai','nilai_kinerja')->whereMonth('tgl_mulai', $bulan)->whereYear('tgl_mulai', $tahun);
                }
            ]
        );
        return $pegawai;
    }

    private function parseDataRekap($pegawai, $persen, $hari_kerja)
    {

        return $data = $pegawai->get()->map(function ($item, $key) use ($persen, $hari_kerja) {
            // dd($item->jabatan());
            $tunjangan = $item->jabatan()->first()->golongan->tunjangan;
            $data['jabatan'] = $item->jabatan()->first()->jabatan;
            $data['kelas_jabatan'] = $item->jabatan()->first()->golongan->golongan;
            $data['data_pribadi'] = $item->toArray();
            $raw_kinerja = $this->parseKinerja($item, $key, $hari_kerja);
            $tambahan_absen = $raw_kinerja->sum('absen_tambahan');
            $raw_absen = $this->parseAbsen($item, $key, $hari_kerja);
            $apel = ($hari_kerja->count() - $this->parseApel($item, $hari_kerja) < 6) ? 100 : 0;
            $data['kinerja'] = $raw_kinerja->sum('kinerja');
            $data['persentase_kinerja'] = ((($data['kinerja'] / ($hari_kerja->count() * 10)) * 100) * $persen['kinerja']) / 100;
            $data['absen'] = $raw_absen->reduce(function ($total, $val) {
                    return $total + $val;
                }) + $tambahan_absen;
            $data['persentase_absen'] = (((($data['absen'] / $hari_kerja->count()) * 60) + (($apel / 100) * 40)) * $persen['absen']) / 100;
            $data['persentase_kepatuhan'] = $this->parseKepatuhan($item->nip, $persen['kepatuhan']);
            $total = $this->calculateTotalTunjangan($data['persentase_absen'], $data['persentase_kinerja'], $data['persentase_kepatuhan'], $tunjangan);
            $data['total_tunjangan'] = $total['tunjangan'];
            $data['total_persentase'] = $total['persentase'];
            return $data;
        });
    }

    private function parseApel($pegawai, $hari_kerja)
    {
        $mesin_upacara = AbsenUpacara::select('SN')->pluck('SN')->all();
        return $hari_kerja->reduce(function ($total, $hk) use ($pegawai, $mesin_upacara) {
            $is_upacara = $apel = false;
            if (date('N', strtotime($hk->tanggal)) == 1) {
                $is_upacara = true;
            }
            $wajib_upacara = $pegawai->status_upacara ? true : false;
            if ($is_upacara && $wajib_upacara) {
                if ($absen_upacara = $pegawai->checkinout()->where('nip', $pegawai->nip)->where('checktype', '0')->whereDate('checktime', $hk->tanggal)->whereIn('sn', $mesin_upacara)->whereTime('checktime', '<=', $this->jam_masuk_upacara)->first()) {
                    $apel = true;
                }
            } else {
                $masuk = $pegawai->checkinout->filter(function ($inout) use ($hk) {
                    return ($inout->checktype == '0' && date('Y-m-d', strtotime($inout->checktime)) == $hk->tanggal);
                })->first();
                $keluar = $pegawai->checkinout->filter(function ($inout) use ($hk) {
                    return ($inout->checktype == '1' && date('Y-m-d', strtotime($inout->checktime)) == $hk->tanggal);
                })->first();
                if ($masuk && $keluar) {
                    if (strtotime($masuk->checktime) <= strtotime($hk->tanggal . " 07:30:00")) {
                        $apel = true;
                    }
                }
            }
            return $apel ? $total + 1 : $total;
        });
    }

    private function parseKepatuhan($nip, $persen)
    {
        $kepatuhan = new KepatuhanRepository($nip);
        $data_kepatuhan = $kepatuhan->getListKepatuhanPegawai();
        $jumlah_kepatuhan = 0;
        if (!empty($data_kepatuhan)) {
            $jumlah_kepatuhan = collect($data_kepatuhan['kepatuhan'])->where('status', 1)->reduce(function ($total, $val) {
                return $total + $val['persen'];
            });
        }
        return $jumlah_kepatuhan ? ($jumlah_kepatuhan / 100) * $persen : 0;
    }

    private function parseKinerja($item, $key, $hari_kerja)
    {
        return $hari_kerja->map(function ($value) use ($key, $item) {
            $kinerja = $absen_tambahan = 0;
            foreach ($item->kinerja AS $itemkinerja) {
                if ($itemkinerja->tgl_mulai <= $value->tanggal && $itemkinerja->tgl_selesai >= $value->tanggal && $itemkinerja->approve == 2) {
                    $kinerja += $itemkinerja->nilai_kinerja;
                }
                if ($itemkinerja->tgl_mulai <= $value->tanggal && $itemkinerja->tgl_selesai >= $value->tanggal && $itemkinerja->approve == 2 && $itemkinerja->jenis_kinerja <> 'hadir') {
                    if ($itemkinerja->jenis_kinerja == 'sakit' || $itemkinerja == 'izin'){
                        $absen_tambahan += 0.2;
                    } else {
                        $absen_tambahan++;
                    }
                }
            }
            return collect(['kinerja' => $kinerja, 'absen_tambahan' => $absen_tambahan, 'tanggal' => $value->tanggal]);
        })->filter(function ($value, $key) {
            return $value->filter(function ($v, $k) {
                return $v > 0;
            });
        });
//        return $item->kinerja->filter(function ($value) {
//            return $value->tgl_selesai <= date('Y-m-d');
//        })->map(function ($itemkinerja, $keykinerja) use ($key, $hari_kerja) {
//            $kinerja = $absen_tambahan = 0;
//            foreach ($hari_kerja AS $hk) {
//                if ($itemkinerja->whereDate('tgl_mulai', '>=', $hk->tanggal)->whereDate('tgl_selesai', '<=', $hk->tanggal) && $itemkinerja->approve == 2) {
//                    $kinerja++;
//                }
//                if ($itemkinerja->whereDate('tgl_mulai', '>=', $hk->tanggal)->whereDate('tgl_selesai', '<=', $hk->tanggal) && $itemkinerja->approve == 2 && $itemkinerja->jenis_kinerja <> 'hadir') {
//                    $absen_tambahan++;
//                }
//            }
//            /*if ($itemkinerja->wherein('tgl_mulai', $hari_kerja->pluck('tanggal')) && $itemkinerja->approve == 2) {
//                $kinerja = 1;
//            }
//            if ($itemkinerja->wherein('tgl_mulai', $hari_kerja->pluck('tanggal')) && $itemkinerja->approve == 2 && $itemkinerja->jenis_kinerja <> 'hadir') {
//                $absen_tambahan = 1;
//            }*/
//
//            return collect(['kinerja' => $kinerja, 'absen_tambahan' => $absen_tambahan]);
//        })->filter(function ($value, $key) {
//            return $value->filter(function ($v, $k) {
//                return $v > 0;
//            });
//        });
    }

    private function parseAbsen($item, $key, $hari_kerja)
    {
        return $item->checkinout->groupBy(function ($itemcheckiout, $keycheckiout) {
            return date('Y-m-d', strtotime($itemcheckiout->checktime));
        })->map(function ($itemabsen, $keyabsen) use ($key, $hari_kerja) {
            if ($itemabsen->wherein('checktime', $hari_kerja->pluck('tanggal'))) {
                $masuk = $itemabsen->where('checktype', '0') ? $itemabsen->where('checktype', '0') : false;
                $keluar = $itemabsen->where('checktype', '1') ? $itemabsen->where('checktype', '1') : false;
                $nip = $itemabsen->first()->nip;

                if ($masuk->first() && $keluar->first()) {
                    $tanggal = date('Y-m-d', strtotime($masuk->first()->checktime));
                    if (strtotime($masuk->first()->checktime) <= strtotime($tanggal . " 08:00:00")) {
                        if ((strtotime($keluar->first()->checktime) - strtotime($masuk->first()->checktime)) >= (8 * 3600)) {
                            return 1;
                        }
                    } else if (strtotime($masuk->first()->checktime) <= strtotime($tanggal . " 08:30:00")) {
                        if ((strtotime($keluar->first()->checktime) - strtotime($masuk->first()->checktime)) >= (8 * 3600)) {
                            return 0.8;
                        }
                    } else if (strtotime($masuk->first()->checktime) <= strtotime($tanggal . " 09:00:00")) {
                        if ((strtotime($keluar->first()->checktime) - strtotime($masuk->first()->checktime)) >= (8 * 3600)) {
                            return 0.6;
                        }
                    } else if (strtotime($masuk->first()->checktime) > strtotime($tanggal . " 09:00:00")) {
                        if ((strtotime($keluar->first()->checktime) - strtotime($masuk->first()->checktime)) >= (8 * 3600)) {
                            return 0.4;
                        }
                    }
                    return 0.2;
                }
                return 0;
            }
        })->filter(function ($value, $key) {
            return $value > 0;
        });
    }

    private function calculateTotalTunjangan($absen, $kinerja, $kepatuhan, $tunjangan)
    {
        $jumlah = ($absen + $kinerja + $kepatuhan);
        return [
            'persentase' => $jumlah,
            'tunjangan' => ($jumlah * $tunjangan) / 100
        ];
    }

    public function getBawahanLangsung()
    {
        $atasan = $this->pegawai;
        return $this->model->where('nip', '<>', '')
            ->whereHas('jabatan', function ($query) use ($atasan) {
                $query->where('id_atasan', $atasan->id_jabatan);
            })->get();
    }

    public function dataAbsensi($nip, $skpd, $raw_date, $search, $show, $page, $is_mobile = false)
    {
        $date = Carbon::parse($raw_date);
        $user = Pegawai::where('nip', $nip)->first();
        $status_hari = $this->getStatusHariKerja($date);
        $pegawai = Pegawai::with(['checkinout' => function ($query) use ($date) {
            $query->select('nip', 'checktype', 'checktime', 'sn')
                ->whereDate('checktime', '=', $date);
            // $query->select(\DB::raw('DISTINCT(checktype),nip, date(checktime),checktime'))
            //       ->whereDate('checktime','=',$date);
        },
            'kinerja' => function ($query) use ($date) {
                $query->select('nip', 'jenis_kinerja')->where('approve', 2)
                    ->whereDate('tgl_mulai', '<=', $date)
                    ->whereDate('tgl_selesai', '>=', $date);
            }
        ])
            ->leftJoin('jabatan', 'pegawai.id_jabatan', '=', 'jabatan.id')
            ->leftJoin('golongan', 'jabatan.id_golongan', '=', 'golongan.id')
            ->leftJoin('role_pegawai','pegawai.nip','=','role_pegawai.nip_pegawai')
        ;

        try {
            if (in_array($user->role()->pluck('id_role')->max(), $this->special_user_id) == false) {
                if ($user->role()->pluck('id_role')->max() > 5) {
                    $pegawai->whereHas('jabatan', function ($query) use ($user) {
                        $query->where('id_atasan', '=', $user->id_jabatan);
                    });
                } else {
                    $pegawai->where('id_skpd',$user->id_skpd)->where('nip','!=',$user->nip);
                }
            }

            if ($skpd > 0) {
                $pegawai->where('id_skpd', $skpd);
            }

            /*if ($skpd < 0) {
                $pegawai->where('id_jabatan', 3);
            }*/

            if ($search) {
                $pegawai->where(function ($query) use ($search) {
                    $query->where('nip', 'like', '%' . $search . '%')->orWhere('nama', 'like', '%' . $search . '%');
                });
            }

            /* hide bupati, wabup, sekda di monitoring absen */
            $jabatan_hide = Jabatan::where(function($query){
                $query->where('jabatan','LIKE','%Bupati Kolaka%');
                $query->orWhere('jabatan','LIKE','%Sekretaris Daerah%');
                $query->orWhere('jabatan','LIKE','%WAKIL BUPATI%');
            })->select('id')->pluck('id')->all();
            $pegawai->whereNotIn('id_jabatan',$jabatan_hide);

            $pegawai->orderBy('jabatan.tunjangan', 'asc');
            // $pegawai->orderBy('role_pegawai.id_role','asc');
            $pegawai->orderBy('pegawai.nama');
            $data_absen_pegawai = $this->parseAbsensi($pegawai, $date, $status_hari->id_status_hari, $is_mobile)->where('nama','!=','Master Data Admin')->sortBy('role_id')->values();
            $pegawai->orderBy('golongan.tunjangan', 'desc');
            $pegawai->orderBy('role_pegawai.id_role','asc');
            // $pegawai->orderBy('pegawai.nama');
            $data_absen_pegawai = $this->parseAbsensi($pegawai, $date, $status_hari->id_status_hari, $is_mobile)->where('nama','!=','Master Data Admin')->sortBy('urutan')->values();

            $sum = $this->summary($data_absen_pegawai, $raw_date, $status_hari->id_status_hari);
            // dd($data_absen_pegawai);
            if ($page) {
                $data_absen_pegawai = $this->paginateMonitoringAbsen($data_absen_pegawai, $show, $page);
            }
            if (!$is_mobile) {
                return
                    [
                        'data' => [
                            'pegawai' => $data_absen_pegawai,
                            'dayBefore' => Carbon::parse($date)->addDays(-1)->format('m/d/Y'),
                            'dayAfter' => Carbon::parse($date)->addDays(1)->format('m/d/Y'),
                            'today' => Carbon::parse($date)->format('m/d/Y'),
                            'current_date' => Carbon::now()->format('m/d/Y'),
                            'dateString' => ucfirst(Hari::find(date('N', strtotime($date)))->nama_hari) . ' , ' . date('d', strtotime($date)) . ' ' . ucfirst(Bulan::find((int)date('m', strtotime($date)))->nama_bulan) . ' ' . date('Y', strtotime($date)),
                            'jam_masuk_timestamp' => Carbon::parse($raw_date . ' ' . $this->jam_masuk)->toDateTimeString(),
                            'jam_masuk_upacara_timestamp' => Carbon::parse($raw_date . ' ' . $this->jam_masuk_upacara)->toDateTimeString(),
                            'summary' => $sum,
                            'status_hari' => $status_hari
                        ],
                        'diagnostic' => ''
                    ];
            } else {
                $min_date = HariKerja::whereHas('statusHari', function ($query) {
                    $query->where('status_hari', 'kerja');
                })->select('tanggal')->orderBy('tanggal')->first();
                return [
                    'data' => [
                        'pegawai' => $data_absen_pegawai,
                        'min_date' => $min_date->tanggal,
                        'summary' => $sum
                    ],
                    'diagnostic' => ''
                ];
            }
        } catch (Exception $e) {
            return [
                'data' => '',
                'diagnostic' => [
                    'code' => 404,
                    'message' => 'Not Found'
                ]
            ];
        }
    }

    private function getStatusHariKerja($date)
    {
        return DB::table('hari_kerja')->where('tanggal', date('Y-m-d', strtotime($date)))->first();
    }

    private function parseAbsensi($pegawai, $date, $status_hari, $is_mobile)
    {
        $pegawai = $pegawai->get();
        $jam_masuk = $this->jam_masuk;
        $jam_sekarang = date('Y-m-d H:i:s');
        $tanggal_pilihan = $date;
        $mesin_upacara = AbsenUpacara::select('SN')->pluck('SN')->all();

        $data = $pegawai->map(function ($item, $key) use ($jam_masuk, $jam_sekarang, $tanggal_pilihan, $status_hari, $is_mobile, $mesin_upacara) {

            $raw_absensi = $item['checkinout'];
            $absensi = null;

            $tanggal_sekarang = date('Y-m-d', strtotime($jam_sekarang));
            $tanggal_pilihan_date = date('Y-m-d', strtotime($tanggal_pilihan));

            $absen_in = $raw_absensi->contains('checktype', 0) ? $raw_absensi->where('checktype', 0)->min()->checktime : false;
            $absen_out = $raw_absensi->contains('checktype', 1) ? $raw_absensi->where('checktype', 1)->max()->checktime : false;
            $apel = false;
            if ($status_hari == 1) {
                if (strtotime($tanggal_sekarang) > strtotime($tanggal_pilihan_date)) {
                    if ($absen_in && $absen_out) {
                        // if (strtotime($absen_in) <= strtotime($tanggal_pilihan_date . ' ' . $jam_masuk)) {
                            // if ((strtotime($absen_out) - strtotime($absen_in)) >= (8 * 3600)) {
                            //     $absensi = 'hadir';
                            // } else {
                            //     $absensi = 'hadir';
                            // }
                            if (date('N', strtotime($tanggal_pilihan_date)) != 1) {
                                if (strtotime($absen_in) <= strtotime($tanggal_pilihan_date . " 07:30:00")) {
                                    $apel = true;
                                }
                            } else {
                                if ($item->status_upacara) {
                                    if ($absen_upacara = Checkinout::where('nip', $item->nip)->where('checktype', 0)->whereDate('checktime', $tanggal_pilihan_date)->whereIn('sn', $mesin_upacara)->whereTime('checktime', '<=', $this->jam_masuk_upacara)->first()) {
                                        $apel = true;
                                    }
                                }
                            }

                            $absensi = 'hadir';
                        // } else {
                        //     $absensi = 'alpa';
                        // }
                    } else {
                        if ($item['kinerja']->count()) {
                            $absensi = $item['kinerja']->first()->jenis_kinerja;
                        } else {
                            $absensi = 'alpa';
                        }
                    }

                } elseif (strtotime($tanggal_sekarang) == strtotime($tanggal_pilihan_date)) {


                    if (strtotime($jam_sekarang) < strtotime($tanggal_sekarang . ' ' . $jam_masuk) && $raw_absensi->count() < 1) {
                        $absensi = 'uncount';
                    } else {
                        $absensi = 'hadir';
                    }

                    if (strtotime($jam_sekarang) > strtotime($tanggal_sekarang . $jam_masuk)) {
                        if ($absen_in) {
                            if ($absen_out) {
                                $absensi = 'hadir';
                            } else {
                                // $absensi = date('H:i', strtotime($absen_in)).'<span> - </span>';
                                $absensi = 'hadir';
                            }
                            if (date('N', strtotime($tanggal_pilihan_date)) != 1) {
                                if (strtotime($absen_in) <= strtotime($tanggal_pilihan_date . " 07:30:00")) {
                                    $apel = true;
                                }
                            } else {
                                if ($item->status_upacara) {
                                    if ($absen_upacara = Checkinout::where('nip', $item->nip)->where('checktype', 0)->whereDate('checktime', $tanggal_pilihan_date)->whereIn('sn', $mesin_upacara)->whereTime('checktime', '<=', $this->jam_masuk_upacara)->first()) {
                                        $apel = true;
                                    }
                                }
                            }
                        } else {
                            if ($item['kinerja']->count()) {
                                $absensi = $item['kinerja']->first()->jenis_kinerja;
                            } else {
                                $absensi = 'alpa';
                            }
                        }
                    }
                } else {
                    $absensi = 'uncount';
                }
            } else {
                $absensi = 'libur';
            }
            $data['urutan'] = $item->urutan == 0 ? 9999 : $item->urutan;
            if (!$is_mobile) {
                $data['absen_in'] = $absen_in ? date('H:i', strtotime($absen_in)) : '';
                $data['absen_out'] = $absen_out ? date('H:i', strtotime($absen_out)) : '';
                $data['absensi'] = $absensi;
                $data['role_id'] = $item->role->max()? ($item->role->max()->id == 1 ? 99 : $item->role->max()->id ):99;
                $data['nama'] = $item->nama;
                $data['tunjangan'] = $item->jabatan()->first() ? $item->jabatan()->first()->golongan->tunjangan : 0;
                $data['apel'] = $apel;
                $data['nip'] = $item->nip;
                $data['foto'] = $item->foto;
            } else {
                $data['uuid'] = $item->uuid;
                $data['absensi'] = $absensi;
                $data['nama'] = $item->nama;
                $data['apel'] = $apel;
                $data['role_id'] = $item->role->max()? ($item->role->max()->id == 1 ? 99 : $item->role->max()->id ):99;
                $data['tunjangan'] = $item->jabatan()->first() ? $item->jabatan()->first()->golongan->tunjangan : 0;
                $data['nip'] = $item->nip;
                $data['foto'] = $item->foto;
                $data['checkinout'] = [
                    'in' => $absen_in ? $absen_in : '',
                    'out' => $absen_out ? $absen_out : ''
                ];
                $data['kinerja'] = $absensi;
            }

            return $data;

        });
        // dd($data);
        return $data;


    }

    private function summary($pegawai, $date, $status_hari)
    {
        if ($status_hari == 1 && strtotime(date('Y-m-d')) >= strtotime($date)) {
            $hadir = (int)$pegawai->where('absensi', 'hadir')->count();
            $cuti = (int)$pegawai->where('absensi', 'cuti')->count();
            $perjalanan_dinas = (int)$pegawai->where('absensi', 'perjalanan_dinas')->count();
            $izin = (int)$pegawai->where('absensi', 'izin')->count();
            $sakit = (int)$pegawai->where('absensi', 'sakit')->count();
            $alpha = (int)$pegawai->where('absensi', 'alpa')->count();
        } else {
            $hadir = 0;
            $cuti = 0;
            $perjalanan_dinas = 0;
            $izin = 0;
            $sakit = 0;
            $alpha = 0;
        }

        return [
            'hadir' => $hadir,
            'cuti' => $cuti,
            'perjalanan_dinas' => $perjalanan_dinas,
            'izin' => $izin,
            'sakit' => $sakit,
            'alpha' => $alpha,
        ];
    }

    private function paginateMonitoringAbsen($items, $perPage = 6, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator(array_values($items->forPage($page, $perPage)->toArray()), $items->count(), $perPage, $page, $options);
    }
}
