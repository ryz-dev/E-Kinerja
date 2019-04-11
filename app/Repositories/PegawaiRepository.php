<?php

namespace App\Repositories;


use App\Models\Absen\Checkinout;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\Agama;
use App\Models\MasterData\Bulan;
use App\Models\MasterData\FormulaVariable;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Jabatan;
use App\Models\MasterData\Pegawai;
use App\Models\MasterData\Skpd;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PDF;

class PegawaiRepository extends BaseRepository
{
    private $special_user = ['Bupati', 'Wakil Bupati', 'Sekretaris Daerah'];
    private $special_user_id = [2, 3, 4];
    public $pegawai;

    public function __construct($nip = null){
        parent::__construct();

        if ($nip) {
            $this->pegawai = $this->model::where('nip',$nip)->orWhere('uuid',$nip)->first();
        }
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

    public function updatePassword($nip, $password)
    {
        $pegawai = $this->model->whereNip($nip)->first();
        $pegawai->password = bcrypt($password);
        return $pegawai->save();
    }

    public function getBawahan($nip, $skpd = null)
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

    public function downloadRekapBulanan(Request $request)
    {
        // dd($request);
        $periode_rekap = $request->input('periode_rekap') ? $request->input('periode_rekap') : date('Y-m-d');
        $bulan = (int)($periode_rekap ? date('m', strtotime($periode_rekap)) : date('m'));
        $tahun = (int)($periode_rekap ? date('Y', strtotime($periode_rekap)) : date('Y'));
        $user = Auth::user();
        $hari_kerja = HariKerja::whereHas('statusHari', function ($query) use ($bulan, $tahun) {
            $query->where('status_hari', 'kerja');
        })->where('bulan', $bulan)->where('tahun', $tahun)->orderBy('tanggal', 'asc')->get();

        $formula = FormulaVariable::all();
        $persen['kinerja'] = $formula->where('variable', 'kinerja')->first()->persentase_nilai;
        $persen['absen'] = $formula->where('variable', 'absen')->first()->persentase_nilai;
        $pegawai = $this->getDataPegawai($user, $bulan, $tahun, $request->input('id_skpd'));

        $data = $this->parseDataRekap($pegawai, $persen, $hari_kerja);
        $skpd = Skpd::find($request->id_skpd);
        $namaSkpd = $skpd ? $skpd->nama_skpd : 'PEMERINTAH KABUPATEN KOLAKA';
        $periode = ucfirst(Bulan::find((int)date('m', strtotime($periode_rekap)))->nama_bulan . ' ' . date('Y', strtotime($periode_rekap)));
        $tanggal_cetak = date('d') . ' ' . ucfirst(Bulan::find((int)date('m'))->nama_bulan) . ' ' . date('Y');
        $pdf = PDF::loadView('pdf.rekap-bulanan', compact('data', 'namaSkpd', 'periode', 'tanggal_cetak'));
        $pdf->setPaper('legal', 'landscape');

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

    public function getBawahanLangsung()
    {
        $atasan = $this->pegawai;
        return $this->model->where('nip','<>','')
                           ->whereHas('jabatan', function($query) use($atasan){
                            $query->where('id_atasan',$atasan->id_jabatan);
                           })->get();
    }

    public static function dataPegawai($pegawai){
        return app()->make(self::model())->where('nip',$pegawai)->orWhere('uuid', $pegawai)->first();
    }
}
