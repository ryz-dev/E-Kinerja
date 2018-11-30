@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
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
                            <label for="eselon">Kelas Jabatan</label>
                            <input id="eselon" type="text" class="form-control" name="eselon" placeholder="Kelas Jabatan" value="{{$jabatan->eselon->eselon}} (Rp.{{$jabatan->eselon->tunjangan_rp}})" readonly="">
                        </div>
                        <div class="form-group">
                            <label for="atasan">Atasan</label>
                            <input id="atasan" type="text" class="form-control" name="atasan" placeholder="Atasan" value="{{$jabatan->atasan ? $jabatan->atasan->jabatan : ''}}" readonly="">
                        </div>
                        <div class="form-group">
                            <label for="keterangan">Keterangan Jabatan</label>
                            <textarea name="keterangan" id="keterangan" rows="3" class="form-control">{{$jabatan->keterangan}}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('script')
    @endpush
@endsection
