@extends('layouts.users.partial.main')
@section('content')
<div class="main">
    @include('layouts.users.partial.part.logout')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-12">
                        <div class="title-nav float-left">
                            <h4 class="mr-3 float-left">Monitoring Absen</h4>
                            <span class="badge text-white">{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</span>
                        </div>
                        <div class="btn-control float-right mt-1">
                            <div class="date-group float-left">
                                <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                <input id="tanggal" class="datepicker" placeholder="Pilih Tanggal" value="{{ \Carbon\Carbon::parse($date)->format('m/d/Y') }}" />
                            </div>
    
                            <div class="float-right">
                                <a href="{{ route('monitoring.absen.index').'?d='.\Carbon\Carbon::parse($date)->addDays(-1)->toDateString() }}" class="btn btn-rounded"><i class="fas fa-angle-left"></i></a>
                                {{-- <button class="btn btn-rounded"><i class="fas fa-angle-left"></i></button> --}}
                                {{-- <button class="btn btn-rounded active"><i class="fas fa-angle-right"></i></button> --}}
                                <a href="{{ route('monitoring.absen.index').'?d='.\Carbon\Carbon::parse($date)->addDays(1)->toDateString() }}" class="btn btn-rounded active"><i class="fas fa-angle-right"></i></a>
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
                            <tbody>
                                @foreach ($pegawai as $item)
                                    <tr>
                                        <td>
                                            <div class="img-user" id="user1" style="background-image: url('assets/images/img-user.png');">
                                            </div>
                                        </td>
                                        <td>{{ $item->nip }}</td>
                                        <td>{{ $item->nama }}</td>
                                        <td>{{ date('H:i', strtotime($item->checkinout->min('checktime'))) }}
                                             <span>-</span> 
                                            {{ date('H:i', strtotime($item->checkinout->max('checktime'))) }}
                                        </td>
                                    </tr>    
                                @endforeach
                            </tbody>
                        </table>
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
@push('script')
    <script>
        $(document).ready(function(){
            $(".datepicker").on('change', function(){
                var tanggalAbsen = $(this).val();
                window.location.replace("{{ route('monitoring.absen.index') }}?d="+tanggalAbsen);
            });
        });
    </script>
@endpush
@endsection