<?php

namespace App\Repositories;


use App\Models\Absen\Checkinout;
use App\Models\MasterData\FormulaVariable;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;

class KinerjaRepository extends BaseRepository
{
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
        return $this->model->where('nip', $nip)->where('tgl_mulai', date('Y-m-d'))->where('jenis_kinerja', 'hadir')->where('approve', '5')->first();
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
                    'diagnostic' => [
                        'code' => '403',
                        'message' => 'gagal menambah kinerja, tanggal berakhir lebih kecil dari tanggal mulai'
                    ]
                ];
            }
        }
        $input['nip'] = $nip;
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
                    if (isset($input['status'])) {
                        if ($input['status'] == 5) {
                            $input['approve'] = 5;
                        }
                    }
                    if (isset($input['id'])) {
                        $kinerja = $this->model->where('nip', $nip)->where('jenis_kinerja', 'hadir')->findOrFail($input['id']);
                        $kinerja->update([
                            'rincian_kinerja' => $input['rincian_kinerja'],
                            'approve' => $input['approve']
                        ]);
                    } else {
                        $kinerja = $this->model->create($input);
                    }
                    return $kinerja;
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
                        'diagnostic' => [
                            'code' => '403',
                            'message' => 'gagal menambahkan kinerja, sdh ada kinerja yang disimpan untuk hari ini'
                        ]
                    ];
                }
                $kinerja = $this->model->create($input);
                return $kinerja;
            }
        } else {
            return [
                'diagnostic' => [
                    'code' => '403',
                    'message' => 'gagal menambahkan kinerja, sdh ada kinerja pada hari yang sama untuk user ini'
                ]
            ];
        }
    }

    public function getTunjanganKinerja($nip, $bulan = null, $tahun = null, $detail = false)
    {
        $bulan = (int)($bulan ? $bulan : date('m'));
        $tahun = $tahun ? $tahun : date('Y');

        $persen_absen = FormulaVariable::select('persentase_nilai')->where('variable', 'absen')->first()->persentase_nilai;
        $persen_kinerja = FormulaVariable::select('persentase_nilai')->where('variable', 'kinerja')->first()->persentase_nilai;


        $pegawai = Pegawai::select('nip', 'id_jabatan')->with(['jabatan' => function ($query) {
            $query->select('id', 'id_golongan');
            $query->with(['golongan' => function ($query) {
                $query->select('id', 'tunjangan');
            }]);
        }])->where('nip', $nip)->first();
        $nip = $pegawai->nip;
        $jumlah_tunjangan = $pegawai->jabatan->golongan->tunjangan;


        $hari_kerja = HariKerja::select('tanggal')->whereHas('statusHari', function ($query) {
            $query->where('status_hari', 'kerja');
        })->where('bulan', $bulan)->where('tahun', $tahun)->orderBy('tanggal', 'asc')->get();
        $jumlah_hari = $hari_kerja->count();
        $jumlah_kinerja = $absen = 0;
        $data_kinerja = [];

        if ($jumlah_hari > 0) {
            foreach ($hari_kerja AS $hk) {
                $knj = $this->model->where('nip', $nip)->where('tgl_mulai', '<=', $hk->tanggal)->where('tgl_selesai', '>=', $hk->tanggal)->terbaru();
                $abs = Checkinout::select('checktime', 'checktype')->where('nip', $nip)->whereDate('checktime', $hk->tanggal)->orderBy('checktype', 'asc')->get();
                $status = '';
                if ($abs->count() > 0) {
                    $in = false;
                    $out = false;
                    $masuk = $pulang = null;
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
                        $poin = $this->poinAbsen($masuk, $pulang);
                        $status = 'hadir';
                    }
                    $absen = (float)$absen + $poin;
                }
                if (strtotime($hk->tanggal) < strtotime(date('Y-m-d'))) {
                    if ($status == '') {
                        $status = 'alpa';
                    }
                }
                if ($detail) {
                    $data_kinerja[] = [
                        'tanggal' => $hk->tanggal,
                        'tanggal_string' => $this->formatDate($hk->tanggal),
                        'tanggal_string2' => $this->formatDate2($hk->tanggal),
                        'hari' => ucfirst($hk->Hari->nama_hari),
                        'kinerja' => $knj->first() ? $knj->first()->toArray() : null,
                        'absen' => $abs ? $abs->toArray() : null,
                        'status' => ucfirst($status)
                    ];
                }

                if ($knj->where('approve', 2)->first()) {
                    $jumlah_kinerja++;
                }
                if ($knj->where('jenis_kinerja', '<>', 'hadir')->where('approve', 2)->first()) {
                    $absen++;
                }
            }
            $persentase = [
                'absen' => ($absen / $jumlah_hari) * 100,
                'kinerja' => ($jumlah_kinerja / $jumlah_hari) * 100,
            ];
            $persentase_total = [
                'absen' => $persentase['absen'] * $persen_absen / 100,
                'kinerja' => $persentase['kinerja'] * $persen_kinerja / 100,
            ];
            $total_persentase_tunjangan = 0;
            foreach ($persentase_total AS $key => $value) {
                $total_persentase_tunjangan += $value;
            }
            $total_tunjangan = ($total_persentase_tunjangan * $jumlah_tunjangan) / 100;
        }
        $response = [
            'pegawai' => $pegawai,
            'pencapaian' => [
                'absen' => $jumlah_hari > 0 ? $this->toDecimal($persentase['absen']) : 0,
                'kinerja' => $jumlah_hari > 0 ? $this->toDecimal($persentase['kinerja']) : 0,
            ],
            'persentase' => [
                'absen' => $persen_absen,
                'kinerja' => $persen_kinerja,
            ],
            'total' => [
                'absen' => $jumlah_hari > 0 ? $this->toDecimal($persentase_total['absen']) : 0,
                'kinerja' => $jumlah_hari > 0 ? $this->toDecimal($persentase_total['kinerja']) : 0,
                'total' => $jumlah_hari > 0 ? $this->toDecimal($total_persentase_tunjangan) : 0
            ],
            'jumlah_tunjagan' => $jumlah_hari > 0 ? $this->toDecimal($jumlah_tunjangan) : 0,
            'total_tunjangan_diterima' => $jumlah_hari > 0 ? $this->toDecimal($total_tunjangan) : 0,
        ];
        if ($detail) {
            $response = array_merge($response, [
                'data' => $data_kinerja
            ]);
        }
        return $response;
    }

    private function poinAbsen($masuk, $pulang)
    {
        if (strtotime($masuk) <= strtotime($hk->tanggal . " 08:00:00")) {
            if ((strtotime($pulang) - (strtotime($masuk))) >= (8 * 3600)) {
                return 1;
            }
        } else if (strtotime($masuk) <= strtotime($hk->tanggal . " 08:30:00")) {
            if ((strtotime($pulang) - (strtotime($masuk))) >= (8 * 3600)) {
                return 0.8;
            }
        } else if (strtotime($masuk) <= strtotime($hk->tanggal . " 09:00:00")) {
            if ((strtotime($pulang) - (strtotime($masuk))) >= (8 * 3600)) {
                return 0.6;
            }
        } else if (strtotime($masuk) > strtotime($hk->tanggal . " 09:00:00")) {
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

    public function required()
    {
        return [
            'nip' => 'required',
            'tgl_mulai' => 'required|date',
            'tgl_selesai' => 'required|date',
            'jenis_kinerja' => 'required|in:hadir,perjalanan_dinas,cuti,izin,sakit',
            'rincian_kinerja' => ''
        ];
    }

    private function getListNip()
    {
        return implode(',', Pegawai::select('nip')->get()->pluck('nip')->all());
    }

}
