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
                $.get('{{route('api.web.master-data.pegawai.page')}}?q=' + search+'&deleted=1')
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
                    url: "{{ route('api.web.master-data.pegawai') }}?page=" + page + '&q=' + search+'&deleted=1',
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
                                row += "<td><div class='btn-group mr-2' role='group' aria-label='Edit'><a class='btn badge-warning btn-edit' data-nip='"+val.nip+"'><i class=''></i>Kembalikan Data</a></div></td>";
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
            $('#search').on('keyup', function (e) {
                e.preventDefault();
                getPage($(this).val())
            })
            $(document).on('click','.btn-edit',function (e) {
                e.preventDefault();
                nip = $(this).data('nip');
                search = $('#search').val();
                row = $(this).parent().parent().parent();
                swal({
                    title: 'Ingin Mengembalikan data Pegawai?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Iya, simpan data!',
                    cancelButtonText: 'Batalkan'
                }).then((result) => {
                    if (result.value) {
                        $.post('{{route('api.web.master-data.pegawai.restore',['nip' => ''])}}/'+nip)
                            .then(function () {
                                getPage(search)
                                row.fadeOut();
                            },function () {

                            })
                    }
                })

            })
        </script>
    @endpush
@endsection
