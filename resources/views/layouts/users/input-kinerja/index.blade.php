@extends('layouts.users.partial.main')
@section('class','input-kinerja')
@section('content')
    <div class="main">
        <div class="nav-top-container">
            <div class="nav-top">
                <div class="title-nav">
                    <h4 class="mr-4">Input Kinerja</h4>
                    <span
                        class="badge text-white">{{ucfirst(\App\Models\MasterData\Hari::find(date('N'))->nama_hari)}}, {{date('d')}} {{ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan)}} {{date('Y')}}</span>
                </div>
                @include('layouts.users.partial.part.logout')
            </div>
        </div>
        <div class="sidebar2">

            <div class="hiddenIconMenu">
                <ul class="nav nav-pills" id="myTab" role="tablist">
                    <li><a class="active" id="hadir-tab" data-toggle="tab" href="#hadir" role="tab" aria-controls="home"
                           aria-selected="true"><span class="hadir">
                                    <img src="assets/images/icons/hadir.svg">
                                </span>
                        </a></li>
                    <li><a id="dinas-tab" data-toggle="tab" href="#dinas" role="tab" aria-controls="dinas"
                           aria-selected="false"><span class="dinas">
                                    <img src="assets/images/icons/perjalanan_dinas.svg">
                                </span></a></li>
                    <li><a id="cuti-tab" data-toggle="tab" href="#cuti" role="tab" aria-controls="cuti"
                           aria-selected="false"><span class="cuti">
                                    <img src="assets/images/icons/cuti.svg">
                                </span></a></li>
                    <li><a id="izin-tab" data-toggle="tab" href="#izin" role="tab" aria-controls="izin"
                           aria-selected="false"><span class="izin">
                                    <img src="assets/images/icons/izin.svg">
                                </span></a></li>
                    <li><a id="sakit-tab" data-toggle="tab" href="#sakit" role="tab" aria-controls="sakit"
                           aria-selected="false"><span class="sakit">
                                    <img src="assets/images/icons/sakit.svg">
                                </span></a></li>
                </ul>
            </div>

            <div class="menu">
                <div class="title-side-2">
                    <h6>Buat laporan kinerja</h6>
                </div>
                <!-- <div> -->
                <ul class="nav nav-pills" id="myTab" role="tablist">
                    <li><a class="active" id="hadir-tab" data-toggle="tab" href="#hadir" role="tab"
                           aria-controls="hadir"
                           aria-selected="true"><span class="hadir">
                                    <img src="assets/images/icons/hadir.svg">
                                </span>
                            <label>Hadir</label></a></li>
                    <li><a id="dinas-tab" data-toggle="tab" href="#dinas" role="tab" aria-controls="dinas"
                           aria-selected="false"><span class="dinas">
                                    <img src="assets/images/icons/perjalanan_dinas.svg">
                                </span><label>Perjalanan
                                Dinas</label></a></li>
                    <li><a id="cuti-tab" data-toggle="tab" href="#cuti" role="tab" aria-controls="cuti"
                           aria-selected="false"><span class="cuti">
                                    <img src="assets/images/icons/cuti.svg">
                                </span><label>Cuti</label></a></li>
                    <li><a id="izin-tab" data-toggle="tab" href="#izin" role="tab" aria-controls="izin"
                           aria-selected="false"><span class="izin">
                                    <img src="assets/images/icons/izin.svg">
                                </span><label>Izin</label></a></li>
                    <li><a id="sakit-tab" data-toggle="tab" href="#sakit" role="tab" aria-controls="sakit"
                           aria-selected="false"><span class="sakit">
                                    <img src="assets/images/icons/sakit.svg">
                                </span><label>Sakit</label></a></li>
                </ul>
                <!-- </div> -->
            </div>
        </div>

        <div class="main-content tab-content">
            <!-- start tab hadir -->
            <div class="tab-pane active" id="hadir" role="tabpanel" aria-labelledby="hadir-tab">
                <div class="container">
                    <form action="{{route('api.web.input-kinerja.post')}}" class="form-submit-kinerja active-form">
                        {{csrf_field()}}
                        <input type="hidden" name="jenis_kinerja" value="hadir">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group hadir">
                                    <!-- <input class="ml-2 mt-2" type="radio"> -->
                                    <h5>Hadir</h5> <label class="wrap-radio">
                                        <input type="radio" name="radio" checked>
                                        <span class="checkmark checkmark-blue"></span>
                                    </label>
                                </div>
                                <h6 class="mb-2 mt-4">Rincian Kinerja Harian</h6>
                                <textarea name="rincian_kinerja" autofocus rows="10" class="form-control"
                                          required></textarea>
                                <button type="submit" class="btn btn-custom float-right mt-3">Simpan</button>
                            </div>
                            <div class="col-md-4">
                                <div class="description">
                                    <h6>Input Kinerja</h6>
                                    <p>Lakukan Penginputan Kinerja Harian sebelum jam pulang Kantor. Nantinya Data ini
                                        akan
                                        dijadikan acuan untuk laporan Kinerja Anda.</p>
                                </div>
                                <div class="clock-side">
                                    <span class="clock">--:--</span>
                                </div>
                                <div class="day-side">
                                    <small>{{ucfirst(\App\Models\MasterData\Hari::find(date('N'))->nama_hari)}}
                                        , {{date('d')}} {{ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan)}} {{date('Y')}}</small>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- end tab hadir -->

            <!-- start tab perjalanan dinas -->
            <div class="tab-pane" id="dinas" role="tabpanel" aria-labelledby="dinas-tab">
                <div class="container">
                    <form action="{{route('api.web.input-kinerja.post')}}" class="form-submit-kinerja">
                        {{csrf_field()}}
                        <input type="hidden" name="jenis_kinerja" value="perjalanan_dinas">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group dinas">
                                    <!-- <input class="ml-2 mt-2" type="radio"> -->
                                    <h5>Perjalanan Dinas</h5> <label class="wrap-radio">
                                        <input type="radio" name="radio">
                                        <span class="checkmark checkmark-green"></span>
                                    </label>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 date-group">
                                        <label class="mb-2 mt-4">Tgl. Mulai</label>
                                        <br>
                                        <input name="tgl_mulai" class="datepicker" autocomplete="off" required/>
                                        <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <div class="col-md-6 date-group">
                                        <label class="mb-2 mt-4">Tgl. Berakhir</label>
                                        <br>
                                        <input name="tgl_selesai" class="datepicker" autocomplete="off" required/>
                                        <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                                <h6 class="mb-2">Rincian Kinerja Harian</h6>
                                <textarea name="rincian_kinerja" autofocus rows="10" class="form-control"
                                          required></textarea>
                                <button type="submit" class="btn btn-custom float-right mt-3">Simpan</button>
                            </div>
                            <div class="col-md-4">
                                <div class="description">
                                    <h6>Input Kinerja</h6>
                                    <p>Lakukan Penginputan Kinerja Harian sebelum jam pulang Kantor. Nantinya Data ini
                                        akan
                                        dijadikan acuan untuk laporan Kinerja Anda.</p>
                                </div>
                                <div class="clock-side">
                                    <span class="clock">--:--</span>
                                </div>
                                <div class="day-side">
                                    <small>{{ucfirst(\App\Models\MasterData\Hari::find(date('N'))->nama_hari)}}
                                        , {{date('d')}} {{ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan)}} {{date('Y')}}</small>
                                </div>

                            </div>

                        </div>
                    </form>
                </div>
            </div>
            <!-- end tab perjalanan dinas -->

            <!-- start tab perjalanan cuti -->
            <div class="tab-pane" id="cuti" role="tabpanel" aria-labelledby="cuti-tab">
                <div class="container">
                    <form action="{{route('api.web.input-kinerja.post')}}" class="form-submit-kinerja">
                        {{csrf_field()}}
                        <input type="hidden" name="jenis_kinerja" value="cuti">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group cuti">
                                    <!-- <input class="ml-2 mt-2" type="radio"> -->
                                    <h5>Cuti</h5> <label class="wrap-radio">
                                        <input type="radio" name="radio">
                                        <span class="checkmark checkmark-purple"></span>
                                    </label>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 date-group">
                                        <label class="mb-2 mt-4">Tgl. Mulai</label>
                                        <input name="tgl_mulai" class="datepicker" autocomplete="off" id="datepicker"
                                               required/>
                                        <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <div class="col-md-6 date-group">
                                        <label class="mb-2 mt-4">Tgl. Berakhir</label>
                                        <input name="tgl_selesai" class="datepicker" autocomplete="off" id="datepicker1"
                                               required/>
                                        <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                                <h6 class="mb-2">Keterangan</h6>
                                <textarea name="rincian_kinerja" autofocus rows="10" class="form-control"
                                          required></textarea>
                                <button type="submit" class="btn btn-custom float-right mt-3">Simpan</button>
                            </div>
                            <div class="col-md-4">
                                <div class="description">
                                    <h6>Input Kinerja</h6>
                                    <p>Lakukan Penginputan Kinerja Harian sebelum jam pulang Kantor. Nantinya Data ini
                                        akan
                                        dijadikan acuan untuk laporan Kinerja Anda.</p>
                                </div>
                                <div class="clock-side">
                                    <span class="clock">--:--</span>
                                </div>
                                <div class="day-side">
                                    <small>{{ucfirst(\App\Models\MasterData\Hari::find(date('N'))->nama_hari)}}
                                        , {{date('d')}} {{ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan)}} {{date('Y')}}</small>
                                </div>

                            </div>

                        </div>
                    </form>
                </div>
            </div>
            <!-- end tab perjalanan cuti -->

            <!-- start tab perjalanan izin -->
            <div class="tab-pane" id="izin" role="tabpanel" aria-labelledby="izin-tab">
                <div class="container">
                    <form action="{{route('api.web.input-kinerja.post')}}" class="form-submit-kinerja">
                        {{csrf_field()}}
                        <input type="hidden" name="jenis_kinerja" value="izin">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group izin">
                                    <!-- <input class="ml-2 mt-2" type="radio"> -->
                                    <h5>Izin</h5> <label class="wrap-radio">
                                        <input type="radio" name="radio">
                                        <span class="checkmark checkmark-orange"></span>
                                    </label>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 date-group">
                                        <label class="mb-2 mt-4">Tgl. Mulai</label>
                                        <input name="tgl_mulai" class="datepicker" autocomplete="off" id="datepicker"
                                               required/>
                                        <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <div class="col-md-6 date-group">
                                        <label class="mb-2 mt-4">Tgl. Berakhir</label>
                                        <input name="tgl_selesai" class="datepicker" autocomplete="off" id="datepicker1"
                                               required/>
                                        <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                                <h6 class="mb-2">Keterangan</h6>
                                <textarea name="rincian_kinerja" autofocus rows="10" class="form-control"
                                          required></textarea>
                                <button type="submit" class="btn btn-custom float-right mt-3">Simpan</button>
                            </div>
                            <div class="col-md-4">
                                <div class="description">
                                    <h6>Input Kinerja</h6>
                                    <p>Lakukan Penginputan Kinerja Harian sebelum jam pulang Kantor. Nantinya Data ini
                                        akan
                                        dijadikan acuan untuk laporan Kinerja Anda.</p>
                                </div>
                                <div class="clock-side">
                                    <span class="clock">--:--</span>
                                </div>
                                <div class="day-side">
                                    <small>{{ucfirst(\App\Models\MasterData\Hari::find(date('N'))->nama_hari)}}
                                        , {{date('d')}} {{ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan)}} {{date('Y')}}</small>
                                </div>

                            </div>

                        </div>
                    </form>
                </div>
            </div>
            <!-- end tab perjalanan izin -->

            <!-- start tab sakit -->
            <div class="tab-pane" id="sakit" role="tabpanel" aria-labelledby="sakit-tab">
                <div class="container">
                    <form action="{{route('api.web.input-kinerja.post')}}" class="form-submit-kinerja">
                        {{csrf_field()}}
                        <input type="hidden" name="jenis_kinerja" value="sakit">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group sakit">
                                    <!-- <input class="ml-2 mt-2" type="radio"> -->
                                    <h5>Sakit</h5> <label class="wrap-radio">
                                        <input type="radio" name="radio">
                                        <span class="checkmark checkmark-red"></span>
                                    </label>
                                </div>
                                <h6 class="mb-2 mt-4">Keterangan</h6>
                                <textarea name="rincian_kinerja" autofocus rows="10" class="form-control"
                                          required></textarea>
                                <button type="submit" class="btn btn-custom float-right mt-3">Simpan</button>
                            </div>
                            <div class="col-md-4">
                                <div class="description">
                                    <h6>Input Kinerja</h6>
                                    <p>Lakukan Penginputan Kinerja Harian sebelum jam pulang Kantor. Nantinya Data ini
                                        akan
                                        dijadikan acuan untuk laporan Kinerja Anda.</p>
                                </div>
                                <div class="clock-side">
                                    <span class="clock">--:--</span>
                                </div>
                                <div class="day-side">
                                    <small>{{ucfirst(\App\Models\MasterData\Hari::find(date('N'))->nama_hari)}}
                                        , {{date('d')}} {{ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan)}} {{date('Y')}}</small>
                                </div>

                            </div>

                        </div>
                    </form>
                </div>
            </div>
            <!-- end tab sakit -->
        </div>
    </div>
    @push('script')
        <script>
            $('[data-toggle=tab]').on('click', function () {
                var id = $(this).attr('href');
                var form = $('.tab-pane' + id).find('form');
                if (form) {
                    $('.tab-pane').find('form').removeClass('active-form')
                    $('.tab-pane').find('form').find('[name=radio]').attr('checked', false)
                }
                form.addClass('active-form');
                form.find('[name=radio]').attr('checked', 'checked')
            })
            $(document).on('submit', '.form-submit-kinerja.active-form', function (e) {
                e.preventDefault();
                var data = $(e.target);
                var action = this.action
                var that = this;
                swal({
                    title: 'Ingin Menyimpan Data Kinerja?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Iya, simpan data!',
                    cancelButtonText: 'Batalakan'
                }).then((result) => {
                    if (result.value) {
                        $('.loading').show();
                        $.post(action, data.serialize())
                            .then(function (res) {
                                $('.loading').hide();
                                if (res.diagnostic.code == 200) {
                                    swal(
                                        'Berhasil Menyimpan Data!',
                                        '',
                                        'success'
                                    );
                                    $(that)[0].reset()
                                } else {
                                    swal(
                                        'Gagal Menyimpan Data!',
                                        res.diagnostic.message,
                                        'warning'
                                    );
                                }
                            }, function (err) {
                                swal(
                                    'Gagal Menyimpan Data!',
                                    '',
                                    'warning'
                                );
                                $('.loading').hide();
                            })
                    }
                })
            })

            function getTime() {
                var date = new Date();
                var jam = (date.getHours() < 10 ? '0' + date.getHours() : date.getHours());
                var menit = (date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes())
                var separator = (date.getSeconds() % 2 ? '<span style="opacity: 0.5">:</span>' : '<span>:</span>')
                $('.clock').html(jam + separator + menit)
            }

            setInterval(function () {
                getTime()
            }, 1000)
        </script>
    @endpush
@endsection
