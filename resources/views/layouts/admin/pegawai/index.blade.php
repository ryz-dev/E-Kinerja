@extends('layouts.admin.partial.main')
@section('content')
  <div class="main">
      <div class="nav-top-container">
          <div class="group-search">
              <span><i class="fas fa-search"></i></span>
              <input type="text" class="form-control" placeholder="Cari Nama / NIP Pegawai">
          </div>
          @include('layouts.admin.partial.part.logout')
      </div>
    <div class="main-content">
        <div class="container-fluid">
            <a href="{{route('pegawai.add')}}" class="btn btn-success">Tambah Pegawai</a>
          <table class="table table-responsive table-pegawai">
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">NIP Pegawai</th>
                  <th scope="col">Nama Pegawai</th>
                  <th scope="col">Jabatan</th>
                  <th scope="col">Jenis Kelamin</th>
                  <th scope="col">Aksi</th>
                </tr>
              </thead>
              <tbody class="list_pegawai">
              </tbody>
          </table>
            <div class="row text-center">
                <ul class="pagination" id="pagination"></ul>
            </div>
        </div>
    </div>
  </div>
    @push('script')
            <script>
                $(document).ready(function(){
                    getPage();
                    getData(1);
                });
                var getPage = function () {
                    $('#pagination').twbsPagination('destroy');
                    $.get('{{route('page_pegawai')}}')
                        .then(function (res) {
                            $('#pagination').twbsPagination({
                                totalPages: res.halaman,
                                visiblePages: 5,
                                onPageClick: function (event, page) {
                                    getData(page);
                                }
                            });
                        })
                };
                var getData = function (page) {
                    var listArr = [];
                    var row = '';
                    var selector = $('.list_pegawai');
                    $.ajax({
                        url: "{{ route('list_pegawai') }}?page="+page,
                        data: '',
                        success: function(res){
                            for(i = 0; i < res.response.data.length; i++) {
                                row += "<tr>";
                                row += "<td><div class='img-user' id='user1' style='background-image: url({{ asset('assets/images/img-user.png') }});'></div></td>";
                                row += "<td><a href='"+res.response.data[i].detail_uri+"'>"+res.response.data[i].nip+"</a></td>";
                                row += "<td>"+res.response.data[i].nama+"</td>";
                                row += "<td>"+res.response.data[i].jabatan.jabatan+"</td>";
                                row += "<td>"+res.response.data[i].jns_kel+"</td>";
                                row += "<td><div class='btn-group mr-2' role='group' aria-label='Edit'><a href='"+res.response.data[i].edit_uri+"' class='btn btn-success'><i class='fas fa-edit'></i></a><button type='button' delete-uri='"+res.response.data[i].delete_uri+"' class='btn btn-danger btn-delete'><i class='fas fa-trash'></i></button></div></td>";
                                row += "</tr>";
                            }
                            selector.html(row);
                        }
                    });
                }
                $(document).on('click','.btn-delete',function (e) {
                    e.preventDefault();
                    var delete_uri = $(this).attr('delete-uri');
                    swal({
                        title: 'Yakin Ingin Menghapus Pegawai?',
                        text: "Proses tidak dapat di kembalikan",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Iya, Hapus Pegawai!',
                        cancelButtonText: 'Tidak'
                    }).then(() => {
                        // if (result.value) {
                        $.post(delete_uri)
                            .then(function () {
                                getPage();
                                getData(1);
                                swal(
                                    'Terhapus!',
                                    'Data Pegawai Berhasil Dihapus.',
                                    'success'
                                )
                            },function () {
                                swal(
                                    'Gagal Menghapus Data',
                                    '',
                                    'warning'
                                )
                            })
                        // }
                    })
                })
            </script>
    @endpush
@endsection
