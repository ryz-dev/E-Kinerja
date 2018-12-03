<?php

namespace App\Http\Controllers\APIMobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use App\Models\Absen\Kinerja;
use App\Models\Absen\Etika;
use App\Models\Absen\Checkinout;

class RekapBulananController extends ApiController
{
    private $special_user = ['Bupati','Wakil Bupati','Sekertaris Daerah'];

    public function getBawahan(Request $request){
        $user = auth('api')->user();
        $skpd = $request->has('skpd') ? $request->input('skpd') : null;
        $search = $request->has('search')? $request->input('search'):'';

        if (in_array($user->role()->first()->nama_role,$this->special_user) == false) {
            $user->load('jabatan.pegawai_bawahan');
            $bawahan = $user->jabatan->pegawai_bawahan;
        } else {
            $bawahan = Pegawai::with('jabatan')->whereNotNull('id_jabatan')->where('nip','<>',$user->nip)->where('id_jabatan','>',$user->id_jabatan);
            if ($skpd){
                $bawahan = $bawahan->where('id_skpd',$skpd);
            }
        }

        if ($search) {
            $bawahan->where(function($query) use ($search){
                $query->where('nip','like','%'.$search.'%')->orWhere('nama','like','%'.$search.'%');
            });
        }

        $bawahan = $bawahan->get();
        $data = [];
        foreach($bawahan as $b) {
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
        $bulan = (int)($bulan?:date('m'));
        $tahun = ($tahun?:date('Y'));
        $min_date = HariKerja::whereHas('statusHari', function ($query){
            $query->where('status_hari', 'kerja');
        })->select('tanggal')->orderBy('tanggal')->first();
        $hari_kerja = HariKerja::where('bulan',$bulan)->where('tahun',$tahun)->whereHas('statusHari',function ($query){
            $query->where('status_hari','kerja');
        })->get();
        try {
            if (in_array($user->role()->first()->nama_role,$this->special_user) == false) {
                $pegawai = Pegawai::whereNip($nip)->whereHas('jabatan.atasan.pegawai', function ($query) {
                    $query->where('nip', auth('api')->user()->nip);
                })->firstOrFail();
            } else {
                $pegawai = Pegawai::whereNip($nip)->where('id_jabatan','>',$user->id_jabatan)->firstOrFail();
            }
        } catch (\Exception $exception){
            abort('404');
        }

        $data_inout = [];
        foreach ($hari_kerja AS $key => $hk){
            $kinerja = $pegawai->kinerja()->where('tgl_mulai','<=',$hk->tanggal)->where('tgl_selesai','>=',$hk->tanggal)->first();
            $etika = $pegawai->etika()->where('tanggal',$hk->tanggal)->first();
            $data_inout[] = [
                'tanggal' => $hk->tanggal,
                'hari' => ucfirst($hk->Hari->nama_hari),
                'status' => ucfirst(str_replace('_',' ',isset($kinerja->jenis_kinerja)?$kinerja->jenis_kinerja:'')),
                'persentase' => isset($etika->persentase)?$etika->persentase : 0,
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

        /* Tarik tanggal sebelumnya */
        $date_prev = $date->whereDate('tanggal','<',$tgl)
        ->whereIdStatusHari(1)
        ->orderBy('tanggal','desc')
        ->first();

        /* Tarik tanggal setelahnya */
        $date_next = $date->whereDate('tanggal','>',$tgl)
        ->whereIdStatusHari(1)
        ->orderBy('tanggal','asc')
        ->first();

        /* Data kinerja */
        $pegawai = Pegawai::where('nip',$nip)->first();
        $kinerja = Kinerja::where('nip',$pegawai->nip)
        ->whereDate('tgl_mulai','<=',$tgl)
        ->whereDate('tgl_selesai','>=',$tgl)
            ->terbaru()
            ->first();

        /* Data etika */
        $etika = Etika::where("nip",$pegawai->nip)
        ->select('persentase', 'keterangan')
        ->where("tanggal",$tgl)
        ->first();

        /* Data checkinout */
        $checkinout = Checkinout::where("nip",$pegawai->nip)
        ->select('checktime')
        ->whereDate("checktime",$tgl)
        ->get();

        /* Data array */
        $result = [
            'uuid' => $pegawai->uuid,
            'nama' => $pegawai->nama,
            'nip' => $pegawai->nip,
            'foto' => $pegawai->foto,
            'kinerja' => $kinerja ? $kinerja : [],
            'etika' => $etika ? $etika : [],
            'checkinout' => [
                'in' => (count($checkinout)) ? $checkinout[0]->checktime : "",
                'out' => (count($checkinout) > 1) ? $checkinout[1]->checktime : "",
            ],
            'min_date' => $min_date->tanggal
        ];

        return $this->ApiSpecResponses($result);
    }
}
