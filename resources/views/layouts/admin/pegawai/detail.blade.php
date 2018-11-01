@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
        <div class="main-content tab-content">
            <div class="container-fluid">
                <div class="row">
                        <div class="col-md-6">
                            <h2 class="mb-2">Detail Pegawai</h2>
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input id="nip" type="text" class="form-control" placeholder="NIP" readonly="" value="{{$pegawai->nip}}">
                            </div>
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input id="nama" type="text" class="form-control" placeholder="Nama" readonly="" value="{{$pegawai->nama}}">
                            </div>
                            <div class="form-group">
                                <label for="jabatan">Jabatan</label>
                                <input id="jabatan" type="text" class="form-control" placeholder="Jabatan" readonly="" value="{{$pegawai->jabatan->jabatan}}">
                            </div>
                            <div class="form-group">
                                <label for="agama">Agama</label>
                                <input id="agama" type="text" class="form-control" placeholder="Agama" readonly="" value="{{$pegawai->agama->agama}}">
                            </div>
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input id="tanggal_lahir" type="text" class="form-control" placeholder="Tanggal Lahir" readonly="" value="{{$pegawai->tanggal_lahir}}">
                            </div>
                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input id="tempat_lahir" type="text" class="form-control" placeholder="Tempat Lahir" readonly="" value="{{$pegawai->tempat_lahir}}">
                            </div>
                            <div class="form-group">
                                <label for="jns_kel">Jenis Kelamin</label>
                                <input id="jns_kel" type="text" class="form-control" placeholder="Jenis Kelamin" readonly="" value="{{$pegawai->jns_kel}}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <img src="{{$pegawai->foto ? url('storage/'.$pegawai->foto) : url('assets/images/img-user.png')}}" class="img-profile" alt="User Image">
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    @push('script')
    @endpush
@endsection
