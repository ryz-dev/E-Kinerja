@extends('layouts.admin.partial.main')
@section('content')
  <div class="main">
      <div class="nav-top-container">
          <div class="group-search">
              <span><i class="fas fa-search"></i></span>
              <input id="search" type="text" class="form-control" placeholder="Cari Eselon">
          </div>
          @include('layouts.admin.partial.part.logout')
      </div>
    <div class="main-content">
        <div class="loading">
            <img src="{{ asset('assets/images/loading.gif') }}" alt="loading">
        </div>
        <div class="container-fluid">
          <a href="{{route('eselon.add')}}" class="btn btn-success">Tambah Eselon</a>
          <hr>
          <div class="table-responsive">
            <table class="table table-eselon">
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">Eselon</th>
                  <th scope="col">Tunjangan</th>
                  <th scope="col">Keterangan</th>
                  <th scope="col">Aksi</th>
                </tr>
              </thead>
              <tbody class="list_eselon">
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
                    getPage('');
                });
                var getPage = function (search) {
                    $('#pagination').twbsPagination('destroy');
                    $.get('{{route('api.web.eselon.page')}}?q='+search)
                        .then(function (res) {
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
                    var selector = $('.list_eselon');
                    $('.loading').show();
                    $.ajax({
                        url: "{{ route('api.web.eselon') }}?page="+page+'&q='+search,
                        data: '',
                        success: function(res) {
                            var data = res.response.map(function (val) {
                                var row = '';
                                row += "<tr>";
                                row += "<td></td>";
                                row += "<td>"+val.eselon+"</td>";
                                row += "<td>Rp."+val.tunjangan_rp+"</td>";
                                row += "<td>"+(val.keterangan ? val.keterangan : '')+"</td>";
                                row += "<td><div class='btn-group mr-2' role='group' aria-label='Edit'><a href='"+val.edit_uri+"' class='btn btn-success'><i class='fas fa-edit'></i></a><button type='button' delete-uri='"+val.delete_uri+"' class='btn btn-danger btn-delete'><i class='fas fa-trash'></i></button></div></td>";
                                row += "</tr>";
                                return row;
                            })
                            selector.html(data.join(''));
                        },
                        complete : function () {
                            $('.loading').hide();
                        }
                    });
                }
                $(document).on('click','.btn-delete',function (e) {
                    e.preventDefault();
                    var delete_uri = $(this).attr('delete-uri');
                    var search = $('#search').val();
                    swal({
                        title: 'Yakin Ingin Menghapus Eselon?',
                        text: "Proses tidak dapat di kembalikan",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Iya, Hapus Eselon!',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.value) {
                        $.post(delete_uri)
                            .then(function () {
                                getPage(search);
                                swal(
                                    'Terhapus!',
                                    'Data Eselon Berhasil Dihapus.',
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
                    var search = $(this).val();
                    getPage(search);
                })
            </script>
    @endpush
@endsection
