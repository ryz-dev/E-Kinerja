@extends('layouts.admin.partial.main')
@section('content')
  <div class="main">
      <div class="nav-top-container">
          <div class="group-search">
              <span><i class="fas fa-search"></i></span>
              <input id="search" type="text" class="form-control" placeholder="Cari Golongan">
          </div>
          @include('layouts.admin.partial.part.logout')
      </div>
    <div class="main-content">
        <div class="container-fluid">
          <a href="{{route('golongan.add')}}" class="btn btn-success">Tambah Golongan</a>
          <br><br>
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">Golongan</th>
                  <th scope="col">Kriteria</th>
                  <th scope="col">Tunjangan</th>
                  <th scope="col">Keterangan</th>
                  <th scope="col">Aksi</th>
                </tr>
              </thead>
              <tbody class="list_golongan">
              </tbody>
            </table>
          </div>
          <div class="box-pagination">
            <ul class="pagination pagination-custome" id="pagination"></ul>
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
                    $.get('{{route('api.web.master-data.golongan.page')}}?q='+search)
                        .then(function (res) {
                            if (res.halaman == 0){
                                $('#preload').hide()
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
                    var selector = $('.list_golongan');
                    $('#preload').show();
                    $.ajax({
                        url: "{{ route('api.web.master-data.golongan') }}?page="+page+'&q='+search,
                        data: '',
                        success: function(res) {
                            var data = res.response.map(function (val) {
                                var row = '';
                                row += "<tr>";
                                row += "<td></td>";
                                row += "<td>"+val.golongan+"</td>";
                                row += "<td>"+val.kriteria+"</td>";
                                row += "<td>Rp."+val.tunjangan_rp+"</td>";
                                row += "<td>"+(val.keterangan ? val.keterangan : '')+"</td>";
                                row += "<td><div class='btn-group mr-2' role='group' aria-label='Edit'><a href='"+val.edit_uri+"' class='btn btn-success'><i class='fas fa-edit'></i></a><button type='button' delete-uri='"+val.delete_uri+"' class='btn btn-danger btn-delete'><i class='fas fa-trash'></i></button></div></td>";
                                row += "</tr>";
                                return row;
                            })
                            selector.html(data.join(''));
                            $('#preload').hide();
                        },
                        complete : function () {
                            $('#preload').hide();
                        }
                    });
                }
                $(document).on('click','.btn-delete',function (e) {
                    e.preventDefault();
                    var delete_uri = $(this).attr('delete-uri');
                    var search = $('#search').val();
                    swal({
                        title: 'Yakin Ingin Menghapus Golongan?',
                        text: "Proses tidak dapat di kembalikan",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Iya, Hapus Golongan!',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.value) {
                        $.post(delete_uri)
                            .then(function (res) {
                                if (res.response.status == '200') {
                                    getPage(search);
                                    swal(
                                        'Terhapus!',
                                        'Data Golongan Jabatan Berhasil Dihapus.',
                                        'success'
                                    )
                                } else {
                                    swal(
                                        'Gagal Menghapus Data Golongan Jabatan',
                                        res.response.message,
                                        'error'
                                    )
                                }
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
                    var search = $(this).val();
                    getPage(search);
                })
            </script>
    @endpush
@endsection
