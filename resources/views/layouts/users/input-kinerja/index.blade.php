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
                    <form action="{{route('input-kinerja.api.post')}}" class="form-submit-kinerja active-form">
                        {{csrf_field()}}
                        <input type="hidden" name="id">
                        <input type="hidden" name="jenis_kinerja" value="hadir">
                        {{--                        <input type="hidden" name="status">--}}
                        <div class="row">
                            <div class="col-md-8">
                                <div class="input-group hadir">
                                    <!-- <input class="ml-2 mt-2" type="radio"> -->
                                    <h5 class="mb-4 font-weight-bold">Hadir</h5>
                                    <label class="wrap-radio">
                                        <!-- <input type="radio" name="radio" />
                                        <span class="checkmark checkmark-blue"></span> -->
                                    </label>
                                </div>
                                @if (!$skp_selesai->isEmpty())
                                    <div class="col-md-12 mb-1">
                                        <label class="text-secondary" style="font-size: 1.2em">SKP Selesai</label>
                                        @foreach($skp_selesai As $ss)
                                            <label class="container-check">
                                                <p>{{$ss->skpTask->task}}</p>
                                                <input type="checkbox" name="skp_selesai[{{$ss->id}}]" checked readonly
                                                       onclick="return false">
                                                <span class="checkmark"></span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="col-md-12 mb-1">
                                    <label class="text-secondary" style="font-size: 1.2em">List SKP</label>
                                    <div class="wrap-progress">
                                        <label>0%</label>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar" role="progressbar" style="width: 0%"
                                                 aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-1">
                                    @foreach($skp_pegawai As $sp)
                                        <label class="container-check">
                                            <p>{{$sp->skpTask->task}}</p>
                                            <input type="checkbox" name="skp_pegawai[]"
                                                   value="{{$sp->id}}" {{$sp->status ? 'checked' : ''}}>
                                            <span class="checkmark"></span>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="col-md-12 mt-4" id="media-text">
                                    <label class="text-secondary" style="font-size: 1.2em">Upload Dokumen</label>
                                    {{--                                    <div class="uploads mb-3">--}}
                                    {{--                                        <img src="/assets/images/icons/word.svg">--}}
                                    {{--                                        <label>Lorem ipsum dolor sit amet consectetur adipisicing</label>--}}
                                    {{--                                        <button>--}}
                                    {{--                                            <i class="material-icons">delete</i>--}}
                                    {{--                                            Hapus--}}
                                    {{--                                        </button>--}}
                                    {{--                                    </div>--}}
                                </div>

                                <div class="col-md-12 mb-3" id="media">
                                    {{--                                    <form action="/file-upload" class="dropzone">--}}
                                    <div class="dropzone">
                                        <div class="dz-message " data-dz-message>
                                            <i class="material-icons">cloud_upload</i>
                                            <span class="ml-3">Upload Dokumen</span>
                                            <div class="fallback">
                                                <input name="doc[]" type="file" multiple/>
                                            </div>
                                        </div>
                                    </div>
                                    {{--                                    </form>--}}
                                </div>

                                <div class="col-md-12">
                                    <div id="rincian-kinerja-terkirim" style="display: none">
                                        Rincian Kinerja :
                                        <p></p>
                                    </div>
                                    <textarea class="rincianKinerja" name="rincian_kinerja" rows="3"
                                              placeholder="Rincian Kinerja"></textarea>
                                    {{--<div class="rincianKinerja" contenteditable="true">
                                        <span>Rincian Kinerja</span>
                                        <p id="rincian-kinerja">
                                            Kinerja Di Sini!!
                                        </p>
                                        <input type="hidden" name="rincian_kinerja" required>
                                    </div>--}}
                                    <a style="color: white; display: none" id="hapus-kinerja"
                                       class="btn btn-warning mt-3">Hapus</a>
                                    {{--                                    <button type="submit" data-status="5" class="btn btn-custom mt-3">Simpan</button>--}}
                                    <button id="kirim-kinerja" type="submit" data-status="0"
                                            class="btn btn-success float-right mt-3">Kirim
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4 time-desc">
                                <div class="description">
                                    <h6>Input Kinerja</h6>
                                    <p>
                                        Lakukan Penginputan Kinerja Harian sebelum jam pulang
                                        Kantor. Nantinya Data ini akan dijadikan acuan untuk
                                        laporan Kinerja Anda.
                                    </p>
                                </div>
                                <div class="clock-side clock"><span>--:--</span></div>
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
                    <form action="{{route('input-kinerja.api.post')}}" class="form-submit-kinerja">
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
                                        <label>Tgl. Mulai</label>
                                        <br>
                                        <input name="tgl_mulai" class="datepicker" autocomplete="off" required/>
                                        <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <div class="col-md-6 date-group">
                                        <label>Tgl. Berakhir</label>
                                        <br>
                                        <input name="tgl_selesai" class="datepicker" autocomplete="off" required/>
                                        <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                                <h6 class="mb-2">Rincian Kinerja Harian</h6>
                                <textarea name="rincian_kinerja" autofocus rows="10" class="form-control"
                                          required></textarea>
                                <button type="submit" class="btn btn-custom float-right mt-3">Kirim</button>
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
                    <form action="{{route('input-kinerja.api.post')}}" class="form-submit-kinerja">
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
                                        <label>Tgl. Mulai</label>
                                        <br>
                                        <input name="tgl_mulai" class="datepicker" autocomplete="off" id="datepicker"
                                               required/>
                                        <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <div class="col-md-6 date-group">
                                        <label>Tgl. Berakhir</label>
                                        <br>
                                        <input name="tgl_selesai" class="datepicker" autocomplete="off" id="datepicker1"
                                               required/>
                                        <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                                <h6 class="mb-2">Keterangan</h6>
                                <textarea name="rincian_kinerja" autofocus rows="10" class="form-control"
                                          required></textarea>
                                <button type="submit" class="btn btn-custom float-right mt-3">Kirim</button>
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
                    <form action="{{route('input-kinerja.api.post')}}" class="form-submit-kinerja">
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
                                        <label>Tgl. Mulai</label>
                                        <br>
                                        <input name="tgl_mulai" class="datepicker" autocomplete="off" id="datepicker"
                                               required/>
                                        <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <div class="col-md-6 date-group">
                                        <label>Tgl. Berakhir</label>
                                        <br>
                                        <input name="tgl_selesai" class="datepicker" autocomplete="off" id="datepicker1"
                                               required/>
                                        <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                </div>
                                <h6 class="mb-2">Keterangan</h6>
                                <textarea name="rincian_kinerja" autofocus rows="10" class="form-control"
                                          required></textarea>
                                <button type="submit" class="btn btn-custom float-right mt-3">Kirim</button>
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
                    <form action="{{route('input-kinerja.api.post')}}" class="form-submit-kinerja">
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
                                <button type="submit" class="btn btn-custom float-right mt-3">Kirim</button>
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
            $('[type=submit]').on('click', function () {
                $(this).addClass('active').siblings().removeClass('active')
            })

            function persentaseSkp() {
                skp_selesai = $('[name*=skp_selesai]').length ? $('[name*=skp_selesai]').length : 0;
                skp = $('[name*=skp_pegawai]').length ? $('[name*=skp_pegawai]').length : 0
                skp_checked = $('[name*=skp_pegawai]:checked').length ? $('[name*=skp_pegawai]:checked').length : 0
                persentase = 0;
                if (skp + skp_selesai > 0) {
                    persentase = (skp_checked + skp_selesai) / (skp + skp_selesai) * 100;
                }
                $('.wrap-progress').find('label').html(Math.floor(persentase) + '%')
                $('.wrap-progress').find('.progress-bar').css({width: persentase + '%'})
            }

            function getKinerja() {
                $.get('{{route('input-kinerja.api.get-kinerja')}}', {'_token': '{{csrf_token()}}'})
                    .then(function (res) {
                        if (res.response != null) {
                            if (res.response.jenis_kinerja == 'hadir') {
                                $('#hadir [name=id]').val(res.response.id)
                                if (res.response.skp_pegawai.length > 0) {
                                    res.response.skp_pegawai.map(function (val) {
                                        $('[name*=skp_pegawai][value=' + val.id + ']').click()
                                    })
                                    $('[name*=skp_pegawai]').attr('disabled', true);
                                }
                                if (res.response.media.length > 0) {
                                    media = res.response.media.map(function (val) {
                                        return '<div class="downloads mb-3">\n' +
                                            '                              <img src="/assets/images/icons/word.svg">\n' +
                                            '                              <label>' + val.nama_media + '</label>\n' +
                                            '                              <button onclick="window.open(\'' + val.media + '\'); return false" type="submit">\n' +
                                            '                                  <i class="material-icons mr-1">cloud_download</i>\n' +
                                            '                                  Download\n' +
                                            '                              </button>\n' +
                                            '                          </div>'
                                    });
                                    $('#media').html('<div>' + media.join('') + '</div>');
                                } else {
                                    $('#media-text').hide();
                                }
                                $('#rincian-kinerja-terkirim>p').html(res.response.rincian_kinerja)
                                $('#rincian-kinerja-terkirim').show();
                                $('.rincianKinerja,.dropzone').hide();
                                $('#hapus-kinerja').show();
                                $('#kirim-kinerja').hide();
                            }
                        }
                    }, function () {

                    })
            }

            persentaseSkp();
            getKinerja();
            {{--var getDraft = function() {--}}
            {{--    $.get('{{route('input-kinerja.api.draft')}}', {--}}
            {{--        '_token': '{{csrf_token()}}'--}}
            {{--    })--}}
            {{--        .then(function (res) {--}}
            {{--            data = res.response;--}}
            {{--            $('#hadir').find('[name=id]').val(data.id)--}}
            {{--            $('#hadir').find('[name=rincian_kinerja]').val(data.rincian_kinerja)--}}
            {{--            $('#hadir').find('#rincian-kinerja').text(data.rincian_kinerja)--}}
            {{--            res.response.skp_pegawai.forEach(function(val){--}}
            {{--                $('[name="skp_pegawai['+val.id+']"]').attr('checked',true);--}}
            {{--            })--}}
            {{--            persentaseSkp();--}}
            {{--            $('#hapus-kinerja').show();--}}
            {{--        }, function (err) {--}}
            {{--            $('#hadir').find('[name=id]').val('')--}}
            {{--            $('#hadir').find('[name=rincian_kinerja]').val('')--}}
            {{--            $('#hadir').find('#rincian-kinerja').text('')--}}
            {{--            $('#hapus-kinerja').hide();--}}
            {{--        })--}}
            {{--}--}}
            // getDraft();
            $('#hapus-kinerja').on('click', function (e) {
                e.preventDefault();
                id = $('#hadir').find('[name=id]').val()
                if (id) {
                    swal({
                        title: 'Ingin Menghapus Kinerja ?',
                        text: "",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Iya, hapus kinerja!',
                        cancelButtonText: 'Batalkan'
                    }).then((result) => {
                        if (result.value) {
                            $.post('{{route('input-kinerja.api.delete-draft',['id' => ''])}}/' + id, {
                                '_token': '{{csrf_token()}}'
                            })
                                .then(function (res) {
                                    swal(
                                        res.response.message,
                                        '',
                                        'success'
                                    );
                                    $('#hadir').find('[name=id]').val('')
                                    location.reload()
                                    // getDraft()
                                }, function (err) {
                                    console.log(err)
                                })
                        }
                    })
                }
            })
            $('[name*=skp_pegawai]').on('click', function () {
                persentaseSkp();
            })
            $(document).on('submit', '.form-submit-kinerja.active-form', function (e) {
                e.preventDefault();
                // text = $('#rincian-kinerja').text();
                // $('#hadir').find('[name=rincian_kinerja]').val(text);
                var action = this.action;
                // var status = $('[type=submit].active').data('status');
                // $('[name=status]').val(status);
                // if (status == 5){
                //     info = 'simpan'
                //     info2 = 'Menyimpan'
                // } else {
                info = 'kirim'
                info2 = 'Mengirim'
                // }
                var formData = new FormData($(this)[0]);

                swal({
                    title: 'Ingin ' + info2 + ' Data Kinerja?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Iya, ' + info + ' data!',
                    cancelButtonText: 'Batalkan'
                }).then((result) => {
                    if (result.value) {
                        $('.loading').show();
                        $.ajax({
                            url: action,
                            type: "POST",
                            data: formData,
                            success: function (res) {
                                $('.form-submit-kinerja.active-form')[0].reset();
                                $('.loading').hide();
                                if (res.diagnostic.code == 200) {
                                    swal(
                                        'Berhasil ' + info2 + ' Data!',
                                        '',
                                        'success'
                                    );
                                    location.reload()
                                    // getDraft()
                                } else {
                                    swal(
                                        'Gagal ' + info2 + ' Data!',
                                        res.diagnostic.message,
                                        'warning'
                                    );
                                }
                            },
                            error: function () {
                                swal(
                                    'Gagal ' + info2 + ' Data!',
                                    '',
                                    'warning'
                                );
                                $('.loading').hide();
                            },
                            cache: false,
                            contentType: false,
                            processData: false
                        });
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
