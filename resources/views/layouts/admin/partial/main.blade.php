<!DOCTYPE html>
<html lang="en">
<head>
  @include('layouts.admin.partial.part.header')
</head>
<body>
<div id="preload">
  <img src="{{ asset('assets/images/loading_ekinerja.svg')}}" width="150">
</div>
<section class="component">
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
                    <p>{{ 'Administrator' }}</p>
                </div>
                <div>
                    <span>Tempat, Tgl.Lahir</span>
                    <p>{{ ucwords(session('user')->get('tempat_lahir'))}}, {{ date('m',strtotime(session('user')->get('tanggal_lahir'))) }} , {{ session('user')->get('bulan_lahir') }} {{ date('Y',strtotime(session('user')->get('tanggal_lahir'))) }} </p>
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
                <form id="change-password-form" action="{{ route('api.web.master-data.pegawai.update-password') }}" method="POST">
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
                                title: 'Ingin Merubah Password?',
                                text: "",
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Iya, rubah password!',
                                cancelButtonText: 'Batalkan'
                            }).then((result) => {
                                if (result.value) {
                                    $('.preload').show();
                                    $.post(form.action, data.serialize())
                                        .then(function (res) {
                                            $('.preload').hide();
                                            
                                            if (res.diagnostic.code == 200) {
                                                swal(
                                                    'Password berhasil di rubah!',
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
    @include('layouts.admin.partial.part.sidebar')
    @yield('content')
</section>
</body>
@include('layouts.admin.partial.part.footer')
</html>
