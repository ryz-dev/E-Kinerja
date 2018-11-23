@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
        <div class="main-content tab-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="mb-2">Detail Kelas Jabatan</h2>
                        <div class="form-group">
                            <label for="eselon">Kelas Jabatan</label>
                            <input id="eselon" type="text" class="form-control" name="eselon" placeholder="Kelas Jabatan" value="{{$eselon->eselon}}" required>
                        </div>
                        <div class="form-group">
                            <label for="eselon">Harga Jabatan</label>
                            <input type="number" class="form-control" name="tunjangan" placeholder="tunjangan" value="{{$eselon->tunjangan}}" required>
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea class="form-control" name="keterangan" id="keterangan">{{$eselon->keterangan}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('script')
    @endpush
@endsection
