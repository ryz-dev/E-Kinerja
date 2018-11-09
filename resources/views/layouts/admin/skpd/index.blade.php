@extends('layouts.admin.partial.main')
@section('content')
  <div class="main">
    <div class="nav-top-container">
        <div class="group-search">
            <span><i class="fas fa-search"></i></span>
            <input id="search" type="text" class="form-control" placeholder="Cari Nama SKPD">
        </div>
        @include('layouts.admin.partial.part.logout')
    </div>
    <div class="main-content">
        <div class="container-fluid">
          <a href="{{route('skpd.add')}}" class="btn btn-success">Tambah SKPD</a>
          <br><br>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col">Nama SKPD</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="list_skpd">
                    </tbody>
                </table>
                <div class="box-pagination">
                    <ul class="pagination" id="pagination"></ul>
                </div>
            </div>
        </div>
    </div>
  </div>
    @push('script')
            <script>
                $(document).ready(function(){
                    getPage('');
                });
                var getPage = function (search) {
                    $('#pagination').twbsPagination('destroy');
                    $.get('{{route('api.web.master-data.skpd.page')}}?q='+search)
                        .then(function (res) {
                            if (res.halaman == 0){
                                if (search != '') {
                                    $('.list_skpd').html('<tr style="text-align: center"><td colspan="100">Kata Kunci "<i>' + search + '</i>" Tidak Ditemukan</td></tr>')
                                } else {
                                    $('.list_skpd').html('<tr style="text-align: center"><td colspan="100">Data Tidak Ditemukan</td></tr>')
                                }
                                $('#preload').hide();
                            }
                            if (res.halaman == 1){
                                $('#pagination').hide();
                            } else {
                                $('#pagination').show();
                            }
                            $('#pagination').twbsPagination({
                                totalPages: res.halaman,
                                visiblePages: 5,
                                onPageClick: function (event, page) {
                                    getData(page,search);
                                }
                            });
                        })
                };
                var getData = function (page,search) {
                    var selector = $('.list_skpd');
                    $('#preload').show();
                    $.ajax({
                        url: "{{ route('api.web.master-data.skpd') }}?page="+page+'&q='+search,
                        data: '',
                        success: function(res) {
                            if (res.response.length > 0) {
                                var data = res.response.map(function (val) {
                                    var row = '';
                                    row += "<tr>";
                                    row += "<td>" + val.nama_skpd + "</td>";
                                    row += "<td>" + val.keterangan + "</td>";
                                    row += "<td><div class='btn-group mr-2' role='group' aria-label='Edit'><a href='" + val.edit_uri + "' class='btn btn-success'><i class='fas fa-edit'></i></a><button type='button' delete-uri='" + val.delete_uri + "' class='btn btn-danger btn-delete'><i class='fas fa-trash'></i></button></div></td>";
                                    row += "</tr>";
                                    return row;
                                })
                                selector.html(data.join(''));
                            } else {
                                selector.html('<tr style="text-align: center"><td colspan="100">Kata Kunci "<i>'+search+'</i>" Tidak Ditemukan</td></tr>')
                            }
                            $('#preload').hide();
                        },
                        complete: function () {
                            $('#preload').hide();
                        }
                    });
                }
                $(document).on('click','.btn-delete',function (e) {
                    e.preventDefault();
                    var delete_uri = $(this).attr('delete-uri');
                    var search = $('#search').val();
                    swal({
                        title: 'Yakin Ingin Menghapus SKPD?',
                        text: "Proses tidak dapat di kembalikan",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Iya, Hapus SKPD!',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.value) {
                        $.post(delete_uri)
                            .then(function () {
                                getPage(search);
                                swal(
                                    'Terhapus!',
                                    'Data SKPD Berhasil Dihapus.',
                                    'success'
                                )
                            },function () {
                                swal(
                                    'Gagal Menghapus Data',
                                    '',
                                    'error'
                                )
                            })
                        }
                    })
                })
                $('#search').on('keyup',function (e) {
                    e.preventDefault();
                    getPage($(this).val())
                })
            </script>
    @endpush
@endsection
