@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
        <div class="nav-top-container">
            @include('layouts.admin.partial.part.logout')
        </div>
        <div class="main-content tab-content">
            <div class="container-fluid">
                <form id="form-store-skpd" action="{{route('api.web.master-data.skpd.store')}}" class="form" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="mb-2">Tambah SKPD</h2>
                            <div class="form-group">
                                <label for="nama_skpd">Nama SKPD</label>
                                <input id="nama_skpd" type="text" name="nama_skpd" class="form-control" placeholder="Nama SKPD" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea id="keterangan" name="keterangan" class="form-control"></textarea>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                        <div class="col-md-6">

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('script')
        <script>
            $('#form-store-skpd').on('submit',function (e) {
                e.preventDefault();
                var action = this.action;
                var formData = new FormData($(this)[0]);
                swal({
                    title: 'Ingin Menyimpan Data?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Iya, simpan data!',
                    cancelButtonText: 'Batalkan'
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
                                    location.href = res.response.detail_uri
                                }, 500);
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
