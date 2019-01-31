<?php

namespace App\Http\Controllers\APIMobile;

use App\Models\MasterData\Bulan;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Kinerja;
use App\Models\Absen\Etika;
use App\Models\Absen\Checkinout;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;


class RekapBulananController extends ApiController
{
    private $special_user = ['Bupati','Wakil Bupati','Sekretaris Daerah'];
    private $special_user_id = [2,3,4];

    public function getBawahan(Request $request){
        $user = auth('api')->user();
        $page = $request->input('page');
        $skpd = $request->has('skpd') ? $request->input('skpd') : null;
        $search = $request->has('search')? $request->input('search'):'';

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
            } else {
                $pegawai->whereHas('jabatan', function($query) use ($user){
                    $query->where('id_skpd', $user->id_skpd);
                });
            }
        }

        if ($search) {
            $pegawai->where(function($query) use ($search){
                $query->where('nip','like','%'.$search.'%')->orWhere('nama','like','%'.$search.'%');
            });
        }

        if ($page) {
            $pegawai = $pegawai->paginate($this->show_limit_mobile);
        } else {
            $pegawai = $pegawai->get();
        }
        
        $data = [];
        foreach($pegawai as $b) {
            $data[] = [
                'uuid' => $b->uuid,
                'nama' => $b->nama,
                'foto' => $b->foto,
                'nip' => $b->nip,
                'created_at' => $b->created_at,
            ];
        }
        return $this->ApiSpecResponses($data);
    }

    public function getRekap($nip,$bulan = null,$tahun = null){
        $user = auth('api')->user();
        $min_date = HariKerja::whereHas('statusHari', function ($query){
            $query->where('status_hari', 'kerja');
        })->select('tanggal')->orderBy('tanggal')->first();
        $bulan = (int)($bulan?:date('m'));
        $tahun = ($tahun?:date('Y'));
        $hari_kerja = HariKerja::where('bulan',$bulan)->where('tahun',$tahun)->whereHas('statusHari',function ($query){
            $query->where('status_hari','kerja');
        })->orderBy('tanggal','asc')->get();
        try {
            if (in_array($user->role()->first()->nama_role, $this->special_user) == false) {
                if ($user->role()->first()->nama_role == 'Kepala Dinas') {
                    $pegawai = Pegawai::whereNip($nip)->where('id_skpd',$user->id_skpd)->firstOrFail();
                } else{
                    $pegawai = Pegawai::whereNip($nip)->whereHas('jabatan.atasan.pegawai', function ($query) {
                        $query->where('nip', auth('api')->user()->nip);
                    })->firstOrFail();
                }
            } else {
                $pegawai = Pegawai::whereNip($nip)->firstOrFail();
                // $pegawai = Pegawai::whereNip($nip)->where('id_jabatan', '>', $user->id_jabatan)->firstOrFail();
            }
        } catch (\Exception $exception){
            abort('404');
        }
        $data_inout = [];
        foreach ($hari_kerja AS $key => $hk){
            $apel = false;
            $kinerja = $pegawai->kinerja()->where('tgl_mulai','<=',$hk->tanggal)->where('tgl_selesai','>=',$hk->tanggal)->terbaru()->first();
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
                        if ((strtotime($pulang) - (strtotime($masuk))) >= (8.5 * 3600)) {
                            $kehadiran['status'] = 'hadir';
                        } else {
                            $kehadiran['status'] = 'alpa';
                        }
                    }

                    if (date('N',strtotime($hk->tanggal)) != 1){
                        if (strtotime($masuk) <= strtotime($hk->tanggal . " 07:30:00")) {
                            $apel = true;
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

//            $etika = $pegawai->etika()->where('tanggal',$hk->tanggal)->first();
            $status = ucfirst(str_replace('_', ' ', isset($kinerja->jenis_kinerja) ? $kinerja->jenis_kinerja : ''));
            if ($status == 'Hadir' || $status == '') {
                $status = ucfirst($kehadiran['status']);
            }

            $data_inout[] = [
                // 'tgl_prev' => isset($hari_kerja[$key-1]->tanggal) ? $hari_kerja[$key-1]->tanggal : '',
                // 'tgl_next' => isset($hari_kerja[$key+1]->tanggal) ? $hari_kerja[$key+1]->tanggal : '',
                // 'tgl' => $hk->tanggal,
                'tanggal' => $hk->tanggal,
                'hari' => ucfirst($hk->Hari->nama_hari),
                // 'checkinout' => $kehadiran,
                'status' => $status,
                'apel' => $apel,
                // 'persentase' => isset($etika->persentase)?$etika->persentase : 0,
                'approve' => isset($kinerja->approve) ? $kinerja->approve : 0
            ];
        }
        return $this->ApiSpecResponses([
            'uuid' => $pegawai->uuid,
            'nama' => $pegawai->nama,
            'nip' => $pegawai->nip,
            'foto' => $pegawai->foto,
            'rekap_bulanan' => $data_inout,
            'min_date' => $min_date->tanggal
        ]);
    }

    public function getDetailRekap($nip,$tgl) {
        $date = new HariKerja;

        $min_date = HariKerja::whereHas('statusHari', function ($query){
            $query->where('status_hari', 'kerja');
        })->select('tanggal')->orderBy('tanggal')->first();

        // /* Tarik tanggal sebelumnya */
        // $date_prev = $date->whereDate('tanggal','<',$tgl)
        // ->whereIdStatusHari(1)
        // ->orderBy('tanggal','desc')
        // ->first();

        // /* Tarik tanggal setelahnya */
        // $date_next = $date->whereDate('tanggal','>',$tgl)
        // ->whereIdStatusHari(1)
        // ->orderBy('tanggal','asc')
        // ->first();

        /* Data kinerja */
        $pegawai = Pegawai::where('nip',$nip)->first();
        $kinerja = Kinerja::where('nip',$pegawai->nip)
        ->select('tgl_mulai', 'tgl_selesai', 'jenis_kinerja', 'rincian_kinerja', 'approve', 'keterangan_approve')
        ->whereDate('tgl_mulai','<=',$tgl)
        ->whereDate('tgl_selesai','>=',$tgl)
            ->terbaru()
            ->first();

        /* Data etika */
        $bulan = date('m',strtotime($tgl));
        $tahun = date('Y',strtotime($tgl));
        $etika = Etika::where("nip",$pegawai->nip)
            ->where("tanggal",'like',$tahun."-".$bulan."%")
            ->select('persentase', 'mengikuti_upacara', 'perilaku_kerja', 'kegiatan_kebersamaan', 'keterangan')
            ->first();
            if ($etika)
            $etika->tanggal_etika = $tahun.'-'.$bulan;
            
            /* Data checkinout */
        $checkinout = Checkinout::where("nip", $pegawai->nip)
            ->whereDate("checktime", $tgl)
            ->get();
       
        //absen masuk
        $checkin = Checkinout::where("nip",$pegawai->nip)
            ->where('checktype', 0)
            ->whereDate("checktime",$tgl)
            ->get();
        //absen pulang
        $checkout = Checkinout::where("nip",$pegawai->nip)
            ->where('checktype', 1)
            ->whereDate("checktime",$tgl)
            ->get();

        $in = (count($checkin)) ? $checkin->min()->checktime : '';
        $out = (count($checkout)) ? $checkout->max()->checktime : '';
   
        $status = '';
        $apel = false;
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
                    if ((strtotime($pulang) - (strtotime($masuk))) >= (8.5 * 3600)) {
                        $status = 'hadir';
                    } else {
                        $status = 'alpa';
                    }
                }
                if (date('N', strtotime($tgl)) != 1){
                    if (strtotime($masuk) <= strtotime($tgl . " 07:30:00")) {
                        $apel = true;
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


        /* Data array */
        $result = [
            'uuid' => $pegawai->uuid,
            'nama' => $pegawai->nama,
            'nip' => $pegawai->nip,
            'foto' => $pegawai->foto,
            'kinerja' => $kinerja,
            'etika' => $etika,
            'checkinout' => [
                'in' => $in,
                'out' => $out,
            ],
            'apel' => $apel,
            'min_date' => $min_date->tanggal
        ];

        return $this->ApiSpecResponses($result);
    }
}
