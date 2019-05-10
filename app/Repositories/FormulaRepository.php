<?php

namespace App\Repositories;

class FormulaRepository extends BaseRepository {
    /**
     * bobot persentase penilaian kinerja
     */
    public static $kinerja = [
        'persentase' => 0.6,
        'sub' => null
    ];
    /**
     * bobot persentase penilaian absensi
     */
    public static $absen = [
        'persentase' => 0.3,
        'sub' => [
            'absen' => 0.6,
            'upacara' => 0.4
        ]
    ];
    /**
     * bobot persentase penilaian kepatuhan
     */
    public static $kepatuhan = [
        'persentase' => 0.1,
        'sub' => [
            'atasan' => [
                'lhkpn' => 0.4,
                'bmd' => 0.3,
                'tptgr' => 0.3
            ],
            'staff' => [
                'lhkpn' => 0,
                'bmd' => 0.5,
                'tptgr' => 0.5
            ]
        ]
    ];

    public function __construct(){}

    public function model(){}
    public function search($array){}

    public static function kinerja($akumulasiNilaiKinerjaHarian, $totalNilaiKinerjaBulanan){
        $capaianKinerja = ($akumulasiNilaiKinerjaHarian/$totalNilaiKinerjaBulanan) * 1;
        $kinerja = (object)self::$kinerja;

        return ($capaianKinerja * $kinerja->persentase);
    }

    public static function absen($akumulasiNilaiKehadiranKerjaHarian, $jumlahHariKerja, $jumlahNilaiKehadiranUpacara){
        $absen = self::absensi($akumulasiNilaiKehadiranKerjaHarian, $jumlahHariKerja);
        $upacara = self::upacara($jumlahNilaiKehadiranUpacara, $jumlahHariKerja);

        return ($absen + $upacara) * self::$absen['persentase'];
    }

    private static function absensi($akumulasiNilaiKehadiranKerjaHarian, $jumlahHariKerja){
        return ($akumulasiNilaiKehadiranKerjaHarian / $jumlahHariKerja) * self::$absen['sub']['absen'];
    }

    private static function upacara($jumlahNilaiKehadiranUpacara, $jumlahHariKerja){
        return ($jumlahNilaiKehadiranUpacara/$jumlahHariKerja) * self::$absen['sub']['upacara'];
    }

    public static function kepatuhan($statusPegawai, $lhkpn, $bmd, $tptgr){
        $kepatuhan = 0;
        $kepatuhan+= $lhkpn?self::$kepatuhan['sub'][$statusPegawai]['lhkpn']:0;
        $kepatuhan+= $bmd?self::$kepatuhan['sub'][$statusPegawai]['bmd']:0;
        $kepatuhan+= $tptgr?self::$kepatuhan['sub'][$statusPegawai]['tptgr']:0;

        return $kepatuhan * self::$kepatuhan['persentase'];
    }



}