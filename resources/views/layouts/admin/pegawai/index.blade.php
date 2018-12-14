@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
        <div class="nav-top-container">
            <div class="group-search">
                <span><i class="fas fa-search"></i></span>
                <input id="search" type="text" class="form-control" placeholder="Cari Nama / NIP Pegawai">
            </div>
            @include('layouts.admin.partial.part.logout')
        </div>
        <div class="main-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        <a href="{{route('pegawai.add')}}" class="btn btn-success">Tambah Pegawai</a>
                    </div>
                    <div class="col-md-2">
                        <a href="{{route('pegawai.add')}}" class="btn btn-primary">Download Rekap</a>
                    </div>
                    <div class="col-md-5">
                        <form id="import-pegawai" action="{{route('pegawai.import')}}" method="post" enctype="multipart/form-data">
                            {{csrf_field()}}
                            <input type="file" name="import" class=""
                                   accept="application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                                   placeholder="Excel" value="" required>
                            <button type="submit" class="btn btn-primary">Import</button>
                        </form>
                    </div>
                </div>
                <br><br>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col">NIP Pegawai</th>
                            <th scope="col">Nama Pegawai</th>
                            <th scope="col">Jabatan</th>
                            <th scope="col">SKPD</th>
                            <th scope="col">Jenis Kelamin</th>
                            <th scope="col">Aksi</th>
                        </tr>
                        </thead>
                        <tbody class="list_pegawai">
                        </tbody>
                    </table>
                    <div class="box-pagination">
                        <ul class="pagination pagination-custome" id="pagination"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('script')
        <script>
            $(document).ready(function () {
                getPage('');
            });
            var getPage = function (search) {
                $('#pagination').twbsPagination('destroy');
                $.get('{{route('api.web.master-data.pegawai.page')}}?q=' + search)
                    .then(function (res) {
                        if (res.halaman == 0) {
                            if (search != '') {
                                $('.list_pegawai').html('<tr style="text-align: center"><td colspan="100">Kata Kunci "<i>' + search + '</i>" Tidak Ditemukan</td></tr>')
                            }
                            $('#preload').hide();
                        }
                        if (res.halaman == 1) {
                            $('#pagination').hide();
                        } else {
                            $('#pagination').show();
                        }
                        $('#pagination').twbsPagination({
                            totalPages: res.halaman,
                            visiblePages: 5,
                            onPageClick: function (event, page) {
                                getData(page, search);
                            }
                        });
                    })
            };
            var getData = function (page, search) {
                var selector = $('.list_pegawai');
                $('#preload').show();
                $.ajax({
                    url: "{{ route('api.web.master-data.pegawai') }}?page=" + page + '&q=' + search,
                    data: '',
                    success: function (res) {
                        if (res.response.length > 0) {
                            var data = res.response.map(function (val) {
                                var row = '';
                                var foto = val.foto ? "{{url('')}}/storage/" + val.foto : "{{url('assets/images/img-user.png')}}"
                                row += "<tr>";
                                row += "<td><div class='img-user' id='user1' style='background-image: url(" + foto + ");'></div></td>";
                                row += "<td><a href='" + val.detail_uri + "'>" + val.nip + "</a></td>";
                                row += "<td>" + val.nama + "</td>";
                                row += "<td>" + (val.jabatan ? val.jabatan.jabatan : '') + "</td>";
                                row += "<td>" + (val.skpd ? val.skpd.nama_skpd : '') + "</td>";
                                row += "<td>" + val.jns_kel + "</td>";
                                row += "<td><div class='btn-group mr-2' role='group' aria-label='Edit'><a href='" + val.edit_uri + "' class='btn btn-success'><i class='fas fa-edit'></i></a><button type='button' delete-uri='" + val.delete_uri + "' class='btn btn-danger btn-delete'><i class='fas fa-trash'></i></button></div></td>";
                                row += "</tr>";
                                return row;
                            })
                            selector.html(data.join(''));
                        } else {
                            selector.html('<tr style="text-align: center"><td colspan="100">Kata Kunci "<i>' + search + '</i>" Tidak Ditemukan</td></tr>')
                        }
                        $('#preload').hide();
                    },
                    complete: function () {
                        $('#preload').hide();
                    }
                });
            }
            $(document).on('click', '.btn-delete', function (e) {
                e.preventDefault();
                var delete_uri = $(this).attr('delete-uri');
                var search = $('#search').val();
                swal({
                    title: 'Yakin Ingin Menghapus Pegawai?',
                    text: "Proses tidak dapat di kembalikan",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    confirmButtonText: 'Iya, Hapus Pegawai!',
                    cancelButtonText: 'Tidak'
                }).then((result) => {
                    if (result.value) {
                        $.post(delete_uri)
                            .then(function (res) {
                                if (res.response.status == '200') {
                                    getPage(search);
                                    swal(
                                        'Terhapus!',
                                        'Data Pegawai Berhasil Dihapus.',
                                        'success'
                                    )
                                } else {
                                    swal(
                                        'Gagal Menghapus Data Pegawai',
                                        res.response.message,
                                        'error'
                                    )
                                }
                            }, function () {
                                swal(
                                    'Gagal Menghapus Data',
                                    '',
                                    'error'
                                )
                            })
                    }
                })
            })
            $('#search').on('keyup', function (e) {
                e.preventDefault();
                getPage($(this).val())
            })
            $('#import-pegawai').on('submit',function (e) {
                e.preventDefault();
                var action = this.action;
                var formData = new FormData($(this)[0]);
                swal({
                    title: 'Ingin Mengimport Pegawai?',
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
                                    'Berhasil Mengimport Data!',
                                    '',
                                    'success'
                                )
                                getPage('')
                            },
                            error: function () {
                                swal(
                                    'Gagal Mengimport Data!',
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
