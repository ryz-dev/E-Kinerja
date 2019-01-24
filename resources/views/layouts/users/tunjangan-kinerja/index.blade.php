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
                            <div class="date-group float-left">
                                <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                <input id="date-rekap" class="datepicker" placeholder="Pilih Bulan"/>
                            </div>

                            <div class="float-right">
                                <button class="btn btn-rounded active" id="bulan-sebelumnya" data-value="-1"><i
                                        class="fas fa-angle-left"></i></button>
                                <button class="btn btn-rounded active" id="bulan-selanjutnya" data-value="1"><i
                                        class="fas fa-angle-right"></i></button>
                            </div>

                            <div class="clearfix"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="box-diagram" id="diagram1">
                            <div class="persen">
                                <label class="float-left">ABSEN (
                                    <x id="persentase-absen">0</x>
                                    %)</label>
                                <span class="float-right"><span id="total-absen">0</span>%</span>
                                <div class="clearfix"></div>
                            </div>
                            <canvas id="chart-absen" height="30vh" width="40vw"></canvas>
                            <div class="capaian text-center">
                                Pencapaian <span id="pencapaian-absen">0</span>%
                            </div>
                        </div>
                        <div class="box-diagram" id="diagram2">
                            <div class="persen">
                                <label class="float-left">KINERJA (
                                    <x id="persentase-kinerja">0</x>
                                    %)</label>
                                <span class="float-right"><span id="total-kinerja">0</span>%</span>
                                <div class="clearfix"></div>
                            </div>
                            <canvas id="chart-kinerja" height="30vh" width="40vw"></canvas>
                            <div class="capaian text-center">
                                Pencapaian <span id="pencapaian-kinerja">0</span>%
                            </div>
                        </div>
                        <div class="box-diagram" id="diagram3">
                            <div class="persen">
                                <label class="float-left">ETIKA (
                                    <x id="persentase-etika">0</x>
                                    %)</label>
                                <span class="float-right"><span id="total-etika">0</span>%</span></span>
                                <div class="clearfix"></div>
                            </div>
                            <canvas id="chart-etika" height="30vh" width="40vw"></canvas>
                            <div class="capaian text-center">
                                Pencapaian <span id="pencapaian-etika">0</span>%
                            </div>
                        </div>
                        <div class="box-diagram" id="diagram4">
                            <div class="chart-container p-2">
                                <canvas id="chart-tunjangan" height="50vh" width="50vw"></canvas>
                            </div>
                            <div class="container-desc">
                                <label>TUNJANGAN</label>
                                <br>
                                <span>Rp <span id="total-tunjangan"></span></span>
                                <div class="capaian text-center">
                                    Pencapaian
                                    <x id="total">0</x>
                                    %
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
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
                                {{--<th scope="col">Etika</th>--}}
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
                                <h6 id="detail-nama"></h6>
                                <span id="detail-nip"></span>
                                <span class="badge text-white float-right" id="detail-tgl"></span>
                                <div class="clearfix"></div>
                            </div>
                            <div class="desc-detail">
                                <h4 id="detail-jenis-kinerja"></h4>
                                <small>Jam Masuk - Jam Pulang</small>
                                <br>
                                <label><span id="detail-jam-masuk">--:--</span> - <span id="detail-jam-pulang">--:--</span></label>
                                <hr>
                                <h4>Penilaian Kinerja</h4>
                                <span id="detail-approve" class=" float-right"><i class="fas fa-lg"></i></span>
                                <h6>Rincian Kinerja</h6>
                                <p id="detail-kinerja"></p>
                                <h6>Keterangan Penilaian</h6>
                                <p id="detail-keterangan-approve">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Accusamus nostrum
                                    possimus asperiores aliquid eaque iusto aut aspernatur earum nihil magnam
                                    saepe odit officiis, ipsam excepturi maiores? Possimus odit alias fugiat
                                    excepturi dolorem doloripsa delectus tempora? Magni, atque totam dicta
                                    accusantium, velit itaque dolores magnam nihil repellendus!</p>
                                <hr>
                                <div class="wrap-modal-value table-responsive">
                                    <h4 class="float-left">Penilaian Etika</h4>
                                    <span class="badge text-white float-right" id="tanggal_etika">-</span>
                                    <table>
                                        <tbody><tr>
                                            <td>Upacara dan Apel 30(%)</td>
                                            <td id="upacara">0%</td>
                                            <td colspan="3" rowspan="3">
                                                <div class="value-percent">
                                                    <div class="values">
                                                        <h2 id="detail-etika-persentase">0%</h2>
                                                        <div class="clearfix"></div>
                                                    </div>
                                                    <div class="ket" id="keterangan_etika">-</div>
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Perilaku Kerja 30(%)</td>
                                            <td id="prilaku">0%</td>
                                        </tr>

                                        <tr>
                                            <td>Kegiatan Kebersamaan 40(%)</td>
                                            <td id="kegiatan_kebersamaan">0%</td>
                                        </tr>

                                        </tbody></table>
                                    <h6>Keterangan Penilaian</h6>
                                    <p id="detail-etika-keterangan"></p>
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
            var ctx3 = document.getElementById("chart-etika").getContext("2d");
            var ctx4 = document.getElementById("chart-tunjangan").getContext("2d");
            var data_response;
            var max_index = 0;
            var chart = chart2 = chart3 = chart4 = null;
            var loadData = function (bulan, tahun) {
                $('#preload').show();
                $.get('{{route('api.web.tunjangan-kinerja.get')}}' + (bulan ? '/' + bulan : '') + (tahun ? '/' + tahun : ''))
                    .then(function (res) {
                        $('#persentase-absen').html(res.response.persentase.absen);
                        $('#persentase-kinerja').html(res.response.persentase.kinerja);
                        $('#persentase-etika').html(res.response.persentase.etika);

                        $('#pencapaian-absen').html(res.response.pencapaian.absen);
                        $('#pencapaian-kinerja').html(res.response.pencapaian.kinerja);
                        $('#pencapaian-etika').html(res.response.pencapaian.etika);

                        $('#total-absen').html(res.response.total.absen);
                        $('#total-kinerja').html(res.response.total.kinerja);
                        $('#total-etika').html(res.response.total.etika);

                        $('#total').html(res.response.total.total)
                        $('#total-tunjangan').html(res.response.total_tunjangan_diterima)
                        data_response = res.response
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

                        var dataEtika = {
                            labels: ["Etika",""],
                            datasets: [
                                {
                                    label: "My First dataset",
                                    backgroundColor: ["#00B894", "#d8dadc"],
                                    borderColor: ["#00B894", "#d8dadc"],
                                    data: [parseInt(res.response.pencapaian.etika), 100 - parseInt(res.response.pencapaian.etika)]
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

                        chart3 = new Chart(ctx3, {
                            type: "pie",
                            data: dataEtika,
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
                                if (val.etika){
                                    if (val.etika.persentase == 100) {
                                        color_persentase = 'badge badge-blue'
                                    } else if (val.etika.persentase >= 75) {
                                        color_persentase = 'badge badge-green'
                                    } else if (val.etika.persentase >= 45) {
                                        color_persentase = 'badge badge-orange'
                                    } else if (val.etika.persentase > 0) {
                                        color_persentase = 'badge badge-red'
                                    } else if (val.etika.persentase === 0) {
                                        color_persentase = 'badge badge-red'
                                    } else if (val.etika.persentase == '') {
                                        color_persentase = ''
                                    }
                                }

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
                                    if (kinerja == 'Hadir' || val.status == 'Hadir'){
                                        if (val.absen){
                                            badge_kinerja = '<div class="badge badge-primary text-white mr-2">' + masuk[1] + ' - ' + pulang[1] + '</div>';
                                        }
                                    } else if (kinerja == "Perjalanan Dinas"){
                                        badge_kinerja = '<div class="badge badge-green text-white mr-2">'+kinerja+'</div>'
                                    } else if (kinerja == "Cuti"){
                                        badge_kinerja = '<div class="badge badge-purple text-white mr-2">'+kinerja+'</div>'
                                    } else if (kinerja == "Alpa"){
                                        badge_kinerja = '<div class="badge badge-gray text-white mr-2">'+kinerja+'</div>'
                                    } else if (kinerja == "Izin"){
                                        badge_kinerja = '<div style="background-color:#f3bd59!important;" class="badge text-white mr-2">'+kinerja+'</div>'
                                    } else if (kinerja == "Sakit"){
                                        badge_kinerja = '<div class="badge badge-red text-white mr-2">'+kinerja+'</div>'
                                    }
                                }

                                if (val.status == ''){
                                    if (val.absen){
                                        masuk = val.absen[0] ? val.absen[0].checktime.split(" ") : null;
                                        if (masuk) {
                                            badge_kinerja = '<div class="badge badge-primary text-white mr-2">' + masuk[1] + '- --:--:--</div>'
                                        }
                                    }
                                }
                                var row = '<tr class="data-tunjangan" data-index="' + i + '">\n' +
                                    '                                <td>' + val.hari + ', ' + val.tanggal_string + '</td>\n' +
                                    '                                <td>' +(typeof badge_kinerja != 'undefined' ? badge_kinerja : '')+ '</td>\n' +
                                    '                                <td>\n' +
                                    '                                    <span class="' + (val.kinerja ? (val.kinerja.approve ? (val.kinerja.approve == 2 ? 'check-list' : 'not-list') : '') : '') + '"><i class="fas fa-lg ' + (val.kinerja ? (val.kinerja.approve ? (val.kinerja.approve == 2 ? 'fa-check' : 'fa-times') : '' ): '') + '"></i></span>\n' +
                                    '                                </td>\n' +
                                    /*'                                <td>\n' +
                                    '                                    <div class="' + color_persentase + ' text-white mr-2">' + (val.etika ? val.etika.persentase+'%' : '') + ' </div>\n' +
                                    '                                </td>\n' +*/
                                    '                                <td>\n' +
                                    '                                    <button data-index="'+i+'" class="btn rounded btn-detail '+(val.kinerja || val.etika ? '' : 'invisible')+'" title="Detail">\n' +
                                    '                                        <i class="fas fa-search-plus"></i>\n' +
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
                etika = data.etika
                $('#current-index').val(index);
                $('.modal-overlay').addClass('show');
                $('#detail-nama').html(data_response.pegawai.nama)
                $('#detail-nip').html(data_response.pegawai.nip)
                $('#detail-tgl').html(data.tanggal_string2)
                if (kinerja.jenis_kinerja == 'hadir') {

                    $('#detail-jenis-kinerja').html(data.status)
                } else {
                    $('#detail-jenis-kinerja').html(kinerja ? capitalizeFirstLetter(kinerja.jenis_kinerja.replace('_', ' ')) : '')
                }
                $('#detail-jam-masuk').html(absen ? (typeof absen[0] !== 'undefined' ? absen[0].absen_time : '--:--') : '--:--')
                $('#detail-jam-pulang').html(absen ? (typeof absen[1] !== 'undefined' ? absen[1].absen_time : '--:--') : '--:--')
                $('#detail-approve').addClass((kinerja ? (kinerja.approve ? (kinerja.approve == 2 ? 'check-list' : 'not-list') : '') : '')).removeClass((kinerja ? (kinerja.approve ? (kinerja.approve == 2 ? 'not-list' : 'check-list') : '') : ''));
                $('#detail-approve').find('i').addClass((kinerja ? (kinerja.approve ? (kinerja.approve == 2 ? 'fa-check' : 'fa-times') : '') : '')).removeClass((kinerja ? (kinerja.approve ? (kinerja.approve == 2 ? 'fa-times' : 'fa-check') : '') : ''));
                $('#detail-kinerja').html(kinerja ? kinerja.rincian_kinerja : '');
                $('#detail-keterangan-approve').html(kinerja ? kinerja.keterangan_approve : '');
                $('#detail-etika-persentase').html(etika ? etika.persentase+'%' : '');
                $('#detail-etika-keterangan').html(etika ? etika.keterangan : '');
                $('#upacara').html(etika ? etika.mengikuti_upacara + '%' : "-");
                $('#kegiatan_kebersamaan').html(etika ? etika.kegiatan_kebersamaan + '%' : "-");
                $('#prilaku').html(etika ? etika.perilaku_kerja + '%' : "-");
                $('#tanggal_etika').html(data.tanggal_etika);
                ket = "-";
                if (etika){
                    if (etika.persentase < 25){
                        ket = 'Buruk'
                    } else if (etika.persentase < 50){
                        ket = 'Cukup Baik'
                    } else if (etika.persentase < 75){
                        ket = 'Baik'
                    } else if (etika.persentase <= 100){
                        ket = 'Sangat Baik'
                    }
                }
                $('#keterangan_etika').html(ket);
                if (i == 0){
                    $('.control-left').addClass('invisible')
                } else {
                    $('.control-left').removeClass('invisible')
                }
                if (i == max_index){
                    $('.control-right').addClass('invisible')
                } else {
                    $('.control-right').removeClass('invisible')
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
