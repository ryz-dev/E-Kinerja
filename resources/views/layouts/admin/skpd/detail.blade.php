@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
        <div class="nav-top-container">
            @include('layouts.admin.partial.part.logout')
        </div>
        <div class="main-content tab-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="mb-2">Detail SKPD</h2>
                        <div class="form-group">
                            <label for="nama_skpd">Nama SKPD</label>
                            <input id="nama_skpd" type="text" name="nama_skpd" class="form-control" placeholder="Nama SKPD" value="{{$skpd->nama_skpd}}" required>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea id="keterangan" name="keterangan" class="form-control">{{$skpd->keterangan}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('script')
    @endpush
@endsection
