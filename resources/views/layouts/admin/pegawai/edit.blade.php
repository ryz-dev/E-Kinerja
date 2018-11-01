@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
        <div class="main-content tab-content">
            <div class="container-fluid">
                <form id="form-update-pegawai" action="{{$pegawai->update_uri}}" class="form" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="mb-2">Edit Pegawai</h2>
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input id="nip" type="text" class="form-control" name="nip" placeholder="NIP" readonly="" value="{{$pegawai->nip}}">
                            </div>
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input id="nama" type="text" class="form-control" name="nama" placeholder="Nama" value="{{$pegawai->nama}}" required>
                            </div>
                            <div class="form-group">
                                <label for="jabatan">Jabatan</label>
                                <select class="form-control" name="id_jabatan" id="jabatan" required>
                                    <option value="">Pilih Jabatan</option>
                                    @foreach($data_option->jabatan AS $jb)
                                        <option value="{{$jb->id}}" {{$jb->id == $pegawai->id_jabatan ? 'selected' : ''}}>{{$jb->jabatan}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="agama">Agama</label>
                                <select class="form-control" name="id_agama" id="agama" required>
                                    <option value="">Pilih Agama</option>
                                    @foreach($data_option->agama AS $ag)
                                        <option value="{{$ag->id}}" {{$ag->id == $pegawai->id_agama ? 'selected' : ''}}>{{$ag->agama}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input id="tanggal_lahir" name="tanggal_lahir" type="date" class="form-control" placeholder="Tanggal Lahir" value="{{$pegawai->tanggal_lahir}}" required>
                            </div>
                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input id="tempat_lahir" name="tempat_lahir" type="text" class="form-control" placeholder="Tempat Lahir" value="{{$pegawai->tempat_lahir}}" required>
                            </div>
                            <div class="form-group">
                                <label for="jns_kel">Jenis Kelamin</label>
                                <select class="form-control" name="jns_kel" id="jns_kel" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="laki-laki" {{$pegawai->jns_kel == 'laki-laki' ? 'selected' : ''}}>Laki-laki</option>
                                    <option value="perempuan" {{$pegawai->jns_kel == 'perempuan' ? 'selected' : ''}}>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <img src="{{$pegawai->foto ? url('storage/'.$pegawai->foto) : url('assets/images/img-user.png')}}" class="img-profile" alt="User Image">
                                <input type="file" name="foto" class="form-control">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('script')
        <script>
            $('#form-update-pegawai').on('submit',function (e) {
                e.preventDefault();
                var action = this.action;
                var formData = new FormData($(this)[0]);
                swal({
                    title: 'Ingin Menyimpan Data?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Iya, simpan data!',
                    cancelButtonText: 'Batalakan'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: action,
                            type: "POST",
                            data: formData,
                            success: function (res) {
                                swal(
                                    'Berhasil Menyimpan Data!',
                                    '',
                                    'success'
                                )
                                setTimeout(function () {
                                    location.href = res.detail_uri
                                }, 3000);
                            },
                            error: function () {
                                swal(
                                    'Gagal Menyimpan Data!',
                                    '',
                                    'error'
                                )
                            },
                            cache: false,
                            contentType: false,
                            processData: false
                        });
                    }
                })
            })
        </script>
    @endpush
@endsection
