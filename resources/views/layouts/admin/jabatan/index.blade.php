@extends('layouts.admin.partial.main')
@section('content')
  <div class="main">
      <div class="nav-top-container">
          <div class="group-search">
              <span><i class="fas fa-search"></i></span>
              <input type="text" class="form-control" placeholder="Cari Jabatan">
          </div>
          @include('layouts.admin.partial.part.logout')
      </div>
    <div class="main-content">
        <div class="container-fluid">
            <a href="{{route('jabatan.add')}}" class="btn btn-success">Tambah Jabatan</a>
            <hr>
            <div class="table-responsive">
              <table class="table table-jabatan">
                <thead>
                  <tr>
                    <th scope="col"></th>
                    <th scope="col">Jabatan</th>
                    <th scope="col">Eselon</th>
                    <th scope="col">Atasan</th>
                    <th scope="col">Aksi</th>
                  </tr>
                </thead>
                <tbody class="list_jabatan">
                </tbody>
              </table>              
            </div>
          <div class="box-pagination">
            <ul class="pagination" id="pagination"></ul>
          </div>
        </div>
    </div>
  </div>
    @push('script')
            <script>
                $(document).ready(function(){
                    getPage();
                });
                var getPage = function () {
                    $('#pagination').twbsPagination('destroy');
                    $.get('{{route('page_jabatan')}}')
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
                    var selector = $('.list_jabatan');
                    $.ajax({
                        url: "{{ route('list_jabatan') }}?page="+page,
                        data: '',
                        success: function(res) {
                            var data = res.response.map(function (val) {
                                var row = '';
                                row += "<tr>";
                                row += "<td></td>";
                                row += "<td>"+val.jabatan+"</td>";
                                row += "<td>"+val.eselon.eselon+"</td>";
                                row += "<td>"+(val.atasan ? val.atasan.jabatan : '')+"</td>";
                                row += "<td><div class='btn-group mr-2' role='group' aria-label='Edit'><a href='"+val.edit_uri+"' class='btn btn-success'><i class='fas fa-edit'></i></a><button type='button' delete-uri='"+val.delete_uri+"' class='btn btn-danger btn-delete'><i class='fas fa-trash'></i></button></div></td>";
                                row += "</tr>";
                                return row;
                            })
                            selector.html(data.join(''));
                        }
                    });
                }
                $(document).on('click','.btn-delete',function (e) {
                    e.preventDefault();
                    var delete_uri = $(this).attr('delete-uri');
                    swal({
                        title: 'Yakin Ingin Menghapus Jabatan?',
                        text: "Proses tidak dapat di kembalikan",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Iya, Hapus Jabatan!',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.value) {
                        $.post(delete_uri)
                            .then(function () {
                                getPage();
                                swal(
                                    'Terhapus!',
                                    'Data Jabatan Berhasil Dihapus.',
                                    'success'
                                )
                            },function () {
                                swal(
                                    'Gagal Menghapus Data',
                                    '',
                                    'warning'
                                )
                            })
                        }
                    })
                })
            </script>
    @endpush
@endsection
