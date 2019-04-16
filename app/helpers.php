<?php

if (!function_exists('month')) {
    function month($date){
        return (int)date('m', strtotime($date));
    }
}

if (!function_exists('year')) {
    function year($date){
        return (int)date('Y', strtotime($date));
    }
}

if (!function_exists('namaBulan')) {
    function namaBulan($int){
        $nama = [1=> 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return $nama[(int) $int];
    }
}
if (!function_exists('namaBulan')) {
    function formatDate2($date)
    {
        $d = date('d', strtotime($date));
        $bulan = (int)date('m', strtotime($date));
        $m = namaBulan($bulan);
        $y = date('Y', strtotime($date));
        return $d . " " . $m . " " . $y;
    }
}
if (!function_exists('namaBulan')) {
    function formatDate3($date)
    {
        $bulan = (int)date('m', strtotime($date));
        $m = namaBulan($bulan);
        $y = date('Y', strtotime($date));
        return $m . " " . $y;
    }
}
