<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E-Kinerja Kabupaten Kolaka</title>
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <!-- endinject -->
</head>
<body>
    <section class="monitoring-absen">
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
                <img src="assets/images/logo.svg" width="120">
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


        <div class="main">
            <div class="nav-top-container">

                <div class="group-search">
                    <span><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" placeholder="Cari Nama / NIP Pegawai">
                </div>

                <div class="nav-top">
                    <div class="img-profile" id="user-profile" style="background-image: url('assets/images/img-user.png');">
                    </div>

                    <div class="profile">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="profile-img">
                                    <!-- image profile show -->
                                    <div class="img-profile" style="background-image: url('assets/images/img-user.png');">
                                    </div>
                                    <!-- end -->
                                </div>
                                <br>
                                <div class="profile-name">
                                    <label>Administrator</label>
                                </div>
                            </div>
                        </div>
                        <a href="login.html" class="btn btn-block" id="btn-logout">Logout</a>
                    </div>
                </div>
            </div>
