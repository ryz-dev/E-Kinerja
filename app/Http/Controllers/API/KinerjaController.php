<?php

namespace App\Http\Controllers\API;

use App\Models\Absen\Checkinout;
use App\Models\Absen\Kinerja;
use App\Models\MasterData\Bulan;
use App\Models\MasterData\FormulaVariable;
use App\Models\MasterData\HariKerja;
use App\Models\MasterData\Pegawai;
use App\Repositories\KinerjaRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class KinerjaController extends ApiController
{
    protected $kinerja;
    public function __construct(KinerjaRepository $kinerja)
    {
        $this->kinerja = $kinerja;
    }

    public function getKinerjaTersimpan(){
        $nip = auth('web')->user()->nip;
        if ($kinerja_tersimpan = $this->kinerja->getKinerjaTersimpan($nip)){
            return $this->ApiSpecResponses($kinerja_tersimpan);
        }
        return null;
    }

    public function hapusKinerjaTersimpan($id){
        $nip = auth('web')->user()->nip;
        if ($this->kinerja->deleteKinerjaTersimpan($id,$nip)) {
            return $this->ApiSpecResponses([
                'message' => 'Berhasil menghapus draft'
            ]);
        }
    }

    public function inputKinerja(Request $request){
        $nip = auth('web')->user()->nip;
        return $this->ApiSpecResponses($this->kinerja->inputKinerja($request->input(),$nip));
    }

    public function tunjanganKinerja($bulan = null,$tahun = null){
        $pegawai = auth('web')->user();
        $nip = $pegawai->nip;
        $tunjangan = $this->kinerja->getTunjanganKinerja($nip,$bulan,$tahun);
        return $this->ApiSpecResponses($tunjangan);
    }
}
