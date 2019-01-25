<?php

namespace App\Http\Controllers\API;


use App\Models\MasterData\Bulan;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Kinerja;
use App\Models\Absen\Etika;
use App\Models\Absen\Checkinout;
use App\User;
use Illuminate\Http\Request;

class RekapBulananController extends ApiController
{
    private $special_user = ['Bupati', 'Wakil Bupati', 'Sekretaris Daerah'];
    private $special_user_id = [2,3,4];

    public function getBawahan(Request $request)
    {
        $user = auth('web')->user();
        $skpd = $request->has('skpd') ? $request->input('skpd') : null;
        $pegawai = Pegawai::where('nip','!=',$user->nip);
        if ($skpd > 0) {
            $pegawai->where('id_skpd',$skpd);
        }

        if ($skpd < 0) {
            $pegawai->where('id_jabatan',3);
        }
        
        $pegawai = $pegawai->leftJoin('jabatan','pegawai.id_jabatan','=','jabatan.id');
        $pegawai = $pegawai->orderBy('jabatan.id_golongan');
        
        if (in_array($user->role()->pluck('id_role')->max(),$this->special_user_id) == false) {
            if ($user->role()->pluck('id_role')->max() != 5) {
                $pegawai->whereHas('jabatan', function($query) use ($user){
                    $query->where('id_atasan','=',$user->id_jabatan);
                });
            }
        }

        return $this->ApiSpecResponses($pegawai->get());
    }

    public function getRekap($nip, $bulan = null, $tahun = null)
    {
        $user = auth('web')->user();
        $bulan = (int)($bulan ?: date('m'));
        $tahun = ($tahun ?: date('Y'));
        $hari_kerja = HariKerja::where('bulan', $bulan)->where('tahun', $tahun)->whereHas('statusHari', function ($query) {
            $query->where('status_hari', 'kerja');
        })->orderBy('tanggal', 'asc')->get();
        try {
            if (in_array($user->role()->first()->nama_role, $this->special_user) == false) {
                if ($user->role()->first()->nama_role == 'Kepala Dinas') {
                    $pegawai = Pegawai::whereNip($nip)->where('id_skpd',$user->id_skpd)->firstOrFail();
                } else{
                    $pegawai = Pegawai::whereNip($nip)->whereHas('jabatan.atasan.pegawai', function ($query) {
                        $query->where('nip', auth('web')->user()->nip);
                    })->firstOrFail();
                }
            } else {
                $pegawai = Pegawai::whereNip($nip)->firstOrFail();
            }
        } catch (\Exception $exception) {
            abort('404');
        }
        $data_inout = [];
        foreach ($hari_kerja AS $key => $hk) {
            $kinerja = $pegawai->kinerja()->where('tgl_mulai', '<=', $hk->tanggal)->where('tgl_selesai', '>=', $hk->tanggal)->terbaru()->first();
            $kehadiran = $pegawai->checkinout()->where('checktime', 'like', '%' . $hk->tanggal . '%')->orderBy('checktype', 'desc')->get()->toArray();
            if (count($kehadiran) > 0) {
                $kehadiran['status'] = 'alpa';
                $masuk = $pulang = null;
                foreach ($kehadiran AS $kh) {
                    if (isset($kh['checktype'])) {
                        if ($kh['checktype'] == 0) {
                            $masuk = $kh['checktime'];
                        }
                        if ($kh['checktype'] == 1) {
                            $pulang = $kh['checktime'];
                        }
                    }
                }
                if (strtotime($masuk) <= strtotime($hk->tanggal . " 09:00:00")) {
                    if ((strtotime($pulang) - (strtotime($masuk))) >= (8.5 * 3600)) {
                        $kehadiran['status'] = 'hadir';
                    }
                }
            }
//            $etika = $pegawai->etika()->where('tanggal',$hk->tanggal)->first();
            $status = ucfirst(str_replace('_', ' ', isset($kinerja->jenis_kinerja) ? $kinerja->jenis_kinerja : ''));
            if ($status == 'Hadir') {
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
//                'persentase' => isset($etika->persentase)?$etika->persentase : '',
                'approve' => isset($kinerja->approve) ? $kinerja->approve : ''
            ];
        }
        return $this->ApiSpecResponses([
            'tanggal_sekarang' => $this->formatDate(date('Y-m-d')),
            'rekap_bulanan' => $data_inout
        ]);
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
        $pegawai = Pegawai::where('nip', $nip)->first();
        $kinerja = Kinerja::where('nip', $pegawai->nip)
            ->whereDate('tgl_mulai', '<=', $tgl)
            ->whereDate('tgl_selesai', '>=', $tgl)
            ->terbaru()
            ->first();

        /* Data etika */
        $bulan = date('m', strtotime($tgl));
        $tahun = date('Y', strtotime($tgl));
        $etika = Etika::where("nip", $pegawai->nip)
            ->where("tanggal", 'like', $tahun . "-" . $bulan . "%")
            ->first();
        if ($etika)
            $etika->tanggal_etika = ucfirst(Bulan::where('kode', $bulan)->first()->nama_bulan) . " " . $tahun;

        /* Data checkinout */
        $checkinout = Checkinout::where("nip", $pegawai->nip)
            ->whereDate("checktime", $tgl)
            ->get();


        /* Data array */
        $result = [
            "kinerja" => $kinerja,
            "etika" => $etika,
            "checkinout" => $checkinout,
            "tanggal" => $this->formatDate2($tgl),
        ];

        return $this->ApiSpecResponses(array_merge($result, [
            'prev' => isset($date_prev->tanggal) == false ? '' : $date_prev->tanggal,
            'next' => isset($date_next->tanggal) == false ? '' : $date_next->tanggal
        ]));
    }
}
