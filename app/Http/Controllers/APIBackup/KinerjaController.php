<?php

namespace App\Http\Controllers\APIBackup;

use App\Repositories\KinerjaRepository;
use Illuminate\Http\Request;

class KinerjaController extends ApiController
{
    protected $kinerja;

    public function __construct(KinerjaRepository $kinerja)
    {
        $this->kinerja = $kinerja;
    }

    public function getKinerjaTersimpan()
    {
        $nip = auth('web')->user()->nip;
        if ($kinerja_tersimpan = $this->kinerja->getKinerjaTersimpan($nip)) {
            return $this->ApiSpecResponses($kinerja_tersimpan);
        }
        return null;
    }

    public function hapusKinerjaTersimpan($id)
    {
        $nip = auth('web')->user()->nip;
        if ($this->kinerja->deleteKinerjaTersimpan($id, $nip)) {
            return $this->ApiSpecResponses([
                'message' => 'Berhasil menghapus draft'
            ]);
        }
        return $this->ApiSpecResponses([
            'message' => 'Gagagl menghapus draft'
        ]);
    }

    public function inputKinerja(Request $request)
    {
        $nip = auth('web')->user()->nip;
        return $this->ApiSpecResponses($this->kinerja->inputKinerja($request->input(), $nip));
    }

    public function tunjanganKinerja($bulan = null, $tahun = null)
    {
        $pegawai = auth('web')->user();
        $nip = $pegawai->nip;
        $tunjangan = $this->kinerja->getTunjanganKinerja($nip, $bulan, $tahun);
        return $this->ApiSpecResponses($tunjangan);
    }
}
