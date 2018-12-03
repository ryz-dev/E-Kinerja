@extends('layouts.users.partial.main')
@section('class','monitoring-absen')
@section('content')

<div class="main">
        <div class="nav-top-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="group-search">
                            <span><i class="fas fa-search"></i></span>
                            <input type="text" id="search" class="form-control" placeholder="Cari Nama / NIP Pegawai">
                        </div>
                    </div>

                    <div class="offset-md-1 col-md-4 float-left">
                        <div class="input-group mb-3 skpd-option">
                            <div class="input-group-prepend">
                                <label class="input-group-text">SKPD</label>
                            </div>
                            <select id="skpd" class="custom-select select-custome">
                                @foreach ($skpd as $key => $item)
                                <option value="{{ $key }}">{{ $item }}</option>    
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        @include('layouts.users.partial.part.logout')

    </div>
    <div class="main-content">
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title-nav float-left">
                                <h4 class="mr-3 float-left">Monitoring Absen</h4>
                                <span id="text-date" class="badge text-white">{{ \Carbon\Carbon::now()->format('d M Y') }}</span>
                            </div>
                            <div class="btn-control float-right mt-1">
                                <div class="date-group float-left">
                                    <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                    <input class="datepicker" id="tanggal-absen" placeholder="Pilih Tanggal" value="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" />
                                </div>

                                <div class="float-right">
                                    <button data-date ='' id="prevdate" class="btn btn-rounded date-nav active"><i class="fas fa-angle-left "></i></button>
                                    <button data-date ='' id="nextdate" class="btn btn-rounded  date-nav active"><i class="fas fa-angle-right"></i></button>
                                </div>

                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-responsive table-pegawai table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col"></th>
                                        <th scope="col">NIP Pegawai</th>
                                        <th scope="col">Nama Pegawai</th>
                                        <th scope="col">Jam Masuk - Jam Pulang</th>
                                    </tr>
                                </thead>
                                <tbody class="list_pegawai">
                                </tbody>
                            </table>
                            <nav style="margin-bottom:1em" aria-label="...">
                                <ul class="pagination pagination-custome" id="pagination"></ul>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="description">
                        <h6 class="font-weight-bold">Keterangan</h6>
                        <ul>
                            <li>
                                <span class="hadir">
                                    <img src="assets/images/icons/hadir.svg">
                                </span>
                                <label>Hadir</label>
                                <label class="float-right count count-hadir">21</label>
                            </li>
                            <li>
                                <span class="dinas">
                                    <img src="assets/images/icons/perjalanan_dinas.svg">
                                </span>
                                <label>Perj. Dinas</label>
                                <label class="float-right count count-perjalanan-dinas">21</label>
                            </li>
                            <li>
                                <span class="cuti">
                                    <img src="assets/images/icons/cuti.svg">
                                </span>
                                <label>Cuti</label>
                                <label class="float-right count count-cuti">3</label>
                            </li>
                            <li>
                                <span class="izin">
                                    <img src="assets/images/icons/izin.svg">
                                </span>
                                <label>Izin</label>
                                <label class="float-right count count-izin">4</label>
                            </li>
                            <li>
                                <span class="sakit">
                                    <img src="assets/images/icons/sakit.svg">
                                </span>
                                <label>Sakit</label>
                                <label class="float-right count count-sakit">2</label>
                            </li>
                            <li>
                                <span class="alpha">
                                    <img src="assets/images/icons/alpha.svg">
                                </span>
                                <label>Alpa</label>
                                <label class="float-right count count-alpha">0</label>
                            </li>
                        </ul>
                    </div>

                    <div class="clock-side">
                        <span class="clock">--:--</span>
                    </div>
                    <div class="day-side">
                        <small>{{ucfirst(\App\Models\MasterData\Hari::find(date('N'))->nama_hari)}}, {{date('d')}} {{ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan)}} {{date('Y')}}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        // $(document).ready(function(){
        //     getPage('{{date('Y-m-d')}}',0);
        // });
        
        $(document).on('click','.date-nav', function(){
            var date = $(this).attr('data-date');
            var skpd = $('#skpd').val();
            var search = $("#search").val();
            getPage(date,skpd,search);
        });

        $(document).on('change','#skpd', function(){
            var date = $('.datepicker').val();
            var skpd = $(this).val();
            var search = $("#search").val();
            getPage(date,skpd,search);
        });

        $(document).on('changeDate','#tanggal-absen', function(){
            var date = $(this).val();
            var skpd = $('#skpd').val();
            var search = $("#search").val();
            getPage(date,skpd,search);    
        });

        $('#search').on('keyup', function () {
            var date = $('#tanggal-absen').val();
            var skpd = $('#skpd').val();
            key = $(this).val();
            if (key.length > 3) {
                getPage(date,skpd,key);
            }
            else if (key.length == 0){
                getPage(date,skpd);
            }
        })

        var parseAbsen = function(data){
            if (data.length > 0) {
                var absenin = 'data tidak ada';
                var absenout = 'data tidak ada';
                var res = data.map(function (val){
                    if (val.checktype == '0') {
                        absenin = val.absen_time?val.absen_time:'data tidak ada';
                    }else if(val.checktype == '1'){
                        absenout =val.absen_time?val.absen_time:'data tidak ada';
                    }
                    
                }); 
                return absenin+'<span> - </span>'+absenout;
                
            } else {
                return 'data tidak ada';
            }
        }

        var parseKinerja = function(data){

            if (data.kinerja.length > 0) {
                switch (data.kinerja[0].jenis_kinerja) {
                    case 'hadir':
                        return parseAbsen(data.checkinout)
                        break;
                    case 'sakit':
                        return '<span class="badge badge-table badge-red">Sakit</span>'
                        break;
                    case 'izin':
                        return '<span class="badge badge-table badge-orange">izin</span>'
                        break;
                    case 'perjalanan_dinas':
                        return  '<span class="badge badge-table badge-green">Perjalanan Dinas</span>'
                        break;
                    case 'cuti':
                        return  '<span class="badge badge-table badge-purple">Cuti</span>'
                        break;
                }
            }
            return  '<span class="badge badge-table badge-gray">Alpa</span>';
        }


        var getPage = function (date, skpd, search) {
            $('#pagination').hide();
            $('#pagination').twbsPagination('destroy');
            $.get('{{route('api.web.monitoring.absen.page')}}?d='+date+'&skpd='+skpd+(search?('&search='+search):''))
                .then(function (res) {
                    if (res.page == 0){
                        $('.list_pegawai').html('<tr style="text-align: center"><td colspan="100">Data Tidak Ditemukan</td></tr>')
                    }
                    if(res.page == 1){
                        $('#pagination').hide();
                    }
                    $('#pagination').twbsPagination({
                        totalPages: res.page,
                        visiblePages: 5,
                        onPageClick: function (event, page) {
                            getData(page,date,skpd,search);
                        }
                    });
                })
        };
        var getData = function (page, date, skpd, search) {
            var selector = $('.list_pegawai');
            $('#preload').show();
            $.ajax({
                url: "{{ route('api.web.monitoring.absen') }}?page="+page+'&d='+date+'&skpd='+skpd+(search?('&search='+search):''),
                data: '',
                success: function(res) {
                    $('.datepicker').val(res.response.today);
                    $('#prevdate').attr('data-date',res.response.dayBefore); 
                    $('#nextdate').attr('data-date',res.response.dayAfter); 
                    $('#text-date').text(res.response.dateString);
                    $('.count-hadir').text(res.response.summary.hadir);
                    $('.count-perjalanan-dinas').text(res.response.summary.perjalanan_dinas);
                    $('.count-cuti').text(res.response.summary.cuti);
                    $('.count-izin').text(res.response.summary.izin);
                    $('.count-sakit').text(res.response.summary.sakit);
                    $('.count-alpha').text(res.response.summary.alpha);
                    if (res.response.pegawai.data.length > 0) {
                        var data = res.response.pegawai.data.map(function (val) { 
                            var row = '';
                            var foto = val.foto ? "{{url('')}}/storage/" + val.foto : "{{url('assets/images/img-user.png')}}"
                            row += "<tr data-nip='"+val.nip+"' >";
                            row += "<td><div class='img-user' id='user1' style='background-image: url(" + foto + ");'></div></td>";
                            row += "<td><a href=''>" + val.nip + "</a></td>";
                            row += "<td>" + val.nama + "</td>";
                            row += "<td>"+parseKinerja(val)+"</td>";
                            row += "</tr>";
                            return row;
                        })
                        selector.html(data.join(''));
                    } else {
                        selector.html('<tr style="text-align: center"><td colspan="100">Data Tidak Ditemukan</td></tr>')
                    }
                },
                complete: function () {
                    $('#pagination').show();
                    $('#preload').hide();
                }
            });
            function getTime() {
                var date =  new Date();
                var jam = (date.getHours() < 10 ? '0'+date.getHours() : date.getHours());
                var menit = (date.getMinutes() < 10 ? '0'+date.getMinutes() : date.getMinutes())
                var separator = (date.getSeconds()%2 ? '<span style="opacity: 0.5">:</span>' : '<span>:</span>')
                $('.clock').html(jam + separator + menit)
            }
            setInterval(function () {
                getTime()
            },1000)
        }
    </script>
@endpush
@endsection
