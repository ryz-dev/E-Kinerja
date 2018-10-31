@extends('layouts.admin.partial.main')
@section('content')
  <div class="main">
      <div class="nav-top-container">
          <div class="group-search">
              <span><i class="fas fa-search"></i></span>
              <input type="text" class="form-control" placeholder="Cari Nama / NIP Pegawai">
          </div>
          @include('layouts.partial.part.logout')
      </div>
    <div class="main-content">
        <div class="container-fluid">
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
                                row += "<td>"+res.response.data[i].nip+"</td>";
                                row += "<td>"+res.response.data[i].nama+"</td>";
                                row += "<td>"+res.response.data[i].jabatan.jabatan+"</td>";
                                row += "<td>"+res.response.data[i].jns_kel+"</td></tr>";
                                row += "</tr>";
                            }
                            selector.html(row);
                        }
                    });
                }
            </script>
    @endpush
@endsection
