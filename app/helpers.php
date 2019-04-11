<?php

if (!function_exists('month')) {
    function month($date){
        return date('m', strtotime($date));
    }
}

if (!function_exists('year')) {
    function year($date){
        return date('Y', strtotime($date));
    }
}

if (!function_exists('namaBulan')) {
    function namaBulan($int){
        $nama = [1=> 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        return $nama[(int) $int];
    }
}
