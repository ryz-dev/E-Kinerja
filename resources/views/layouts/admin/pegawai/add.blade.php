@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
        <div class="main-content tab-content">
            <div class="container-fluid">
                <form id="form-store-pegawai" action="{{route('api.web.pegawai.store')}}" class="form" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="mb-2">Tambah Pegawai</h2>
                            <div class="form-group">
                                <label for="nip">NIP</label>
                                <input id="nip" type="text" name="nip" class="form-control" placeholder="NIP" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="nama">Nama</label>
                                <input id="nama" type="text" name="nama" class="form-control" placeholder="Nama" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="jabatan">Jabatan</label>
                                <select class="form-control" name="id_jabatan" id="jabatan" required>
                                    <option value="">Pilih Jabatan</option>
                                    @foreach($data_option->jabatan AS $jb)
                                        <option value="{{$jb->id}}">{{$jb->jabatan}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="agama">Agama</label>
                                <select class="form-control" name="id_agama" id="agama" required>
                                    <option value="">Pilih Agama</option>
                                    @foreach($data_option->agama AS $ag)
                                        <option value="{{$ag->id}}" >{{$ag->agama}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input id="tanggal_lahir" name="tanggal_lahir" type="date" class="form-control" placeholder="Tanggal Lahir" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input id="tempat_lahir" name="tempat_lahir" type="text" class="form-control" placeholder="Tempat Lahir" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="jns_kel">Jenis Kelamin</label>
                                <select class="form-control" name="jns_kel" id="jns_kel" required>
                                    <option value="">Pilih Jenis Kelamin</option>
                                    <option value="laki-laki">Laki-laki</option>
                                    <option value="perempuan">Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
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
            $('#form-store-pegawai').on('submit',function (e) {
                e.preventDefault();
                var action = this.action;
                var formData = new FormData($(this)[0]);
                swal({
                    title: 'Ingin Menyimpan Data?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
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
                                    // location.href = res.response.detail_uri
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
