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
          <a href="{{route('pegawai.add')}}" class="btn btn-success">Tambah Pegawai</a>
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
                    $.get('{{route('api.web.master-data.pegawai.page')}}?q='+search)
                        .then(function (res) {
                            if (res.halaman == 0){
                                if (search != '') {
                                    $('.list_pegawai').html('<tr style="text-align: center"><td colspan="100">Kata Kunci "<i>' + search + '</i>" Tidak Ditemukan</td></tr>')
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
                    var selector = $('.list_pegawai');
                    $('#preload').show();
                    $.ajax({
                        url: "{{ route('api.web.master-data.pegawai') }}?page="+page+'&q='+search,
                        data: '',
                        success: function(res) {
                            if (res.response.length > 0) {
                                var data = res.response.map(function (val) {
                                    var row = '';
                                    var foto = val.foto ? "{{url('')}}/storage/" + val.foto : "{{url('assets/images/img-user.png')}}"
                                    row += "<tr>";
                                    row += "<td><div class='img-user' id='user1' style='background-image: url(" + foto + ");'></div></td>";
                                    row += "<td><a href='" + val.detail_uri + "'>" + val.nip + "</a></td>";
                                    row += "<td>" + val.nama + "</td>";
                                    row += "<td>" + val.jabatan.jabatan + "</td>";
                                    row += "<td>" + val.skpd.nama_skpd + "</td>";
                                    row += "<td>" + val.jns_kel + "</td>";
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
                            .then(function () {
                                getPage(search);
                                swal(
                                    'Terhapus!',
                                    'Data Pegawai Berhasil Dihapus.',
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
