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
                        <h2 class="mb-2">Detail Jabatan</h2>
                        <div class="form-group">
                            <label for="jabatan">Jabatan</label>
                            <input id="jabatan" type="text" class="form-control" name="jabatan" placeholder="Jabatan" value="{{$jabatan->jabatan}}" readonly="">
                        </div>
                        <div class="form-group">
                            <label for="golongan">Kelas Jabatan</label>
                            <input id="golongan" type="text" class="form-control" name="golongan" placeholder="Kelas Jabatan" value="{{$jabatan->golongan->golongan}} (Rp.{{$jabatan->golongan->tunjangan_rp}})" readonly="">
                        </div>
                        <div class="form-group">
                            <label for="atasan">Atasan</label>
                            <input id="atasan" type="text" class="form-control" name="atasan" placeholder="Atasan" value="{{$jabatan->atasan ? $jabatan->atasan->jabatan : ''}}" readonly="">
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan Jabatan</label>
                            <textarea name="keterangan" id="keterangan" rows="3" class="form-control">{{$jabatan->keterangan}}</textarea>
                        </div>
                        {{--<div class="form-group">
                            <label for="atasan">Urutan</label>
                            <input id="atasan" type="text" class="form-control" name="urutan" placeholder="Urutan" value="{{$jabatan->urutan ? $jabatan->urutan : ''}}" readonly="">
                        </div>--}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('script')
    @endpush
@endsection
