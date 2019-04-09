<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>E-Kinerja Kabupaten Kolaka</title>
    <link rel="stylesheet" href="{{ asset('assets/css/app.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/datepicker3.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert/sweetalert2.min.css') }}">

</head>
<body>
    <div id="preload">
      <img src="{{ asset('assets/images/loading_ekinerja.svg')}}" width="150">
    </div>
    <section class="@yield('class')">
        <div class="burgerBtn">
            <div class="bar1"></div>
            <div class="bar2"></div>
            <div class="bar3"></div>
        </div>
        <div class="overlayProfile"></div>
        <div class="profile">
            <div class="imgProfile">
                <div class="closeProfile">&times;</div>
                <img src="{{asset('assets/images/bg_login_whitebase.png')}}" class="bgImg" />
                <div
                class="imgUser"
                style="background-image: url('{{ session('user')->get('foto')?url('').'/storage/'.session('user')->get('foto'):asset('assets/images/img-user.png') }}')"
                ></div>
                <div class="descUser mt-2">
                <h5 class="mb-1">{{ ucfirst(session('user')->get('nama')) }}</h5>
                <p>{{ session('user')->get('nip') }}</p>
                </div>
            </div>
    
            <div class="descProfile">
                <div class="dataForm">
                <h5 class="mb-2">Informasi</h5>
                <div>
                    <span>Jabatan</span>
                    <p>{{ ucwords(strtolower(session('user')->get('jabatan'))) }}</p>
                </div>
                <div>
                    <span>Tempat, Tgl.Lahir</span>
                    <p>{{ucwords(session('user')->get('tempat_lahir'))}}, {{ date('m',strtotime(session('user')->get('tanggal_lahir'))) }} , {{ session('user')->get('bulan_lahir') }} {{ date('Y',strtotime(session('user')->get('tanggal_lahir'))) }} </p>
                </div>
                <div>
                    <span>Agama</span>
                    <p>{{ ucwords(session('user')->get('agama')) }}</p>
                </div>
                <div>
                    
                    <label for="password">Kata Sandi</label>
                    <div class="input-group">
                        <input
                        id="password"
                        type="password"
                        disabled
                        value="password"
                        class="form-control"
                        />
                        <div class="input-group-prepend">
                        <button class="btn btn-warning btn-changePassword" title="Ganti Password">
                            <i class="fa fa-edit"></i>
                        </button>
                        </div>
                    </div>
                </div>
                <button 
                class="btn btn-block btn-out" 
                id="btn-logout"
                onclick="event.preventDefault();
                        document.getElementById('logout-form').submit();">Logout</button>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
                </form>
                </div>

                <div class="formChangePassword">
                <h5 class="mb-3">Ubah Kata Sandi</h5>
                <form id="change-password-form" action="{{ route('update-password') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="oldPassword">Kata Sandi Lama</label>
                        <input type="password" class="form-control" required name="oldPassword" id="oldPassword"placeholder="Kata Sandi Lama">
                        </div>
                    <div class="form-group">
                            <label for="newPassword">Kata Sandi Baru</label>
                            <input type="password" class="form-control" required name="newPassword" id="newPassword"placeholder="Kata Sandi Baru">
                    </div>
                    <div class="pt-4">
                        <button class="btn btn-custom-2 btn-cancelChange">Batal</button>
                        <button class="btn btn-custom">Simpan</button>
                    </div>
                </form>
                @push('script')
                    <script>
                        $('#change-password-form').on('submit',function(e){
                            e.preventDefault();
                            var data = $(e.target);
                            var form = this;
                            swal({
                                title: 'Ingin Mengubah Password?',
                                text: "",
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Iya, ubah password!',
                                cancelButtonText: 'Batalkan'
                            }).then((result) => {
                                if (result.value) {
                                    $('.preload').show();
                                    $.post(form.action, data.serialize())
                                        .then(function (res) {
                                            $('.preload').hide();
                                            
                                            if (res.diagnostic.code == 200) {
                                                swal(
                                                    'Password berhasil di ubah!',
                                                    '',
                                                    'success'
                                                );
                                                setTimeout(function(){
                                                    location.reload();
                                                },1000);

                                            } else {
                                                swal(
                                                    'Gagal Menyimpan Data!',
                                                    res.message,
                                                    'warning'
                                                );
                                            }
                                        }, function (err) {
                                            swal(
                                                'Gagal Menyimpan Data!',
                                                err.responseJSON.message,
                                                'warning'
                                            );
                                            $('.overlayProfile').removeClass('show');
                                            
                                            $('.preload').hide();
                                        })
                                }
                            })
                        })
                    </script>
                @endpush
                </div>
            </div>
        </div>
        <div class="overlay">
            <div class="close-side"><i class="fas fa-times"></i></div>
        </div>
        <div class="sidebar1">
            <div class="brand">
                <img src="{{ asset('assets/images/logo_ekinerja.png') }}" width="120">
            </div>
            <ul>
                <li class="{{str_contains(url()->current(),route('sasaran-kerja.index')) ? 'active' : ''}}">
                    <a href="{{ route('sasaran-kerja.index') }}">
                        <i class="material-icons">work</i>
                        <span>Sasaran Kerja</span>
                    </a>
                </li>
                @can('monitoring-absen')
                <li class="{{str_contains(url()->current(),route('monitoring.absen.index')) ? 'active' : ''}}">
                    <a href="{{route('monitoring.absen.index')}}">
                        <i class="material-icons">recent_actors</i> 
                        <span>Monitoring Absen</span>
                    </a>
                </li>
                @endcan
                @can('rekap-bulanan')
                <li class="{{str_contains(url()->current(),route('rekap-bulanan.index')) ? 'active' : ''}}">
                    <a href="{{route('rekap-bulanan.index')}}" >
                        <i class="material-icons">library_books</i>
                        <span>Rekap Bulanan</span>
                    </a>
                </li>
                @endcan
                @can('input-kinerja')
                <li class="{{str_contains(url()->current(),route('input-kinerja.index')) ? 'active' : ''}}">
                    <a href="{{route('input-kinerja.index')}}" >
                        <i class="material-icons">assignment</i>
                        <span>Input Kinerja</span>
                    </a>
                </li>
                @endcan
                @can('penilaian-kinerja')
                <li class="{{str_contains(url()->current(),route('penilaian-kinerja.index')) ? 'active' : ''}}">
                    <a href="{{route('penilaian-kinerja.index')}}" >
                        <i class="material-icons">how_to_reg</i>
                        <span>Penilaian Kinerja</span>
                    </a>
                </li>
                @endcan
                @can('tunjangan-kinerja')
                <li class="{{str_contains(url()->current(),route('tunjangan-kinerja.index')) ? 'active' : ''}}">
                    <a href="{{route('tunjangan-kinerja.index')}}" >
                        <i class="material-icons">insert_chart</i>
                        <span>Tunjangan Kinerja</span>
                    </a>
                </li>
                @endcan
            </ul>
            <ul class="wrapToggle">
                <li>
                    @can('master-data')
                    <label class="mr-2">
                        <a href="{{route('pegawai.index')}}">Masuk Panel Admin <i class="fas fa-cogs"></i> </a> 
                    </label>
                    @endcan
                </li>
                <li>
                    <a href="panduan-monitoring-absen.html">
                        Panduan Pengguna
                        <i class="material-icons">help_outline</i>
                    </a>
                </li>
                <li>
                    <div class="toggleSwitch">
                        <label class="mr-2">Mode Malam</label>
                        <label class="switch">
                            <input id="toggle-switch" type="checkbox" checked>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </li>
            </ul>
        </div>
