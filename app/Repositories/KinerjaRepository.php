<?php

namespace App\Repositories;


use App\Models\Absen\Checkinout;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\AbsenUpacara;
use App\Models\MasterData\FormulaVariable;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use App\Models\Media;
use App\Models\SkpPegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KinerjaRepository extends BaseRepository
{
    private $jam_masuk_upacara = '07.30.59';

    static function getBawahanPenilaianKinerja($nip, $date, $search = '')
    {
        $id_jabatan = collect(self::populateJabatan($nip, $bawahan = 1))->unique('id_jabatan')->pluck('id_jabatan')->sort()->all();
        $pegawai = Pegawai::where(function ($query) use ($search) {
            if ($search !== '') {
                $query->where('nip', 'like', '%' . $search . '%')->orWhere('nama', 'like', '%' . $search . '%');
            }
        })->whereIn('id_jabatan', $id_jabatan)->with(['kinerja' => function ($query) use ($date) {
            $query->whereDate('tgl_mulai', '<=', $date ? $date : date('Y-m-d'));
            $query->whereDate('tgl_mulai', '>=', $date ? $date : date('Y-m-d'));
            $query->terbaru();
        }])->get();
        return $pegawai;
    }

    static function populateJabatan($nip, $bawahan)
    {
        $pegawai = self::collectJabatanBawahan($nip, $bawahan);
        $bawahan = self::mergePegawai($pegawai, []);
        return $bawahan;
    }

    static function mergePegawai($pegawai, $all)
    {
        $data = array_merge($pegawai, $all);
        foreach ($pegawai AS $row) {
            $data = self::mergePegawai($row['bawahan'], $data);
            unset($row['bawahan']);
        }
        return $data;
    }

    static function getAllBawahan($nip, $date, $search)
    {
        $user = Pegawai::where('nip', $nip)->first();
        $pegawai = Pegawai::where(function ($query) use ($search) {
            if ($search !== '') {
                $query->where('nip', 'like', '%' . $search . '%')->orWhere('nama', 'like', '%' . $search . '%');
            }
        })->wherehas('jabatan', function ($query) use ($user) {
            $query->where('id_atasan', '=', $user->id_jabatan);
        })->with(['kinerja' => function ($query) use ($date) {
            $query->whereDate('tgl_mulai', '<=', $date ? $date : date('Y-m-d'));
            $query->whereDate('tgl_mulai', '>=', $date ? $date : date('Y-m-d'));
            $query->terbaru();
        }])->get()->map(function ($val) use ($date, $search) {
            $val->bawahan = self::getBawahanPenilaianKinerja($val->nip, $date, $search);
            return $val;
        });
        return $pegawai;
    }

    static function collectJabatanBawahan($nip, $count_bawahan)
    {
        if ($count_bawahan <= 2) {
            $user = Pegawai::select('nip', 'id_jabatan')->where('nip', $nip)->first();
            $pegawai = Pegawai::select('nip', 'id_jabatan')->wherehas('jabatan', function ($query) use ($user) {
                $query->where('id_atasan', '=', $user->id_jabatan);
            })->get()->map(function ($val) use ($count_bawahan) {
                $val->setAppends([]);
                $val->bawahan = self::populateJabatan($val->nip, ++$count_bawahan);
                return $val;
            });
            return $pegawai->toArray();
        }
        return [];
    }

    static function getKinerjaPenilaianKinerja($nip, $date)
    {
        $pegawai = Pegawai::with('role')->where('nip', $nip)->first();
        $role = $pegawai->role->map(function($val){
            return $val->nama_role;
        });
        $kepatuhan_input = null;
        if ($role->isNotEmpty()) {
            $kepatuhan_input = [
                'bmd' => 'BMD (Barang Milik Daerah)',
                'tptgr' => 'TBTGR (Tuntutan Bendahara dan Tuntutan Ganti Rugi)'
            ];
            if (!in_array('Staf', $role->toArray())) {
                $kepatuhan_input = array_merge($kepatuhan_input, ['lkpn' => 'LKPN (Laporan kekayaan Penyelenggara Negara), namun untuk staff tidak dapat melakukan penilaian ini']);
            }
        }
        $kepatuhan = new KepatuhanRepository($nip);
        $old_kinerja = Kinerja::where('nip', $pegawai->nip)
            ->with(['skp_pegawai' => function ($query) {
                $query->select('skp_pegawai.id', 'id_skp', 'nip_pegawai', 'periode', 'status', 'nip_update');
                $query->with(['skpTask' => function ($query) {
                    $query->select('id', 'task');
                }]);
            }, 'media' => function ($query) {
                $query->select('media', 'nama_media', 'id_kinerja');
            }])
            ->where('approve', 0)
            ->whereMonth('tgl_mulai', date('m'))
            ->whereDate('tgl_mulai', '<', date('Y-m-d'))
            ->get();
        $kinerja = Kinerja::where('nip', $pegawai->nip)
            ->with(['skp_pegawai' => function ($query) {
                $query->select('skp_pegawai.id', 'id_skp', 'nip_pegawai', 'periode', 'status', 'nip_update');
                $query->with(['skpTask' => function ($query) {
                    $query->select('id', 'task');
                }]);
            }, 'media' => function ($query) {
                $query->select('media', 'nama_media', 'id_kinerja');
            }])
            ->whereDate('tgl_mulai', '<=', $date ? $date : date('Y-m-d'))
            ->whereDate('tgl_mulai', '>=', $date ? $date : date('Y-m-d'))
            ->terbaru()
            ->first();
        return [
            'kepatuhan_input' => $kepatuhan_input,
            'kepatuhan' => $kepatuhan->getKepatuhan(),
            'now' => $kinerja,
            'old' => $old_kinerja->pluck('tgl_mulai')->toArray()
        ];
    }

    static function replyKinerjaPenilaianKinerja(array $param)
    {
//        try {
        $kinerja = Kinerja::with('skp_pegawai')->find($param['id']);
        $kinerja->keterangan_approve = $param['keterangan_approve'];
        $kinerja->approve = $param['type'];
        $kinerja->nilai_kinerja = $param['nilai_kinerja'];
        $kinerja->save();

        $kinerja->skp_pegawai()->pluck('skp_pegawai.id')->diff(isset($param['skp_pegawai']) ? $param['skp_pegawai'] : [])->map(function ($id) use ($param) {
            if ($update = SkpPegawai::whereHas('kinerja', function ($q) use ($param) {
                $q->where('kinerja.id', $param['id']);
            })->where('id', $id)->where('status', 1)->first()) {
                $update->update([
                    'status' => 0,
                    'tanggal_selesai' => null,
                    'nip_update' => null
                ]);
            }
        });
        if (isset($param['skp_pegawai'])) {
            foreach ($param['skp_pegawai'] AS $id) {
                if ($update = SkpPegawai::whereHas('kinerja', function ($q) use ($param) {
                    $q->where('kinerja.id', $param['id']);
                })->where('id', $id)->where('status', 0)->first())
                    $update->update([
                        'status' => 1,
                        'tanggal_selesai' => date('Y-m-d H:i:s'),
                        'nip_update' => Auth::user()->nip
                    ]);
            }
        }
        return ['status' => 'HTTP_OK'];
//        } catch (Exception $e) {
//            throw new ModelNotFoundException('Kinerja Tidak Ditemukan');
//        }
    }

    public function model()
    {
        return 'App\Models\Absen\Kinerja';
    }

    public function search(array $parameters, $perPage = 10)
    {

        if (!empty($parameters['q'])) {
            $this->where('nip', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->where('tgl_mulai', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->where('tgl_selesai', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->where('jenis_kinerja', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->where('rincian_kinerja', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->model = $this->model->orWhereHas('pegawai', function ($query) use ($parameters) {
                $query->where('nama', $parameters['q']);
            });
        }

        return $this->paginate($perPage);
    }

    public function getPage(array $parameters)
    {
        if (!empty($parameters['q'])) {
            $this->where('nip', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->where('tgl_mulai', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->where('tgl_selesai', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->where('jenis_kinerja', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->where('rincian_kinerja', 'like', '%' . $parameters['q'] . '%', 'or');
            $this->model = $this->model->orWhereHas('pegawai', function ($query) use ($parameters) {
                $query->where('nama', $parameters['q']);
            });
        }

        return $this->count();
    }

    public function getKinerjaTersimpan($nip)
    {
        return $this->model->with('skp_pegawai')->where('nip', $nip)->where('tgl_mulai', date('Y-m-d'))->where('jenis_kinerja', 'hadir')->where('approve', '5')->first();
    }

    public function deleteKinerjaTersimpan($id, $nip)
    {
        if ($cek_kinerja = $this->model->where('nip', $nip)->where('jenis_kinerja', 'hadir')->where('approve', '5')->where('tgl_mulai', date('Y-m-d'))->find($id)) {
            return $cek_kinerja->delete() ? true : false;
        }
        return false;
    }

    public function inputKinerja(array $input, $nip)
    {
        if (in_array($input['jenis_kinerja'], ['hadir', 'sakit'])) {
            $input['tgl_mulai'] = date('Y-m-d');
            $input['tgl_selesai'] = date('Y-m-d');
        } else {
            $tgl_mulai = explode('/', $input['tgl_mulai']);
            $tgl_selesai = explode('/', $input['tgl_selesai']);

            $input['tgl_mulai'] = $tgl_mulai[2] . '-' . $tgl_mulai[0] . '-' . $tgl_mulai[1];
            $input['tgl_selesai'] = $tgl_selesai[2] . '-' . $tgl_selesai[0] . '-' . $tgl_selesai[1];
            if (strtotime($input['tgl_mulai']) > strtotime($input['tgl_selesai'])) {
                return [
                    'data' => '',
                    'diagnostic' => [
                        'code' => '403',
                        'message' => 'gagal menambah kinerja, tanggal berakhir lebih kecil dari tanggal mulai'
                    ]
                ];
            }
        }
        $input['nip'] = $nip;
        $input['nilai_kinerja'] = 0;
//        $cek_kinerja = $this->>model->where('nip',$input['nip'])->where('tgl_mulai','<=',$input['tgl_mulai'])->where('tgl_selesai','>=',$input['tgl_selesai'])->whereIn('approve',[0,2])->first();
        $cek_kinerja = $this->model->where('nip', $nip)->where(function ($query) use ($input) {
            $query->where(function ($query) use ($input) {
                $query->where('tgl_mulai', '<=', $input['tgl_mulai']);
                $query->where('tgl_selesai', '>=', $input['tgl_mulai']);
            });
            $query->orWhere(function ($query) use ($input) {
                $query->where('tgl_mulai', '<=', $input['tgl_selesai']);
                $query->where('tgl_selesai', '>=', $input['tgl_selesai']);
            });
        })->whereIn('approve', [0, 2])->first();
        if (!$cek_kinerja) {
            $input['approve'] = 0;
            if ($input['jenis_kinerja'] == 'hadir') {
                $cek_hari_kerja = HariKerja::whereDate('tanggal', date('Y-m-d'))->first();
                if ($cek_hari_kerja) {
                    $cek_hadir_kerja = Checkinout::whereDate('checktime', date('Y-m-d'))->where('checktype', '0')->where('nip', $nip)->first();
                    $cek_pulang_kerja = Checkinout::whereDate('checktime', date('Y-m-d'))->where('checktype', '1')->where('nip', $nip)->first();
                    /*if (strtotime($cek_hadir_kerja->checktime) <= strtotime(date('Y-m-d')." 09:00:00")){
                        if ((strtotime($cek_pulang_kerja->checktime) - strtotime($cek_hadir_kerja->checktime)) >= (8 * 3600)) {*/
                    /*if (isset($input['status'])) {
                        if ($input['status'] == 5) {
                            $input['approve'] = 5;
                        }
                    }*/
                    if (isset($input['id'])) {
                        $kinerja = $this->model->where('nip', $nip)->where('jenis_kinerja', 'hadir')->findOrFail($input['id']);
                        $kinerja->update([
                            'rincian_kinerja' => $input['rincian_kinerja'],
                            'approve' => $input['approve']
                        ]);
                    } else {
                        $kinerja = $this->model->create($input);
                    }
                    Log::info(json_encode($input) . "1");
                    Log::info(isset($input['skp_pegawai']) ? 'true' : 'false');
                    if (isset($input['skp_pegawai'])) {
                        if (is_array($input['skp_pegawai'])) {
                            if (count($input['skp_pegawai']) > 0) {
                                $cek = true;
                                foreach ($input['skp_pegawai'] AS $id) {
                                    if (!$kinerja->whereHas('skp_pegawai', function ($query) use ($id) {
                                        $query->where('id_skp', $id);
                                    })->first()) {
                                        try {
                                            $kinerja->skp_pegawai()->attach($id, ['uuid' => (string)Str::uuid()]);
                                        } catch (\Exception $exception) {
                                            $cek = false;
                                            break;
                                        }
                                    }
                                }
                                if (!$cek) {
                                    $kinerja->delete();
                                    return [
                                        'data' => '',
                                        'diagnostic' => [
                                            'code' => 403,
                                            'status' => 'ERROR',
                                            'message' => 'skp_pegawai tidak valid'
                                        ]
                                    ];
                                } else {
                                    $kinerja->load('skp_pegawai');
                                }
                            }
                        }
                    }
                    return [
                        'data' => $kinerja,
                        'diagnostic' => '',
                    ];
                    /*}
                } else {
                    return response()->json([
                            'diagnostic' => [
                                'code' => '403',
                                'message' => 'gagal menambah kinerja, tidak tercata hadir pada absen'
                            ]
                        ]);
                }*/
                }

                return [
                    'data' => '',
                    'diagnostic' => [
                        'code' => '403',
                        'message' => 'gagal menambah kinerja, bukan hari kerja'
                    ]
                ];
            } else {
                $cek_kinerja = $this->model->where('nip', $nip)->where(function ($query) use ($input) {
                    $query->where(function ($query) use ($input) {
                        $query->where('tgl_mulai', '<=', $input['tgl_mulai']);
                        $query->where('tgl_selesai', '>=', $input['tgl_mulai']);
                    });
                    $query->orWhere(function ($query) use ($input) {
                        $query->where('tgl_mulai', '<=', $input['tgl_selesai']);
                        $query->where('tgl_selesai', '>=', $input['tgl_selesai']);
                    });
                })->whereIn('approve', [5])->first();
                if ($cek_kinerja) {
                    return [
                        'data' => '',
                        'diagnostic' => [
                            'code' => '403',
                            'message' => 'gagal menambahkan kinerja, sdh ada kinerja yang disimpan untuk hari ini'
                        ]
                    ];
                }
                $kinerja = $this->model->create($input);
                return [
                    'data' => $kinerja,
                    'diagnostic' => '',
                ];
            }
        } else {
            return [
                'data' => '',
                'diagnostic' => [
                    'code' => '403',
                    'message' => 'gagal menambahkan kinerja, sdh ada kinerja pada hari yang sama untuk user ini'
                ]
            ];
        }
    }

    public function getTunjanganKinerja($nip, $bulan = null, $tahun = null, $is_mobile = false, $detail = false)
    {
        $bulan = (int)($bulan ? $bulan : date('m'));
        $tahun = $tahun ? $tahun : date('Y');

        $persen_absen = FormulaVariable::select('persentase_nilai')->where('variable', 'absen')->first()->persentase_nilai;
        $persen_kinerja = FormulaVariable::select('persentase_nilai')->where('variable', 'kinerja')->first()->persentase_nilai;
        $persen_kepatuhan = FormulaVariable::select('persentase_nilai')->where('variable', 'kepatuhan')->first()->persentase_nilai;

        $pegawai = Pegawai::select('nip', 'id_jabatan')->with(['jabatan' => function ($query) {
            $query->select('id', 'id_golongan');
            $query->with(['golongan' => function ($query) {
                $query->select('id', 'tunjangan');
            }]);
        }])->where('nip', $nip)->first();
        $nip = $pegawai->nip;
        $pegawai->setAppends([]);
        $pegawai->jabatan->setAppends([]);
        $pegawai->jabatan->golongan->setAppends([]);
        $jumlah_tunjangan = $pegawai->jabatan->golongan->tunjangan;
        $kepatuhan = new KepatuhanRepository($nip);
        $data_kepatuhan = $kepatuhan->getKepatuhan();
        $jumlah_kepatuhan = 0;
        $pembagi_kepatuhan = 2;
        if ($data_kepatuhan){
            $data_kepatuhan->tanggal_periode = formatDate3($data_kepatuhan->periode);

            $jumlah_kepatuhan += $data_kepatuhan->bmd;
            $jumlah_kepatuhan += $data_kepatuhan->tptgr;

        } else {
            $data_kepatuhan = new \stdClass();
        }
        $data_kepatuhan->list_kepatuhan = [
            'bmd' => 'BMD (Barang Milik Daerah)',
            'tptgr' => 'TBTGR (Tuntutan Bendahara dan Tuntutan Ganti Rugi)'
        ];
        if (!$pegawai->role->contains('nama_role','Staf')){
            $pembagi_kepatuhan = 3;
            $jumlah_kepatuhan += $data_kepatuhan->lkpn;
            $data_kepatuhan->list_kepatuhan = array_merge($data_kepatuhan->list_kepatuhan, ['lkpn' => 'LKPN (Laporan kekayaan Penyelenggara Negara), namun untuk staff tidak dapat melakukan penilaian ini']);
        }

        $min_date = HariKerja::whereHas('statusHari', function ($query) {
            $query->where('status_hari', 'kerja');
        })->select('tanggal')->orderBy('tanggal')->first();
        $hari_kerja = HariKerja::with('Hari')->select('tanggal', 'hari')->whereHas('statusHari', function ($query) {
            $query->where('status_hari', 'kerja');
        })->where('bulan', $bulan)->where('tahun', $tahun)->where('tanggal', '<=', date('Y-m-d'))->orderBy('tanggal', 'asc')->get();
        $jumlah_hari = $hari_kerja->count();
        $jumlah_kinerja = $absen = $nilai_apel = 0;
        $data_kinerja = [];
        $persen_apel = 0;
        if ($jumlah_hari > 0) {
            foreach ($hari_kerja AS $hk) {
                $apel = false;
                $knj = $this->model->where('nip', $nip)->where('tgl_mulai', '<=', $hk->tanggal)->where('tgl_selesai', '>=', $hk->tanggal)->terbaru();
                $abs = Checkinout::select('checktime', 'checktype')->where('nip', $nip)->whereDate('checktime', $hk->tanggal)->orderBy('checktype', 'asc')->get();
                $status = '';
                $is_upacara = false;
                if (date('N', strtotime($hk->tanggal)) == 1) {
                    $is_upacara = true;
                }
                $wajib_upacara = $pegawai->status_upacara ? true : false;
                $upacara = false;
                if ($is_upacara) {
                    if ($wajib_upacara) {
                        $mesin_upacara = AbsenUpacara::select('SN')->pluck('SN')->all();
                        if ($absen_upacara = Checkinout::where('nip', $pegawai->nip)->where('checktype', 0)->whereDate('checktime', $hk->tanggal)->whereIn('sn', $mesin_upacara)->whereTime('checktype', '<=', $this->jam_masuk_upacara)->first()) {
                            $upacara = true;
                        }
                    }
                }
                $masuk = $pulang = null;
                if ($abs->count() > 0) {
                    $in = false;
                    $out = false;
                    foreach ($abs AS $a) {
                        if ($a->checktype == '0') {
                            $in = true;
                            $masuk = $a->checktime;
                        }
                        if ($a->checktype == '1') {
                            $out = true;
                            $pulang = $a->checktime;
                        }
                    }
                    if ($in) {
                        $status = '';
                    } else {
                        $status = 'alpa';
                    }
                    $poin = 0;
                    if ($in && $out) {
                        $poin = $this->poinAbsen($masuk, $pulang, $hk->tanggal);
                        $status = 'hadir';
                    }
                    $absen = (float)$absen + $poin;
                }
                if (strtotime($hk->tanggal) < strtotime(date('Y-m-d'))) {
                    if ($status == '') {
                        $status = 'alpa';
                    }
                }
                if ($status != 'alpa') {
                    if ($masuk != null) {
                        if (strtotime($masuk) <= strtotime($hk->tanggal . " 07:30:00")) {
                            $apel = true;
                        }
                    }
                    if ($is_upacara && $wajib_upacara) {
                        $apel = $upacara;
                    }
                }
                if ($apel) {
                    $nilai_apel++;
                }
                if ($detail) {
                    if ($is_mobile) {
                        $nilai_kinerja = null;
                        if ($dk = $knj->first()) {
                            if ($dk->approve == 2) {
                                $nilai_kinerja = $dk->nilai_kinerja;
                            }
                            if ($dk->jenis_kinerja != 'hadir') {
                                $status = $dk->jenis_kinerja;
                            }
                        }
                        $dabsen = [
                            'in' => '',
                            'out' => '',
                        ];
                        if ($abs) {
                            $dabsen['in'] = ($abs->where('checktype', 0)->first() ? $abs->where('checktype', 0)->first()->checktime : '');
                            $dabsen['out'] = ($abs->where('checktype', 1)->first() ? $abs->where('checktype', 1)->first()->checktime : '');
                        }

                        $data_kinerja[] = [
                            'tanggal' => $hk->tanggal,
//                            'tanggal_string' => $this->formatDate($hk->tanggal),
//                            'tanggal_string2' => $this->formatDate2($hk->tanggal),
                            'hari' => ucfirst($hk->Hari->nama_hari),
                            'kinerja' => $nilai_kinerja,
//                            'absen' => $dabsen,
                            'status' => $status,
                            'apel' => $apel
                        ];
                    } else {
                        $data_kinerja[] = [
                            'tanggal' => $hk->tanggal,
                            'tanggal_string' => $this->formatDate($hk->tanggal),
                            'tanggal_string2' => $this->formatDate2($hk->tanggal),
                            'hari' => ucfirst($hk->Hari->nama_hari),
                            'kinerja' => $knj->first() ? $knj->with('skp_pegawai.skpTask', 'media')->first()->toArray() : null,
                            'absen' => $abs ? $abs->toArray() : null,
                            'status' => ucfirst($status),
                            'apel' => $apel
                        ];
                    }
                }

                if ($row_kinerja = $knj->where('approve', 2)->first()) {
                    $jumlah_kinerja += $row_kinerja->nilai_kinerja;
                }
                if ($knj->where('jenis_kinerja', '<>', 'hadir')->where('approve', 2)->first()) {
                    $absen++;
                }
            }
            if (($jumlah_hari - $nilai_apel) < 6) {
                $persen_apel = 100;
            }
        }
        try {
            $persentase = [
                'absen' => (($absen / $jumlah_hari) * 60) + (($persen_apel / 100 * 40)),
                'kinerja' => ($jumlah_kinerja / ($jumlah_hari * 10)) * 100,
                'kepatuhan' => $jumlah_kepatuhan / $pembagi_kepatuhan * 100
            ];
        } catch (\Exception $e){
            $persentase = [
                'absen' => 0,
                'kinerja' => 0,
                'kepatuhan' => 0
            ];
        }
        $persentase_total = [
            'absen' => $persentase['absen'] * $persen_absen / 100,
            'kinerja' => $persentase['kinerja'] * $persen_kinerja / 100,
            'kepatuhan' => $persentase['kepatuhan'] * $persen_kepatuhan / 100
        ];
        $total_persentase_tunjangan = 0;
        foreach ($persentase_total AS $key => $value) {
            $total_persentase_tunjangan += $value;
        }
        $total_tunjangan = ($total_persentase_tunjangan * $jumlah_tunjangan) / 100;
        if (!$is_mobile) {
            $response = [
                'pegawai' => $pegawai,
                'pencapaian' => [
                    'absen' => $jumlah_hari > 0 ? $this->toDecimal($persentase['absen']) : 0,
                    'kinerja' => $jumlah_hari > 0 ? $this->toDecimal($persentase['kinerja']) : 0,
                    'kepatuhan' => $this->toDecimal($persentase['kepatuhan'])
                ],
                'persentase' => [
                    'absen' => $persen_absen,
                    'kinerja' => $persen_kinerja,
                    'kepatuhan' => $persen_kepatuhan,
                ],
                'total' => [
                    'absen' => $jumlah_hari > 0 ? $this->toDecimal($persentase_total['absen']) : 0,
                    'kinerja' => $jumlah_hari > 0 ? $this->toDecimal($persentase_total['kinerja']) : 0,
                    'kepatuhan' => $this->toDecimal($persentase_total['kepatuhan']),
                    'total' => $jumlah_hari > 0 ? $this->toDecimal($total_persentase_tunjangan) : 0
                ],
                'jumlah_tunjagan' => $jumlah_hari > 0 ? $this->toDecimal($jumlah_tunjangan) : 0,
                'total_tunjangan_diterima_juta' => $jumlah_hari > 0 ? $this->toDecimal($total_tunjangan / 1000000) : 0,
                'total_tunjangan_diterima' => $jumlah_hari > 0 ? $this->toDecimal($total_tunjangan) : 0,
                'min_date' => $min_date->tanggal,
                'data_kepatuhan' => $data_kepatuhan
            ];
        } else {

            $response = [
                'pegawai' => $pegawai,
                'pencapaian' => [
                    'absen' => $jumlah_hari > 0 ? $this->toFloat($persentase['absen']) : 0,
                    'kinerja' => $jumlah_hari > 0 ? $this->toFloat($persentase['kinerja']) : 0,
                    'kepatuhan' => $this->toFloat($persentase['kepatuhan'])

                ],
                'persentase' => [
                    'absen' => $persen_absen,
                    'kinerja' => $persen_kinerja,
                    'kepatuhan' => $persen_kepatuhan,
                ],
                'total' => [
                    'absen' => $jumlah_hari > 0 ? $this->toFloat($persentase_total['absen']) : 0,
                    'kinerja' => $jumlah_hari > 0 ? $this->toFloat($persentase_total['kinerja']) : 0,
                    'kepatuhan' => $this->toFloat($persentase_total['kepatuhan']),
                    'total' => $jumlah_hari > 0 ? $this->toFloat($total_persentase_tunjangan) : 0
                ],
                'jumlah_tunjagan' => $jumlah_hari > 0 ? $this->toDecimal($jumlah_tunjangan) : 0,
                'total_tunjangan_diterima' => $jumlah_hari > 0 ? $this->toDecimal($total_tunjangan / 1000000) : 0,
                'min_date' => $min_date->tanggal
            ];
        }
        if ($is_mobile) {
            unset($response['pegawai']);
        }
        if ($detail) {
            $response = array_merge($response, [
                'data' => $data_kinerja
            ]);
        }
        return $response;
    }

    private function poinAbsen($masuk, $pulang, $tanggal)
    {
        if (strtotime($masuk) <= strtotime($tanggal . " 08:00:00")) {
            if ((strtotime($pulang) - (strtotime($masuk))) >= (8 * 3600)) {
                return 1;
            }
        } else if (strtotime($masuk) <= strtotime($tanggal . " 08:30:00")) {
            if ((strtotime($pulang) - (strtotime($masuk))) >= (8 * 3600)) {
                return 0.8;
            }
        } else if (strtotime($masuk) <= strtotime($tanggal . " 09:00:00")) {
            if ((strtotime($pulang) - (strtotime($masuk))) >= (8 * 3600)) {
                return 0.6;
            }
        } else if (strtotime($masuk) > strtotime($tanggal . " 09:00:00")) {
            if ((strtotime($pulang) - (strtotime($masuk))) >= (8 * 3600)) {
                return 0.4;
            }
        }
        return 0.2;
    }

    private function toDecimal($number)
    {
        return number_format((float)$number, 2, ',', '.');
    }

    public function getDetailKinerja($tgl)
    {
        $min_date = HariKerja::whereHas('statusHari', function ($query) {
            $query->where('status_hari', 'kerja');
        })->select('tanggal')->orderBy('tanggal')->first();

        /* Data kinerja */
        $pegawai = auth('api')->user();
        $kinerja = Kinerja::with(['skp_pegawai' => function ($query) {
            $query->select('skp_pegawai.id', 'id_skp', 'status');
            $query->with(['skpTask' => function ($query) {
                $query->select('task', 'id');
            }]);
        }, 'media' => function ($query) {
            $query->select('id_kinerja', 'media', 'nama_media');
        }])->where('nip', $pegawai->nip)
            ->select('tgl_mulai', 'tgl_selesai', 'jenis_kinerja', 'rincian_kinerja', 'approve', 'keterangan_approve', 'id', 'nilai_kinerja')
            ->whereDate('tgl_mulai', '<=', $tgl)
            ->whereDate('tgl_selesai', '>=', $tgl)
            ->terbaru()
            ->first();

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
        }*/

        $bulan = date('m', strtotime($tgl));
        $tahun = date('Y', strtotime($tgl));


        /* Data checkinout */
        $checkinout = Checkinout::where("nip", $pegawai->nip)
            ->whereDate("checktime", $tgl)
            ->get();
        $is_upacara = false;
        if (date('N', strtotime($tgl)) == 1) {
            if (HariKerja::where('tanggal', $tgl)->where('id_status_hari', 1)->first()) {
                $is_upacara = true;
            }
        }
        $status = '';
        $apel = false;
        $wajib_upacara = $pegawai->status_upacara ? true : false;
        $upacara = false;
        if ($is_upacara) {
            if ($wajib_upacara) {
                $mesin_upacara = AbsenUpacara::select('SN')->pluck('SN')->all();
                if ($absen_upacara = Checkinout::where('nip', $pegawai->nip)->where('checktype', 0)->whereDate('checktime', $tgl)->whereIn('sn', $mesin_upacara)->whereTime('checktype', '<=', $this->jam_masuk_upacara)->first()) {
                    $upacara = true;
                }
            }
        }
        $in = ($checkinout->contains('checktype', 0)) ? $checkinout->where('checktype', 0)->min()->checktime : '';
        $out = ($checkinout->contains('checktype', 1)) ? $checkinout->where('checktype', 1)->max()->checktime : '';
        if ($in) {
            $status = '';
        } else {
            $status = 'alpa';
        }
        if (strtotime($in) <= strtotime($tgl . " 09:00:00")) {
            if ($in && $out) {
                if ((strtotime($out) - (strtotime($in))) >= (8 * 3600)) {
                    $status = 'hadir';
                } else {
                    $status = 'alpa';
                }
            }

        } else {
            $status = 'alpa';
        }
        if (strtotime($tgl) < strtotime(date('Y-m-d'))) {
            if ($status == '') {
                $status = 'alpa';
            }
        }
        if ($status != 'alpa') {
            if ($in != null) {
                if (strtotime($in) <= strtotime($tgl . " 07:30:00")) {
                    $apel = true;
                }
            }
            if ($is_upacara && $wajib_upacara) {
                $apel = $upacara;
            }
        }
        if ($kinerja) {
            if ($kinerja->jenis_kinerja != 'hadir') {
                $status = $kinerja->jenis_kinerja;
            }
        }
        /*if ($kinerja) {
            $kinerja = $kinerja->toArray();
            if (isset($skp_pegawai))
                $kinerja['skp_pegawai'] = $skp_pegawai;
            if (isset($media))
                $kinerja['media'] = $media;
        }*/
        /* Data array */

        $result = [
            'uuid' => $pegawai->uuid,
            'nama' => $pegawai->nama,
            'nip' => $pegawai->nip,
            'foto' => $pegawai->foto,
            'kinerja' => $kinerja,
            'checkinout' => [
                'in' => $in,
                'out' => $out,
            ],
            'status' => $status,
            'apel' => $apel,
            'min_date' => $min_date->tanggal
        ];
        return $result;
    }

    public function cekKinerja($nip)
    {
        $cek_kinerja = Kinerja::where('nip', $nip)->where(function ($query) {
            $query->where(function ($query) {
                $query->where('tgl_mulai', '<=', date('Y-m-d'));
                $query->where('tgl_selesai', '>=', date('Y-m-d'));
            });
            $query->orWhere(function ($query) {
                $query->where('tgl_mulai', '<=', date('Y-m-d'));
                $query->where('tgl_selesai', '>=', date('Y-m-d'));
            });
        })->whereIn('approve', [0, 2])->first();
        return $cek_kinerja;
    }

    public function required($jenis_kinerja = '')
    {
        if (in_array($jenis_kinerja, ['perjalanan_dinas', 'cuti', 'izin'])) {
            $req = [
                'tgl_mulai' => 'required|date',
                'tgl_selesai' => 'required|date'
            ];
        } else {
            $req = [
                'tgl_mulai' => 'date',
                'tgl_selesai' => 'date'
            ];
        }
        return array_merge([
            'nip' => '',
            'jenis_kinerja' => 'required|in:hadir,perjalanan_dinas,cuti,izin,sakit',
            'rincian_kinerja' => 'required',
            'skp_pegawai' => 'array',
            'doc' => 'array'
        ], $req);
    }

    public function uploadFile(array $files, $id_kinerja)
    {
        $success = true;
        foreach ($files AS $key => $file) {
            try {
                $name = 'kinerja-' . $id_kinerja . '-' . $file->getClientOriginalName();
                if ($file->storeAs('public/doc', $name)) {
                    Media::create([
                        'id_kinerja' => $id_kinerja,
                        'media' => url('storage/doc/' . $name),
                        'nama_media' => $name,
                        'uuid' => (string)Str::uuid()
                    ]);
                }
            } catch (\Exception $exception) {
                $success = false;
            }
        }
        if (!$success) {
            Kinerja::where('id', $id_kinerja)->delete();
            abort(500, 'Gagal Menambah Kinerja, dokumen gagal diupload');
        }
    }

    private function getListNip()
    {
        return implode(',', Pegawai::select('nip')->get()->pluck('nip')->all());
    }

    private function toFloat($number)
    {
        return (float)number_format((float)$number, 2, '.', ',');
    }

}
