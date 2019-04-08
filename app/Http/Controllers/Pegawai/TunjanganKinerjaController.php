<?php

namespace App\Http\Controllers\Pegawai;


use App\Http\Controllers\Controller;
use App\Repositories\KinerjaRepository;

class TunjanganKinerjaController extends Controller
{
    public function index()
    {
        return view('layouts.users.tunjangan-kinerja.index');
    }

    public function tunjanganKinerja($bulan = null, $tahun = null)
    {
        $kinerja = new KinerjaRepository();
        $pegawai = auth('web')->user();
        $nip = $pegawai->nip;
        $tunjangan = $kinerja->getTunjanganKinerja($nip, $bulan, $tahun);
        return $this->ApiSpecResponses($tunjangan);
    }
}
