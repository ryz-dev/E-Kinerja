@extends('layouts.users.partial.main')
@section('class','tunjangan-kinerja')
@section('content')
    <div class="main">
        <div class="nav-top-container">
            <div class="nav-top">
                <div class="title-nav float-left">
                    <h4 class="mr-3 float-left">Tunjangan Kinerja</h4>
                    <span
                        class="badge text-white">{{date('d')}} {{ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan)}} {{date('Y')}}</span>
                </div>

                <div class="img-profile" id="user-profile" style="background-image: url('assets/images/img-user.png');">
                </div>

                @include('layouts.users.partial.part.logout')
            </div>

        </div>
        <div class="main-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12 mt-3 control-date-btn">
                            <div class="date-group">
                                <h5>{{ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan)}} {{date('Y')}}</h5>
                            </div>
                            <div class="date-group">
                                <span class="icon-date"><i class="material-icons">event</i></span>
                                <input id="date-rekap" class="datepicker" placeholder="Pilih Bulan"/>
                            </div>

                            <div class="btn-control-group">
                                <button class="btn btn-rounded active" id="bulan-sebelumnya" data-value="-1"><i
                                        class="fas fa-angle-left"></i></button>
                                <button class="btn btn-rounded active" id="bulan-selanjutnya" data-value="1"><i
                                        class="fas fa-angle-right"></i></button>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="row wrap-diagram-container">
                    <div class="col-md-12 diagram-container">
                        <div class="box-diagram" id="diagram1">
                            <div>
                                <label class="float-right">ABSEN (
                                    <x id="persentase-absen">0</x>
                                    %)</label>
                            </div>
                            <div class="subContainer">
                                <div>
                                    <canvas id="chart-absen" height="50vh" width="50vw"></canvas>
                                </div>
                                <div class="capaian text-center">
                                    <h2><span id="total-absen">0</span>%</h2>
                                    Pencapaian <span id="pencapaian-absen">0</span>%
                                </div>
                            </div>
                        </div>
                        <div class="box-diagram" id="diagram2">
                            <div class="persen">
                                <label class="float-right">KINERJA (
                                    <x id="persentase-kinerja">0</x>
                                    %)</label>
                            </div>
                            <div class="subContainer">
                                <div>
                                    <canvas id="chart-kinerja" height="50vh" width="50vw"></canvas>
                                </div>
                                <div class="capaian text-center">
                                    <h2><span id="total-kinerja">0</span>%</h2>
                                    <span>Pencapaian <span id="pencapaian-kinerja">0</span>%</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-diagram btn-detail-ktj" id="diagram3">
                            <div class="persen">
                                <label class="float-right">KEPATUHAN (
                                    <x id="persentase-kepatuhan">0</x>
                                    %)</label>
                            </div>
                            <div class="subContainer">
                                <div>
                                    <canvas id="chart-kepatuhan" height="50vh" width="50vw"></canvas>
                                </div>
                                <div class="capaian text-center">
                                    <h2><span id="total-kepatuhan">0</span>%</h2>
                                    <span>Pencapaian <span id="pencapaian-kepatuhan">0</span>%</span>
                                </div>
                            </div>
                        </div>
                        <div class="box-diagram" id="diagram4">
                            <div class="persen">
                                <label class="float-right">TUNJANGAN </label>
                                <div class="clearfix"></div>
                            </div>
                            <div class="subContainer">
                                <div>
                                    <canvas id="chart-tunjangan" height="50vh" width="50vw"></canvas>
                                </div>
                                <div class="capaian text-center">
                                    <h2><sup>Rp.</sup><span id="total-tunjangan"></span> jt</h2>
                                    <span>Pencapaian <x id="total">0</x>
                                    %</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-responsive table-pegawai">
                            <thead>
                            <tr>
                                <th scope="col">Hari, Tanggal</th>
                                <th scope="col">Absen</th>
                                <th scope="col">Kinerja</th>
                                <th scope="col">Detail</th>
                            </tr>
                            </thead>
                            <tbody id="data-tunjangan">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-detail">
        <input type="hidden" id="current-index">
        <div class="modal-overlay">
            <!-- button close -->
            <div class="close">
                <i class="fas fa-times"></i>
            </div>
            <!-- button control -->
            <a class="control-left" data-next="-1">
                <i class="fas fa-angle-left"></i>
            </a>
            <a class="control-right" data-next="1">
                <i class="fas fa-angle-right"></i>
            </a>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="modal-konten mySlides">
                            <div class="title-name">
                                <div class="img-user" id="user-modal"
                                     style="background-image: url('assets/images/img-user.png');">
                                </div>
                                <h6 class="detail-nama"></h6>
                                <span class="detail-nip"></span>
                                <span class="badge text-white float-right" id="detail-tgl"></span>
                                <div class="clearfix"></div>
                            </div>
                            <div class="desc-detail">
                                <div class="title-detail">
                                    <h4 id="detail-jenis-kinerja"></h4>
                                </div>
                                <small>Jam Masuk - Jam Pulang</small>
                                <br>
                                <label><span id="detail-jam-masuk">--:--</span> - <span id="detail-jam-pulang">--:--</span></label>
                                <hr>
                                <div class="title-values">
                                    <h4>Penilaian Kinerja</h4>
                                    <span style="" class="check-list">
{{--                                            <i class="material-icons">check</i>--}}
                                        </span>
                                    <div class="value-kinerja">
                                        <h2 id="nilai_kinerja"></h2>
                                    </div>
                                </div>
                                <div class="row style-modal-content">
                                    <div class="col-md-12 mb-1" id="skp-progress">
                                        <label class="text-secondary" style="font-size: 1.2em">List SKP</label>
                                        <div class="wrap-progress">
                                            <label>0%</label>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar" role="progressbar" style="width: 0%"
                                                     aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-1" id="skp-pegawai">

                                    </div>

                                    <div class="col-md-12 mt-2" id="media-pegawai">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6 class="mb-2 mt-4">Rincian Kinerja Harian</h6>
                                        <div class="desc-kinerja">
                                            <p id="detail-kinerja"></p>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <h6 class="mb-2 mt-4">Keterangan Penilaian</h6>
                                        <div class="desc-kinerja">
                                            <p id="detail-keterangan-approve"></p>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-detail">
        <div class="modal-overlay-ktj">
            <!-- button close -->
            <div class="close">
                <i class="fas fa-times"></i>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="modal-konten-ktj mySlides-ktj">
                            <div class="title-name">
                                <div class="img-user" id="user-modal"
                                     style="background-image: url('assets/images/img-user.png');">
                                </div>
                                <h6 class="detail-nama"></h6>
                                <span class="detail-nip"></span>
{{--                                <span class="badge text-white float-right detail-tgl></span>--}}
                                <div class="clearfix"></div>
                            </div>
                            <div class="desc-detail">
                                <div class="title-detail">
                                    <h4>Kepatuhan & Tanggungjawab</h4>
                                    <img src="assets/images/icons/upacara.svg" class="iconUpacara">
                                </div>
                                <small>Periode</small>
                                <br>
                                <h5 id="periode"></h5>
                                <hr>
                                <!-- new version -->
                                <div class="row style-modal-content">
                                    <div class="col-md-12 mb-1">
                                        <label>List Kepatuhan & Tanggungjawab</label>
                                        <div class="kepatuhan-holder">

                                        </div>
                                        <div class="valuePersen" style="width: 210px">
                                            <h1><span class="pencapaian-kepatuhan">0</span>%</h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('script')
        <script>
            var ctx1 = document.getElementById("chart-absen").getContext("2d");
            var ctx2 = document.getElementById("chart-kinerja").getContext("2d");
            var ctx3 = document.getElementById("chart-kepatuhan").getContext("2d");
            var ctx4 = document.getElementById("chart-tunjangan").getContext("2d");
            var data_response;
            var max_index = 0;
            var chart = chart2 = chart3 = chart4 = null;
            var loadData = function (bulan, tahun) {
                $('#preload').show();
                $.get('{{route('tunjangan-kinerja.api')}}' + (bulan ? '/' + bulan : '') + (tahun ? '/' + tahun : ''))
                    .then(function (res) {
                        $('#persentase-absen').html(res.response.persentase.absen);
                        $('#persentase-kinerja').html(res.response.persentase.kinerja);
                        $('#persentase-kepatuhan').html(res.response.persentase.kepatuhan);

                        $('#pencapaian-absen').html(res.response.pencapaian.absen);
                        $('#pencapaian-kinerja').html(res.response.pencapaian.kinerja);
                        $('#pencapaian-kepatuhan').html(res.response.pencapaian.kepatuhan);
                        $('.pencapaian-kepatuhan').html(res.response.pencapaian.kepatuhan);

                        $('#total-absen').html(res.response.total.absen);
                        $('#total-kinerja').html(res.response.total.kinerja);
                        $('#total-kepatuhan').html(res.response.total.kepatuhan);

                        $('#total').html(res.response.total.total)
                        $('#total-tunjangan').html(res.response.total_tunjangan_diterima_juta)
                        $('.detail-nama').html(res.response.pegawai.nama)
                        $('.detail-nip').html(res.response.pegawai.nip)
                        data_response = res.response
                        var data_kinerja = res.response.data_kepatuhan;
                        var list_kepatuhan = '';
                        for (key in data_kinerja.list_kepatuhan){
                            var check = data_kinerja[key] == 1 ? '<i class="material-icons">check</i>' : ''
                            list_kepatuhan += '<div class="checklist-kepatuhan">\n' +
                                '     <span class="check-list">'+check+'</span>\n' +
                                '     <label>'+data_kinerja.list_kepatuhan[key]+'</p></label>\n' +
                                ' </div>'
                        }
                        $('.kepatuhan-holder').html(list_kepatuhan);
                        $('#periode').html((typeof data_kinerja.tanggal_periode != 'undefined' ? data_kinerja.tanggal_periode : ''))
                        var dataAbsen = {
                            labels: ["Absen",""],
                            datasets: [
                                {
                                    label: "My First dataset",
                                    backgroundColor: ["#6C5CE7", "#d8dadc"],
                                    borderColor: ["#6C5CE7", "#d8dadc"],
                                    data: [parseInt(res.response.pencapaian.absen), 100 - parseInt(res.response.pencapaian.absen)]
                                }
                            ]
                        };

                        var dataKinerja = {
                            labels: ["Kinerja",""],
                            datasets: [
                                {
                                    label: "My First dataset",
                                    backgroundColor: ["#F25857", "#d8dadc"],
                                    borderColor: ["#F25857", "#d8dadc"],
                                    data: [parseInt(res.response.pencapaian.kinerja), 100 - parseInt(res.response.pencapaian.kinerja)]
                                }
                            ]
                        };

                        var dataKepatuhan = {
                            labels: ["Kepatuhan", ""],
                            datasets: [
                                {
                                    label: "My First dataset",
                                    backgroundColor: ["#3ab894", "#d8dadc"],
                                    borderColor: ["#3ab894", "#d8dadc"],
                                    data: [parseInt(res.response.pencapaian.kepatuhan), 100 - parseInt(res.response.pencapaian.kepatuhan)]
                                }
                            ]
                        };

                        var dataTunjangan = {
                            labels: ["Tunjangan",""],
                            datasets: [
                                {
                                    label: "My First dataset",
                                    backgroundColor: ["#0984E3", "#d8dadc"],
                                    borderColor: ["#0984E3", "#d8dadc"],
                                    data: [parseInt(res.response.total.total), 100 - parseInt(res.response.total.total)]
                                }
                            ]
                        };

                        // --------
                        if (chart)
                            chart.destroy();
                        if (chart2)
                            chart2.destroy();
                        if (chart3)
                            chart3.destroy();
                        if (chart4)
                            chart4.destroy();

                        chart = new Chart(ctx1, {
                            type: "pie",
                            data: dataAbsen,
                            options: {
                                responsive: true,
                                maintainAspectRatio: true
                            }
                        });

                        chart2 = new Chart(ctx2, {
                            type: "pie",
                            data: dataKinerja,
                            options: {
                                responsive: true,
                                maintainAspectRatio: true
                            }
                        });

                        var chart3 = new Chart(ctx3, {
                            type: "pie",
                            data: dataKepatuhan,
                            options: {
                                responsive: true,
                                maintainAspectRatio: true
                            }
                        });

                        chart4 = new Chart(ctx4, {
                            type: "pie",
                            data: dataTunjangan,
                            options: {
                                responsive: true,
                                maintainAspectRatio: true
                            }
                        });
                        $('#preload').hide();
                        window['trigger'] = 0;
                        if (res.response.data.length > 0){
                            max_index = res.response.data.length - 1;
                            var data_tunjangan = res.response.data.map(function (val, i) {
                                color_persentase = '';

                                if (val.kinerja || val.status == 'Hadir' || val.status == 'Alpa'){
                                    kinerja = ( val.kinerja ? (val.kinerja.jenis_kinerja == 'hadir') ? val.status : capitalizeFirstLetter(val.kinerja.jenis_kinerja.replace('_',' ')) : '');
                                    var badge_kinerja = '';
                                    if (val.absen){
                                        masuk = val.absen[0] ? val.absen[0].checktime.split(" ") : null;
                                        pulang = val.absen[1] ? val.absen[1].checktime.split(" ") : null;
                                        if (masuk && pulang) {
                                            if (val.status == 'Alpa') {
                                                badge_kinerja = '<div class="badge badge-gray text-white mr-2">Alpa</div>'
                                            }
                                        }
                                    }
                                    let apel = '<img style="background-color: white;border-radius: 5px;" src="{{url('')}}/assets/images/icons/upacara.svg" class="iconUpacara">'
                                    if (kinerja == 'Hadir' || val.status == 'Hadir'){
                                        if (val.absen){
                                            badge_kinerja = '<div class="badge badge-primary text-white mr-2">' + masuk[1] + ' - ' + pulang[1] + (val.apel ? '&nbsp&nbsp'+apel : '') +' </div>';
                                        }
                                    } else if (kinerja == "Perjalanan Dinas"){
                                        badge_kinerja = '<div class="badge badge-green text-white mr-2">'+kinerja+'</div>'
                                    } else if (kinerja == "Cuti"){
                                        badge_kinerja = '<div class="badge badge-purple text-white mr-2">'+kinerja+'</div>'
                                    } else if (kinerja == "Izin"){
                                        badge_kinerja = '<div style="background-color:#f3bd59!important;" class="badge text-white mr-2">'+kinerja+'</div>'
                                    } else if (kinerja == "Sakit"){
                                        badge_kinerja = '<div class="badge badge-red text-white mr-2">'+kinerja+'</div>'
                                    } else if (kinerja == "Alpa" || val.status == 'Alpa'){
                                        badge_kinerja = '<div class="badge badge-gray text-white mr-2">Alpa</div>'
                                    }
                                }

                                if (val.status == ''){
                                    if (val.absen){
                                        masuk = val.absen[0] ? val.absen[0].checktime.split(" ") : null;
                                        if (masuk) {
                                            badge_kinerja = '<div class="badge badge-primary text-white mr-2">' + masuk[1] + ' - --:--:--</div>'
                                        }
                                    }
                                }
                                var row = '<tr class="data-tunjangan" data-index="' + i + '">\n' +
                                    '                                <td>' + val.hari + ', ' + val.tanggal_string + '</td>\n' +
                                    '                                <td>' +(typeof badge_kinerja != 'undefined' ? badge_kinerja : '') + '</td>\n' +
                                    '                                <td>\n' +
                                    '                                    <span class="' + (val.kinerja ? (val.kinerja.approve ? (val.kinerja.approve == 2 ? 'check-list' : 'not-list') : '') : '') + '"><i class="fas fa-lg ' + (val.kinerja ? (val.kinerja.approve ? (val.kinerja.approve == 2 ? 'fa-check' : 'fa-times') : '' ): '') + '"></i></span>\n' +
                                    '                                </td>\n' +
                                    /*'                                */
                                    '                                <td>\n' +
                                    '                                    <button data-index="'+i+'" class="btn rounded btn-detail '+(val.kinerja ? '' : '')+'" title="Detail">\n' +
                                    '                                        <i class="material-icons">more_horiz</i>\n' +
                                    '                                    </button>\n' +
                                    '                                </td>\n' +
                                    '                            </tr>\n'
                                return row;
                            });
                        } else {
                            data_tunjangan = '<tr><td colspan="100" style="text-align: center">Data Tidak Ditemukan</td></tr>'
                        }
                        $('#data-tunjangan').html(data_tunjangan);

                    }, function () {
                        window['trigger'] = 0;
                        $('#preload').hide();
                    })
            }
            $('#bulan-sebelumnya,#bulan-selanjutnya').on('click', function (e) {
                e.preventDefault();
                d = $("#date-rekap").datepicker("getDate");
                if (d == 'Invalid Date') {
                    d = new Date();
                }
                $("#date-rekap").datepicker("setDate", new Date(d.getFullYear(), d.getMonth() + parseInt($(this).attr('data-value')), d.getDate()));
            })
            window['trigger'] = 0;
            $('#date-rekap').on('changeDate', function (e) {
                e.preventDefault();
                if (window['trigger'] == 0) {
                    window['trigger'] = 1;
                    var value = $(this).val();
                    var date = value.split(' ');
                    var tahun = date[1];
                    var bulan;
                    switch (date[0]) {
                        case 'January' :
                            bulan = 1;
                            break;
                        case 'February' :
                            bulan = 2;
                            break;
                        case 'March' :
                            bulan = 3;
                            break;
                        case 'April' :
                            bulan = 4;
                            break;
                        case 'May' :
                            bulan = 5;
                            break;
                        case 'June' :
                            bulan = 6;
                            break;
                        case 'July' :
                            bulan = 7;
                            break;
                        case 'August' :
                            bulan = 8;
                            break;
                        case 'September' :
                            bulan = 9;
                            break;
                        case 'October' :
                            bulan = 10;
                            break;
                        case 'November' :
                            bulan = 11;
                            break;
                        case 'December' :
                            bulan = 12;
                            break;
                    }
                    loadData(bulan, tahun)
                }
            })

            $(document).ready(function () {
                loadData(null, null)
            })
            window['trigger-modal'] = 0
            $(document).on('click', '.btn-detail', function (e) {
                if (window['trigger-modal'] == 0) {
                    e.preventDefault();
                    window['trigger-modal'] = 1;
                    index = $(this).data('index');
                    setDetailModal(index)
                }
            })
            var setDetailModal = function (i) {
                $('#preload').show();
                index = i;
                data = data_response.data[index];
                kinerja = data.kinerja
                absen = data.absen
                let apel = '<img src="assets/images/icons/upacara.svg" class="iconUpacara">'

                $('.value-kinerja').hide();
                $('.wrap-progress').find('label').html('0%')
                $('.progress-bar').css({'width': '0%'})
                $('#skp-pegawai').html("")
                $('#media-pegawai').html("")
                $('#skp-progress').hide();
                $('#detail-kinerja').html("");
                $('#detail-keterangan-approve').html("");

                $('#current-index').val(index);
                $('.modal-overlay').addClass('show');
                $('#detail-tgl').html(data.tanggal_string2)
                $('#detail-jam-masuk').html(absen ? (typeof absen[0] !== 'undefined' ? absen[0].absen_time : '--:--') : '--:--')
                $('#detail-jam-pulang').html(absen ? (typeof absen[1] !== 'undefined' ? absen[1].absen_time : '--:--') : '--:--')
                if (kinerja) {
                    if (kinerja.jenis_kinerja == 'hadir') {
                        $('#detail-jenis-kinerja').html(data.status).after((data.apel ? apel : ''))
                    } else {
                        $('#detail-jenis-kinerja').html(kinerja ? capitalizeFirstLetter(kinerja.jenis_kinerja.replace('_', ' ')) : '')
                    }
                    $('#detail-approve').addClass((kinerja ? (kinerja.approve ? (kinerja.approve == 2 ? 'check-list' : 'not-list') : '') : '')).removeClass((kinerja ? (kinerja.approve ? (kinerja.approve == 2 ? 'not-list' : 'check-list') : '') : ''));
                    $('#detail-approve').find('i').addClass((kinerja ? (kinerja.approve ? (kinerja.approve == 2 ? 'fa-check' : 'fa-times') : '') : '')).removeClass((kinerja ? (kinerja.approve ? (kinerja.approve == 2 ? 'fa-times' : 'fa-check') : '') : ''));
                    $('#detail-kinerja').html(kinerja ? kinerja.rincian_kinerja : '');
                    $('#detail-keterangan-approve').html(kinerja ? kinerja.keterangan_approve : '');
                    if (i == 0) {
                        $('.control-left').addClass('invisible')
                    } else {
                        $('.control-left').removeClass('invisible')
                    }
                    if (i == max_index) {
                        $('.control-right').addClass('invisible')
                    } else {
                        $('.control-right').removeClass('invisible')
                    }
                }
                if (kinerja) {
                    if (kinerja.approve == 2) {
                        // $('#kinerja_status').addClass('fa-check');
                        $('.value-kinerja').show();
                    }
                    if (kinerja.skp_pegawai.length > 0) {
                        $('#skp-progress').show();
                        var jumlah_skp = kinerja.skp_pegawai.length;
                        var jumlah_selesai = 0;
                        kinerja.skp_pegawai.forEach(function (val) {
                            if (val.status) {
                                jumlah_selesai++;
                            }
                        })
                        var persentase = (jumlah_selesai / jumlah_skp) * 100;
                        $('.wrap-progress').find('label').html(persentase + '%')
                        $('.progress-bar').css({'width': persentase + '%'})
                        var skp = kinerja.skp_pegawai.map(function (val, index) {
                            let checked = val.status ? 'checked ' : '';
                            return '<label class="container-check">\n' +
                                '<p>' + val.skp_task.task + '</p>\n' +
                                '<input type="checkbox" ' + checked + 'readonly onclick="return false">\n' +
                                '<span class="checkmark"></span>\n' +
                                '</label>'
                        })
                        $('#skp-pegawai').html(skp.join(''))
                    }
                    if (kinerja.media.length > 0) {
                        var media = kinerja.media.map(function (val, index) {
                            if (index == 0){
                                res = '<label class="text-secondary" style="font-size: 1.2em">Dokumen SKP</label>'
                            }
                            res += '<div class="downloads mb-3">\n' +
                                '    <img src="{{asset('assets/images/icons/word.svg')}}">\n' +
                                '    <label>'+val.nama_media+'</label>\n' +
                                '    <button onclick="window.open(\''+val.media+'\'); return false">\n' +
                                '        <i class="material-icons mr-1">cloud_download</i>\n' +
                                '        Download\n' +
                                '    </button>\n' +
                                '</div>'
                            return res
                        })
                        $('#media-pegawai').html(media.join(''))
                    }
                }

                window['trigger-modal'] = 0;
                $('#preload').hide();
            }
            $('.control-right,.control-left').on('click',function (e) {
                e.preventDefault();
                index_next = parseInt($(this).data('next'))
                index = parseInt($('#current-index').val())
                new_index = index + index_next;
                if (new_index >= 0 && new_index <= max_index){
                    setDetailModal(new_index)
                }
            })

            function capitalizeFirstLetter(str) {
                return str.replace(/\w\S*/g, function(txt){
                    return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
                });
            }
        </script>
    @endpush
@endsection
