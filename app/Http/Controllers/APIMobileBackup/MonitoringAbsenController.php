<?php

namespace App\Http\Controllers\APIMobileBackup;

use App\Http\Controllers\Controller;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;


class MonitoringAbsenController extends Controller
{
    private $special_user = [2, 3, 4];
    private $jam_masuk = '09:00:59';
    private $jam_masuk_upacara = '07.30.59';
    private $status_hari = true;

    public function dataAbsensi(Request $request)
    {
        $this->show_limit_mobile = $request->has('s') ? $request->input('s') : $this->show_limit_mobile;
        $page = $request->input('page');
        $skpd = $request->input('skpd');
        $date = Carbon::parse($request->input('d'));
        $raw_date = $request->input('d');
        $search = $request->has('search') ? $request->input('search') : '';
        $user = auth('api')->user();
        $status_hari = $this->getStatusHariKerja($date);

        $min_date = HariKerja::whereHas('statusHari', function ($query) {
            $query->where('status_hari', 'kerja');
        })->select('tanggal')->orderBy('tanggal')->first();

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
            if (in_array($user->role()->pluck('id_role')->max(), $this->special_user) == false) {
                if ($user->role()->pluck('id_role')->max() != 5) {
                    $pegawai->whereHas('jabatan', function ($query) use ($user) {
                        $query->where('id_atasan', '=', $user->id_jabatan);
                    });
                } else {
                    $pegawai->whereHas('jabatan', function ($query) use ($user) {
                        $query->where('id_skpd', $user->id_skpd);
                    });
                }
            }

            if ($skpd > 0) {
                $pegawai->where('id_skpd', $skpd);
            }

            if ($skpd == -1) {
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

            if ($page) {
                $data_absen_pegawai = $this->paginate($data_absen_pegawai, $this->show_limit_mobile, $page);
            } else {
                $data_absen_pegawai = $data_absen_pegawai;
            }

            return $this->ApiSpecResponses(
                [
                    'pegawai' => $data_absen_pegawai,
                    'min_date' => $min_date->tanggal,
                    'summary' => $sum
                ]
            );
        } catch (Exception $e) {
            return response()->json([
                'message' => 'NOT_FOUND'
            ], 404);
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
            // $data['absen_in'] = '';
            // $data['absen_out'] = '';

            $raw_absensi = $item['checkinout'];
            $absensi = null;

            $tanggal_sekarang = date('Y-m-d', strtotime($jam_sekarang));
            $tanggal_pilihan_date = date('Y-m-d', strtotime($tanggal_pilihan));

            $absen_in = $raw_absensi->contains('checktype', 0) ? $raw_absensi->where('checktype', 0)->min()->checktime : '';
            $absen_out = $raw_absensi->contains('checktype', 1) ? $raw_absensi->where('checktype', 1)->max()->checktime : '';

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
                        $absensi = '';
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
                    $absensi = '';
                }
            } else {
                $absensi = 'libur';
            }


            $data['uuid'] = $item->uuid;
            $data['nama'] = $item->nama;
            $data['nip'] = $item->nip;
            $data['foto'] = $item->foto;
            $data['checkinout'] = [
                'in' => $absen_in ? $absen_in : '',
                'out' => $absen_out ? $absen_out : ''
            ];
            $data['kinerja'] = $absensi;

            return $data;

        });
        return $data;
    }

    private function summary($pegawai, $date, $status_hari)
    {
        if ($status_hari == 1 && strtotime(date('Y-m-d')) >= strtotime($date)) {
            $hadir = (int)$pegawai->where('kinerja', 'hadir')->count();
            $cuti = (int)$pegawai->where('kinerja', 'cuti')->count();
            $perjalanan_dinas = (int)$pegawai->where('kinerja', 'perjalanan_dinas')->count();
            $izin = (int)$pegawai->where('kinerja', 'izin')->count();
            $sakit = (int)$pegawai->where('kinerja', 'sakit')->count();
            $alpha = (int)$pegawai->where('kinerja', 'alpa')->count();
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

    private function paginate($items, $perPage = 6, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator(array_values($items->forPage($page, $perPage)->toArray()), $items->count(), $perPage, $page, $options);
    }
}
