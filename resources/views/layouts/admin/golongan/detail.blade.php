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
                        <h2 class="mb-2">Detail Kelas Jabatan</h2>
                        <div class="form-group">
                            <label for="golongan">Kelas Jabatan</label>
                            <input id="golongan" type="text" class="form-control" name="golongan" placeholder="Kelas Jabatan" value="{{$golongan->golongan}}" required>
                        </div>
                        <div class="form-group">
                            <label for="golongan">Kriteria</label>
                            <input id="golongan" type="text" class="form-control" name="kriteria" placeholder="Kriteria" value="{{$golongan->kriteria}}" required>
                        </div>
                        <div class="form-group">
                            <label for="golongan">Tunjangan</label>
                            <input type="number" class="form-control" name="tunjangan" placeholder="tunjangan" value="{{$golongan->tunjangan}}" required>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan">{{$golongan->keterangan}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('script')
    @endpush
@endsection
