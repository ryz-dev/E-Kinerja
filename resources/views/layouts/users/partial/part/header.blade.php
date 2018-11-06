<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E-Kinerja Kabupaten Kolaka</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/datepicker3.css') }}">
</head>
<body>
    <section id="main-section" class="rekap-bulanan">
        <div class="burgerBtn">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
        </div>
        <div class="overlay">
            <div class="close-side"><i class="fas fa-times"></i></div>
        </div>
        <div class="sidebar1">
            <div class="brand">
                <img src="{{ asset('assets/images/logo.svg') }}" width="120">
            </div>
            <ul>
                <li><a href="index.html" class="active">Monitoring Absen</a></li>
                <li><a href="rekap-bulanan.html">Rekap Bulanan</a></li>
                <li><a href="input-kinerja.html">Input Kinerja</a></li>
                <li><a href="penilaian-kinerja.html">Penilaian Kinerja</a></li>
                <li><a href="penilaian-etika.html">Penilaian Etika</a></li>
                <li><a href="tunjangan-kinerja.html">Tunjangan Kinerja</a></li>
            </ul>
            <div class="toggleSwitch">
                <label class="mr-2">Mode Malam</label>
                <label class="switch">
                    <input id="toggle-switch" type="checkbox" checked>
                    <span class="slider round"></span>
                </label>
            </div>
        </div>        
