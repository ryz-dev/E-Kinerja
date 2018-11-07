@extends('layouts.users.partial.main')
@section('content')

<div class="main">
        <div class="nav-top-container">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="group-search">
                            <span><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Cari Nama / NIP Pegawai">
                        </div>
                    </div>

                    <div class="offset-md-1 col-md-4 float-left">
                        <div class="input-group mb-3 skpd-option">
                            <div class="input-group-prepend">
                                <label class="input-group-text">SKPD</label>
                            </div>
                            <select id="skpd" class="custom-select select-custome">
                                <option value="0">- ALL -</option>
                                @foreach ($skpd as $item)
                                <option value="{{ $item->id }}">{{ $item->nama_skpd }}</option>    
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        @include('layouts.users.partial.part.logout')

    </div>
    <div class="main-content">
        <div class="loading">
            <img src="{{ asset('assets/images/loading.gif') }}" alt="loading">
        </div>
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
                                    <button data-date ='' id="prevdate" class="btn btn-rounded date-nav"><i class="fas fa-angle-left "></i></button>
                                    <button data-date ='' id="nextdate" class="btn btn-rounded  date-nav active"><i class="fas fa-angle-right"></i></button>
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
                                        <th scope="col"></th>
                                        <th scope="col">NIP Pegawai</th>
                                        <th scope="col">Nama Pegawai</th>
                                        <th scope="col">Jam Masuk - Jam Pulang</th>
                                    </tr>
                                </thead>
                                <tbody class="list_pegawai">
                                </tbody>
                            </table>
                            <nav aria-label="...">
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
                                <label class="float-right count">21</label>
                            </li>
                            <li>
                                <span class="dinas">
                                    <img src="assets/images/icons/perjalanan_dinas.svg">
                                </span>
                                <label>Perj. Dinas</label>
                                <label class="float-right count">21</label>
                            </li>
                            <li>
                                <span class="cuti">
                                    <img src="assets/images/icons/cuti.svg">
                                </span>
                                <label>Cuti</label>
                                <label class="float-right count">3</label>
                            </li>
                            <li>
                                <span class="izin">
                                    <img src="assets/images/icons/izin.svg">
                                </span>
                                <label>Izin</label>
                                <label class="float-right count">4</label>
                            </li>
                            <li>
                                <span class="sakit">
                                    <img src="assets/images/icons/sakit.svg">
                                </span>
                                <label>Sakit</label>
                                <label class="float-right count">2</label>
                            </li>
                            <li>
                                <span class="alpha">
                                    <img src="assets/images/icons/alpha.svg">
                                </span>
                                <label>Alpha</label>
                                <label class="float-right count">0</label>
                            </li>
                        </ul>
                    </div>

                    <div class="clock-side">
                        <span>17:10</span>
                    </div>
                    <div class="day-side">
                        <small>Selasa, 23 September 2018</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
    <script>
        $(document).ready(function(){
            getPage('{{date('Y-m-d')}}',0);
        });
        
        $(document).on('click','.date-nav', function(){
            var date = $(this).attr('data-date');
            var skpd = $('#skpd').val();
            getPage(date,skpd);
        });

        $(document).on('click','#skpd', function(){
            var date = $('.datepicker').val();
            var skpd = $(this).val();
            getPage(date,skpd);
        });

        $(document).on('change','#tanggal-absen', function(){
            var date = $(this).val();
            var skpd = $('#skpd').val();
            getPage(date,skpd);
        });


        var parseAbsen = function(data){
            if (data.length > 0) {
                var absenin;
                var absenout;
                var res = data.map(function (val){
                    
                    if (val.checktype == 'i') {
                        absenin = val.absen_time?val.absen_time:'data tidak ada';
                    }else if(val.checktype == 'o'){
                        absenout =val.absen_time?val.absen_time:'data tidak ada';
                    }
                }); 
                return absenin+' - '+absenout;
                
            } else {
                return 'data tidak ada';
            }
        }

        var getPage = function (date, skpd) {
            $('#pagination').twbsPagination('destroy');
            $.get('{{route('api.web.monitoring.absen.page')}}?d='+date+'&skpd='+skpd)
                .then(function (res) {
                    if (res.page == 0){
                        $('.list_pegawai').html('<tr style="text-align: center"><td colspan="100">Kata Kunci "<i>'+date+'</i>" Tidak Ditemukan</td></tr>')
                    }
                    $('#pagination').twbsPagination({
                        totalPages: res.page,
                        visiblePages: 5,
                        onPageClick: function (event, page) {
                            getData(page,date,skpd);
                        }
                    });
                })
        };
        var getData = function (page, date, skpd) {
            var selector = $('.list_pegawai');
            $('.loading').show();
            $.ajax({
                url: "{{ route('api.web.monitoring.absen') }}?page="+page+'&d='+date+'&skpd='+skpd,
                data: '',
                success: function(res) {
                    $('.datepicker').val(res.response.today);
                    $('#prevdate').attr('data-date',res.response.dayBefore); 
                    $('#nextdate').attr('data-date',res.response.dayAfter); 
                    $('#text-date').text(res.response.dateString); 
                    if (res.response.pegawai.data.length > 0) {
                        var data = res.response.pegawai.data.map(function (val) { 
                            var row = '';
                            var foto = val.foto ? "{{url('')}}/storage/" + val.foto : "{{url('assets/images/img-user.png')}}"
                            row += "<tr>";
                            row += "<td><div class='img-user' id='user1' style='background-image: url(" + foto + ");'></div></td>";
                            row += "<td><a href=''>" + val.nip + "</a></td>";
                            row += "<td>" + val.nama + "</td>";
                            row += "<td>"+parseAbsen(val.checkinout)+"</td>";
                            row += "</tr>";
                            return row;
                        })
                        selector.html(data.join(''));
                    } else {
                        selector.html('<tr style="text-align: center"><td colspan="100">Kata Kunci "<i>'+date+'</i>" Tidak Ditemukan</td></tr>')
                    }
                },
                complete: function () {
                    $('.loading').hide();
                }
            });
        }
    </script>
@endpush
@endsection