<?php

namespace App\Repositories;


use App\Models\Absen\Checkinout;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\Agama;
use App\Models\MasterData\Bulan;
use App\Models\MasterData\FormulaVariable;
use App\Models\MasterData\Hari;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Jabatan;
use App\Models\MasterData\Pegawai;
use App\Models\MasterData\Skpd;
use App\Models\SkpPegawai;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use PDF;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PegawaiRepository extends BaseRepository
{
    private $special_user = ['Bupati', 'Wakil Bupati', 'Sekretaris Daerah'];
    private $special_user_id = [2, 3, 4];
    private $jam_masuk = '09:00:59';
    private $jam_masuk_upacara = '07.30.59';
    private $status_hari = true;

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
        return $this->count();
    }

    public function getPageMonitoringAbsen($nip,$skpd,$search){
        $user = Pegawai::where('nip',$nip)->first();

        $data = Pegawai::where('nip', '<>', '');

        if (in_array($user->role()->pluck('id_role')->max(), $this->special_user_id) == false) {
            if ($user->role()->pluck('id_role')->max() != 5) {
                $data->whereHas('jabatan', function ($query) use ($user) {
                    $query->where('id_atasan', '=', $user->id_jabatan);
                });
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

    public function getBawahan($nip, $skpd = null)
    {
        $skpd = $skpd ? $skpd : null;
        $user = Pegawai::with('role')->where('nip', $nip)->first();
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
            if ($user->role()->pluck('id_role')->max() != 5) {
                $pegawai->whereHas('jabatan', function ($query) use ($user) {
                    $query->where('id_atasan', '=', $user->id_jabatan);
                });
            }
        }
        return $pegawai->get();
    }

    public function getRekap($nip_user, $nip, $bulan = null, $tahun = null)
    {
        $user = $this->model->with('role')->where('nip', $nip_user)->first();
        $bulan = (int)($bulan ?: date('m'));
        $tahun = ($tahun ?: date('Y'));
        $hari_kerja = HariKerja::where('bulan', $bulan)->where('tahun', $tahun)->whereHas('statusHari', function ($query) {
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

    public function getDetailRekap($nip, $tgl)
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

        /* Data array */
        if ($kinerja) {
            if ($kinerja->jenis_kinerja != 'hadir') {
                $status = $kinerja->jenis_kinerja;
            }
        }
        $result = [
            "kinerja" => $kinerja,
            "checkinout" => $checkinout,
            "tanggal" => $this->formatDate2($tgl),
            "status" => ucwords(str_replace('_', ' ', $status))
        ];

        return [array_merge($result, [
            'prev' => isset($date_prev->tanggal) == false ? '' : $date_prev->tanggal,
            'next' => isset($date_next->tanggal) == false ? '' : $date_next->tanggal
        ])];
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

    public function downloadRekapBulanan($nip,$skpd,$d_id_skpd,$periode_rekap,$download = false)
    {
        // dd($request);
        $bulan = (int)($periode_rekap ? date('m', strtotime($periode_rekap)) : date('m'));
        $tahun = (int)($periode_rekap ? date('Y', strtotime($periode_rekap)) : date('Y'));
        $user = Pegawai::where('nip',$nip)->first();
        $hari_kerja = HariKerja::whereHas('statusHari', function ($query) use ($bulan, $tahun) {
            $query->where('status_hari', 'kerja');
        })->where('bulan', $bulan)->where('tahun', $tahun)->orderBy('tanggal', 'asc')->get();

        $formula = FormulaVariable::all();
        $persen['kinerja'] = $formula->where('variable', 'kinerja')->first()->persentase_nilai;
        $persen['absen'] = $formula->where('variable', 'absen')->first()->persentase_nilai;

        $pegawai = $this->getDataPegawai($user, $bulan, $tahun, $d_id_skpd);

        $data = $this->parseDataRekap($pegawai, $persen, $hari_kerja);

        $skpd = Skpd::where('id', $skpd)->first();
        $namaSkpd = $skpd ? $skpd->nama_skpd : 'PEMERINTAH KABUPATEN KOLAKA';
        $periode = ucfirst(Bulan::find((int)date('m', strtotime($periode_rekap)))->nama_bulan . ' ' . date('Y', strtotime($periode_rekap)));
        $tanggal_cetak = date('d') . ' ' . ucfirst(Bulan::find((int)date('m'))->nama_bulan) . ' ' . date('Y');
        $pdf = PDF::loadView('pdf.rekap-bulanan', compact('data', 'namaSkpd', 'periode', 'tanggal_cetak'));
        $pdf->setPaper('legal', 'landscape');
        if ($download){
            return $pdf->download('rekap_bulanan.pdf');
        }
        return $pdf->stream('rekap_bulanan.pdf');
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
            if ($user->role()->pluck('id_role')->max() != 5) {
                $pegawai->whereHas('jabatan', function ($query) use ($user) {
                    $query->where('id_atasan', '=', $user->id_jabatan);
                });
            }
        }

        $pegawai = $pegawai->with(
            [

                'checkinout' => function ($query) use ($bulan, $tahun) {
                    $query->select('nip', 'checktime', 'checktype')->whereMonth('checktime', $bulan)->whereYear('checktime', $tahun);
                },
                'kinerja' => function ($query) use ($bulan, $tahun) {
                    $query->select('nip', 'approve', 'jenis_kinerja', 'tgl_mulai', 'tgl_selesai')->whereMonth('tgl_mulai', $bulan)->whereYear('tgl_mulai', $tahun);
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
            $data['kinerja'] = $raw_kinerja->sum('kinerja');
            $data['persentase_kinerja'] = ((($data['kinerja'] / $hari_kerja->count()) * 100) * $persen['kinerja']) / 100;
            $data['absen'] = $raw_absen->count() + $tambahan_absen;
            $data['persentase_absen'] = ((($data['absen'] / $hari_kerja->count()) * 100) * $persen['absen']) / 100;
            $total = $this->calculateTotalTunjangan($data['persentase_absen'], $data['persentase_kinerja'], $tunjangan);
            $data['total_tunjangan'] = $total['tunjangan'];
            $data['total_persentase'] = $total['persentase'];
            return $data;
        });
    }

    private function parseKinerja($item, $key, $hari_kerja)
    {
        return $item->kinerja->map(function ($itemkinerja, $keykinerja) use ($key, $hari_kerja) {
            $kinerja = $absen_tambahan = 0;
            if ($itemkinerja->wherein('tgl_mulai', $hari_kerja->pluck('tanggal')) && $itemkinerja->approve == 2) {
                $kinerja = 1;
            }
            if ($itemkinerja->wherein('tgl_mulai', $hari_kerja->pluck('tanggal')) && $itemkinerja->approve == 2 && $itemkinerja->jenis_kinerja <> 'hadir') {
                $absen_tambahan = 1;
            }
            return collect(['kinerja' => $kinerja, 'absen_tambahan' => $absen_tambahan]);
        })->filter(function ($value, $key) {
            return $value->filter(function ($v, $k) {
                return $v > 0;
            });
        });
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
                    if (strtotime($masuk->first()->checktime) <= strtotime(date('Y-m-d', strtotime($masuk->first()->checktime)) . " 08:00:00")) {
                        if ((strtotime($keluar->first()->checktime) - strtotime($masuk->first()->checktime)) >= (8 * 3600)) {
                            return 1;
                        } else {
                            return 0.2;
                        }
                    } else if (strtotime($masuk->first()->checktime) <= strtotime(date('Y-m-d', strtotime($masuk->first()->checktime)) . " 08:30:00")) {
                        if ((strtotime($keluar->first()->checktime) - strtotime($masuk->first()->checktime)) >= (8 * 3600)) {
                            return 0.8;
                        } else {
                            return 0.2;
                        }
                    } else if (strtotime($masuk->first()->checktime) <= strtotime(date('Y-m-d', strtotime($masuk->first()->checktime)) . " 09:00:00")) {
                        if ((strtotime($keluar->first()->checktime) - strtotime($masuk->first()->checktime)) >= (8 * 3600)) {
                            return 0.6;
                        } else {
                            return 0.2;
                        }
                    } else if (strtotime($masuk->first()->checktime) <= strtotime(date('Y-m-d', strtotime($masuk->first()->checktime)) . " 09:30:00")) {
                        if ((strtotime($keluar->first()->checktime) - strtotime($masuk->first()->checktime)) >= (8 * 3600)) {
                            return 0.4;
                        } else {
                            return 0.2;
                        }
                    }
                }
            }
        })->filter(function ($value, $key) {
            return $value > 0;
        });
    }

    private function calculateTotalTunjangan($absen, $kinerja, $tunjangan)
    {
        $jumlah = ($absen + $kinerja);
        return [
            'persentase' => $jumlah,
            'tunjangan' => (floor($jumlah) * $tunjangan) / 100
        ];
    }

    public function dataAbsensi($nip,$skpd,$raw_date,$search,$show,$page)
    {
        $date = Carbon::parse($raw_date);
        $user = Pegawai::where('nip',$nip)->first();
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
        ])->leftJoin('jabatan', 'pegawai.id_jabatan', '=', 'jabatan.id')
            ->leftJoin('golongan', 'jabatan.id_golongan', '=', 'golongan.id');

        try {
            if (in_array($user->role()->pluck('id_role')->max(), $this->special_user_id) == false) {
                if ($user->role()->pluck('id_role')->max() != 5) {
                    $pegawai->whereHas('jabatan', function ($query) use ($user) {
                        $query->where('id_atasan', '=', $user->id_jabatan);
                    });
                }
            }

            if ($skpd > 0) {
                $pegawai->where('id_skpd', $skpd);
            }

            if ($skpd < 0) {
                $pegawai->where('id_jabatan', 3);
            }

            if ($search) {
                $pegawai->where(function ($query) use ($search) {
                    $query->where('nip', 'like', '%' . $search . '%')->orWhere('nama', 'like', '%' . $search . '%');
                });
            }

            $pegawai->orderBy('golongan.tunjangan', 'desc');
            $pegawai->orderBy('pegawai.nama');
            $data_absen_pegawai = $this->parseAbsensi($pegawai, $date, $status_hari->id_status_hari);
            $sum = $this->summary($data_absen_pegawai, $raw_date, $status_hari->id_status_hari);
            $total = (int)$data_absen_pegawai->count();

            $data_absen_pegawai = $this->paginateMonitoringAbsen($data_absen_pegawai, $show, $page);

            return
                [
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
                ];
        } catch (Exception $e) {
            throw new NotFoundHttpException('Not Found');
        }
    }

    private function getStatusHariKerja($date)
    {
        return DB::table('hari_kerja')->where('tanggal', date('Y-m-d', strtotime($date)))->first();
    }

    private function parseAbsensi($pegawai, $date, $status_hari)
    {
        $pegawai = $pegawai->get();

        $jam_masuk = $this->jam_masuk;
        $jam_sekarang = date('Y-m-d H:i:s');
        $tanggal_pilihan = $date;

        $data = $pegawai->map(function ($item, $key) use ($jam_masuk, $jam_sekarang, $tanggal_pilihan, $status_hari) {
            $data['absen_in'] = '';
            $data['absen_out'] = '';

            $raw_absensi = $item['checkinout'];
            $absensi = null;

            $tanggal_sekarang = date('Y-m-d', strtotime($jam_sekarang));
            $tanggal_pilihan_date = date('Y-m-d', strtotime($tanggal_pilihan));

            $absen_in = $raw_absensi->contains('checktype', 0) ? $raw_absensi->where('checktype', 0)->min()->checktime : false;
            $absen_out = $raw_absensi->contains('checktype', 1) ? $raw_absensi->where('checktype', 1)->max()->checktime : false;

            if ($status_hari == 1) {
                if (strtotime($tanggal_sekarang) > strtotime($tanggal_pilihan_date)) {
                    if ($absen_in && $absen_out) {
                        if (strtotime($absen_in) <= strtotime($tanggal_pilihan_date . ' ' . $jam_masuk)) {
                            if ((strtotime($absen_out) - strtotime($absen_in)) >= (8 * 3600)) {
                                $absensi = 'hadir';
                            } else {
                                $absensi = 'alpa';
                            }
                        } else {
                            $absensi = 'alpa';
                        }
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

            $data['absen_in'] = $absen_in ? date('H:i', strtotime($absen_in)) : '';
            $data['absen_out'] = $absen_out ? date('H:i', strtotime($absen_out)) : '';
            $data['absensi'] = $absensi;
            $data['nama'] = $item->nama;
            $data['nip'] = $item->nip;
            $data['foto'] = $item->foto;

            return $data;

        });

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

    static function getBawahanPenilaianKinerja($nip,$date){
        $user = Pegawai::where('nip',$nip)->first();
        $pegawai = Pegawai::wherehas('jabatan', function ($query) use ($user) {
            $query->where('id_atasan', '=', $user->id_jabatan);
        })->with(['kinerja' => function ($query) use ($date) {
            $query->whereDate('tgl_mulai', '<=', $date ? $date : date('Y-m-d'));
            $query->whereDate('tgl_mulai', '>=', $date ? $date : date('Y-m-d'));
            $query->terbaru();
        }])->get();
        return $pegawai;
    }

    static function getKinerjaPenilaianKinerja($nip,$date){
        $pegawai = Pegawai::where('nip', $nip)->first();
        $old_kinerja = Kinerja::where('nip', $pegawai->nip)
            ->with('skp_pegawai.skpTask','media')
            ->where('approve', 0)
            ->whereMonth('tgl_mulai', date('m'))
            ->whereDate('tgl_mulai', '<', date('Y-m-d'))
            ->get();
        $kinerja = Kinerja::where('nip', $pegawai->nip)
            ->with('skp_pegawai.skpTask','media')
            ->whereDate('tgl_mulai', '<=', $date? $date : date('Y-m-d'))
            ->whereDate('tgl_mulai', '>=', $date ? $date : date('Y-m-d'))
            ->terbaru()
            ->first();
        return [
            'now' => $kinerja,
            'old' => $old_kinerja->pluck('tgl_mulai')->toArray()
        ];
    }

    static function replyKinerjaPenilaianKinerja(array $param){
//        try {
        $kinerja = Kinerja::with('skp_pegawai')->find($param['id']);
        $kinerja->keterangan_approve = $param['keterangan_approve'];
        $kinerja->approve = $param['type'];
        $kinerja->nilai_kinerja = $param['rate'];
        $kinerja->save();
        SkpPegawai::whereHas('kinerja',function ($q)use($param){
            $q->where('kinerja.id',$param['id']);
        })->update([
            'status' => 0
        ]);
        if (isset($param['skp_pegawai'])){
            foreach ($param['skp_pegawai'] AS $key => $value) {
                SkpPegawai::whereHas('kinerja',function ($q)use($param){
                    $q->where('kinerja.id',$param['id']);
                })->where('skp_pegawai.id',$key)->update([
                    'status' => 1
                ]);
            }
        }
        return ['status' => 'HTTP_OK'];
//        } catch (Exception $e) {
//            throw new ModelNotFoundException('Kinerja Tidak Ditemukan');
//        }
    }

}
