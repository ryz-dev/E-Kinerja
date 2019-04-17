<?php

namespace App\Http\Controllers\APIMobileBackup;

use App\Models\Absen\Checkinout;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\FormulaVariable;
use App\Models\MasterData\HariKerja;
use App\Repositories\KinerjaRepository;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KinerjaController extends ApiController
{
    protected $kinerja;
    public function __construct(KinerjaRepository $kinerja)
    {
        $this->kinerja = $kinerja;
    }

    public function getKinerjaTersimpan()
    {
        $nip = auth('api')->user()->nip;
        $kinerja_tersimpan = Kinerja::where('nip', $nip)->where('tgl_mulai', date('Y-m-d'))->where('jenis_kinerja', 'hadir')->where('approve', '5')->first();

        try {
            $data = [
                'id' => $kinerja_tersimpan->id,
                'nip' => $kinerja_tersimpan->nip,
                'tgl_mulai' => $kinerja_tersimpan->tgl_mulai,
                'tgl_selesai' => $kinerja_tersimpan->tgl_selesai,
                'jenis_kinerja' => $kinerja_tersimpan->jenis_kinerja,
                'rincian_kinerja' => $kinerja_tersimpan->rincian_kinerja,
                'approve' => $kinerja_tersimpan->approve
            ];
            return $this->ApiSpecResponses($data);

        } catch (Exception $e) {
            return response()->json([
                'diagnostic' => [
                    'code' => '404',
                    'message' => 'Tidak ada kinerja yang tersimpan'
                ]
            ]);
        }
    }

    public function hapusKinerjaTersimpan($id)
    {
        $nip = auth('api')->user()->nip;
        $cek_kinerja = Kinerja::where('nip', $nip)->where('jenis_kinerja', 'hadir')->where('approve', '5')->where('tgl_mulai', date('Y-m-d'))->find($id);

        if ($cek_kinerja) {
            $cek_kinerja->delete();
            return response()->json([
                'diagnostic' => [
                    'code' => '201',
                    'message' => 'Berhasil menghapus draft'
                ]
            ]);
        } else {
            return response()->json([
                'diagnostic' => [
                    'code' => '404',
                    'message' => 'id tidak ditemukan'
                ]
            ]);
        }
    }

    public function inputKinerja(Request $request)
    {
        $input = $request->all();
        $nip = Auth::user()->nip;
        $res = $this->kinerja->inputKinerja($input,$nip);
        return apiResponse(isset($res['data']) ? $res['data'] : '',isset($res['diagnostic']) ? $res['diagnostic'] : []);
        $input = $request->input();
        $input['nip'] = auth('api')->user()->nip;
        if (in_array($input['jenis_kinerja'], ['hadir', 'sakit'])) {
            $input['tgl_mulai'] = date('Y-m-d');
            $input['tgl_selesai'] = date('Y-m-d');
        } else {
            $tgl_mulai = explode('/', $input['tgl_mulai']);
            $tgl_selesai = explode('/', $input['tgl_selesai']);

            $input['tgl_mulai'] = $tgl_mulai[2] . '-' . $tgl_mulai[0] . '-' . $tgl_mulai[1];
            $input['tgl_selesai'] = $tgl_selesai[2] . '-' . $tgl_selesai[0] . '-' . $tgl_selesai[1];
            if (strtotime($input['tgl_mulai']) > strtotime($input['tgl_selesai'])) {
                return response()->json([
                    'diagnostic' => [
                        'code' => '403',
                        'message' => 'gagal menambah kinerja, tanggal berakhir lebih kecil dari tanggal mulai'
                    ]
                ]);
            }
        }
        // $cek_kinerja = Kinerja::where('nip',$input['nip'])->where('tgl_mulai','<=',$input['tgl_mulai'])->where('tgl_selesai','>=',$input['tgl_selesai'])->whereIn('approve',[0,2])->first();
        $cek_kinerja = Kinerja::where('nip', $input['nip'])->where(function ($query) use ($input) {
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
            $input['nilai_kinerja'] = 0;
            if ($input['jenis_kinerja'] == 'hadir') {
                $cek_hari_kerja = HariKerja::whereDate('tanggal', date('Y-m-d'))->first();
                if ($cek_hari_kerja) {
                    $cek_hadir_kerja = Checkinout::whereDate('checktime', date('Y-m-d'))->where('checktype', '0')->where('nip', $input['nip'])->first();
                    $cek_pulang_kerja = Checkinout::whereDate('checktime', date('Y-m-d'))->where('checktype', '1')->where('nip', $input['nip'])->first();
                    // if (strtotime($cek_hadir_kerja->checktime) <= strtotime(date('Y-m-d')." 09:00:00")){
                    //     if ((strtotime($cek_pulang_kerja->checktime) - strtotime($cek_hadir_kerja->checktime)) >= (8 * 3600)) {
                    if ($request->has('status')) {
                        if ($request->input('status') == 5) {
                            $check = Kinerja::where('tgl_mulai', date('Y-m-d'))->where('jenis_kinerja', 'hadir')->where('approve', '5')->first();
                            if ($check) {
                                return response()->json([
                                    'diagnostic' => [
                                        'code' => '403',
                                        'message' => 'gagal menambah draft, sudah ada draft yang tersimpan hari ini'
                                    ]
                                ]);
                            } else {
                                $input['approve'] = 5;
                            }
                        }
                    }
                    if ($request->has('id') && $request->input('id')) {
                        $kinerja = Kinerja::where('nip', $input['nip'])->where('jenis_kinerja', 'hadir')->findOrFail($request->input('id'));
                        $kinerja->update([
                            'rincian_kinerja' => $input['rincian_kinerja'],
                            'approve' => $input['approve']
                        ]);
                    } else {
                        $kinerja = Kinerja::create($input);
                    }
                    return $this->ApiSpecResponses($kinerja);
                    //     }
                    // } else {
                    //     return response()->json([
                    //         'diagnostic' => [
                    //             'code' => '403',
                    //             'message' => 'gagal menambah kinerja, tidak tercata hadir pada absen'
                    //         ]
                    //     ]);
                    // }
                }

                return response()->json([
                    'diagnostic' => [
                        'code' => '403',
                        'message' => 'gagal menambah kinerja, bukan hari kerja'
                    ]
                ]);
            } else {
                $cek_kinerja = Kinerja::where('nip', $input['nip'])->where(function ($query) use ($input) {
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
                    return response()->json([
                        'diagnostic' => [
                            'code' => '403',
                            'message' => 'gagal menambahkan kinerja, sdh ada kinerja yang disimpan untuk hari ini'
                        ]
                    ]);
                }
                $kinerja = Kinerja::create($input);
                return $this->ApiSpecResponses($kinerja);
            }
        } else {
            return response()->json([
                'diagnostic' => [
                    'code' => '403',
                    'message' => 'gagal menambahkan kinerja, sdh ada kinerja pada hari yang sama untuk user ini'
                ]
            ]);
        }
    }

    public function tunjanganKinerja($bulan = null, $tahun = null)
    {
        $bulan = (int)($bulan ? $bulan : date('m'));
        $tahun = $tahun ? $tahun : date('Y');

        $persen_absen = FormulaVariable::where('variable', 'absen')->first()->persentase_nilai;
        $persen_kinerja = FormulaVariable::where('variable', 'kinerja')->first()->persentase_nilai;

        $pegawai = auth('api')->user();
        $nip = $pegawai->nip;

        $pegawai->load('jabatan.golongan');
        $jumlah_tunjangan = $pegawai->jabatan->golongan->tunjangan;

        $min_date = HariKerja::whereHas('statusHari', function ($query) {
            $query->where('status_hari', 'kerja');
        })->select('tanggal')->orderBy('tanggal')->first();

        $hari_kerja = HariKerja::whereHas('statusHari', function ($query) {
            $query->where('status_hari', 'kerja');
        })->where('bulan', $bulan)->where('tahun', $tahun)->orderBy('tanggal', 'asc')->get();
        $jumlah_hari = $hari_kerja->count();

        if ($jumlah_hari > 0) {
            foreach ($hari_kerja AS $hk) {
                $knj = Kinerja::where('nip', $nip)->where('tgl_mulai', '<=', $hk->tanggal)->where('tgl_selesai', '>=', $hk->tanggal)->where('approve', 2)->terbaru();
                $abs = Checkinout::where('nip', $nip)->whereDate('checktime', $hk->tanggal)->get();
                $status = '';
                // $status = '';                
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

                    if (strtotime($masuk) <= strtotime($hk->tanggal . " 09:00:00")) {
                        if ($in && $out) {
                            if ((strtotime($pulang) - (strtotime($masuk))) >= (8 * 3600)) {
                                $absen++;
                                $status = 'hadir';
                            } else {
                                $status = 'alpa';
                            }
                        }
                    } else {
                        $status = 'alpa';
                    }
                }

                if ($knj->first()) {
                    $status = $knj->first()->jenis_kinerja;
                }

                if (strtotime($hk->tanggal) < strtotime(date('Y-m-d'))) {
                    if ($status == '') {
                        $status = 'alpa';
                    }
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
            'pencapaian' => [
                'absen' => $jumlah_hari > 0 ? $this->toFloat($persentase['absen']) : 0,
                'kinerja' => $jumlah_hari > 0 ? $this->toFloat($persentase['kinerja']) : 0,
            ],
            'persentase' => [
                'absen' => $persen_absen,
                'kinerja' => $persen_kinerja,
            ],
            'total' => [
                'absen' => $jumlah_hari > 0 ? $this->toFloat($persentase_total['absen']) : 0,
                'kinerja' => $jumlah_hari > 0 ? $this->toFloat($persentase_total['kinerja']) : 0,
                'total' => $jumlah_hari > 0 ? $this->toFloat($total_persentase_tunjangan) : 0
            ],
            'jumlah_tunjagan' => $jumlah_hari > 0 ? $this->toDecimal($jumlah_tunjangan) : 0,
            'total_tunjangan_diterima' => $jumlah_hari > 0 ? $this->toDecimal($total_tunjangan) : 0,

            'min_date' => $min_date->tanggal
        ];
        return $this->ApiSpecResponses($response);
    }

    private function toFloat($number)
    {
        return (float)number_format((float)$number, 2, '.', ',');
    }

    private function toDecimal($number)
    {
        return number_format((float)$number, 2, ',', '.');
    }

    public function detailKinerja($tgl)
    {
        $date = new HariKerja;

        $min_date = HariKerja::whereHas('statusHari', function ($query) {
            $query->where('status_hari', 'kerja');
        })->select('tanggal')->orderBy('tanggal')->first();

        /* Data kinerja */
        $pegawai = auth('api')->user();
        $kinerja = Kinerja::where('nip', $pegawai->nip)
            ->select('tgl_mulai', 'tgl_selesai', 'jenis_kinerja', 'rincian_kinerja', 'approve', 'keterangan_approve')
            ->whereDate('tgl_mulai', '<=', $tgl)
            ->whereDate('tgl_selesai', '>=', $tgl)
            ->terbaru()
            ->first();


        $bulan = date('m', strtotime($tgl));
        $tahun = date('Y', strtotime($tgl));


        /* Data checkinout */
        $checkinout = Checkinout::where("nip", $pegawai->nip)
            ->whereDate("checktime", $tgl)
            ->get();

        $in = ($checkinout->contains('checktype', 0)) ? $checkinout->where('checktype', 0)->min()->checktime : '';
        $out = ($checkinout->contains('checktype', 1)) ? $checkinout->where('checktype', 1)->max()->checktime : '';

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
            'min_date' => $min_date->tanggal
        ];

        return $this->ApiSpecResponses($result);
    }

    public function cekKinerja()
    {
        $pegawai = auth('api')->user();
        $nip = $pegawai->nip;
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

        $true = true;
        $false = false;
        if ($cek_kinerja) {
            return $this->ApiSpecResponses($true);
        } else {
            return $this->ApiSpecResponses($false);
        }
    }
}