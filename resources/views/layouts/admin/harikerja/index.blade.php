@extends('layouts.admin.partial.main')
@section('content')
  <div class="main">
    <div class="nav-top-container">
        <div class="group-search">
          <span><i class="fas fa-search"></i></span>
          <input type="text" id='search' class="datepicker form-control" placeholder="Cari Hari Kerja">
        </div>
        @include('layouts.admin.partial.part.logout')
    </div>
    <div class="main-content">
        <div class="container-fluid">
          <table class="table table-responsive table-hari-kerja">
            <thead>
              <tr>
                <th scope="col">Tanggal</th>
                <th scope="col">Tahun</th>
                <th scope="col">Bulan</th>
                <th scope="col">Tanggal</th>
                <th scope="col">Status</th>
                <th scope="col">Aksi</th>
              </tr>
            </thead>
            <tbody class="list_hari_kerja">
            </tbody>
          </table>
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
        getData('',1);
      });
      var getKey = '';
      $("#search").on("change",function(e) {
        var getkey = this.value.replace(/(\d\d)\/(\d\d)\/(\d{4})/, "$3-$1-$2");
        getData(getkey,1);
        getPage(getkey);
      });
      var getPage = function (getKey) {
        $('#pagination').twbsPagination('destroy');
        $.get('{{route('page_hari_kerja')}}?q='+getKey)
        .then(function (res) {
          $('#pagination').twbsPagination({
            totalPages: res.halaman,
            visiblePages: 5,
            onPageClick: function (event, page) {
              getData(getKey,page);
            }
          });
        })
      };
      var getData = function (getKey='',page) {
        var base = "{{ route('list_hari_kerja') }}?q="+getKey+"&page="+page;
        var row = '';
        var selector = $('.list_hari_kerja');
        $.ajax({
          url: base,
          data: '',
          success: function(res) {
            if (res.diagnostic.status == 'HTTP_OK') {
              for(i = 0; i < res.response.length; i++) {
                row += "<tr>";
                row += "<td>"+res.response[i].tanggal+"</td>";
                row += "<td>"+res.response[i].tahun+"</td>";
                row += "<td>"+res.response[i].bulan+"</td>";
                row += "<td>"+res.response[i].hari+"</td>";
                row += "<td>"+res.response[i].id_status_hari+"</td>";
                row += "<td><div class='btn-group mr-2' role='group' aria-label='Edit'><button type='button' class='btn btn-success'><i class='fas fa-edit'></i></button><button type='button' class='btn btn-danger'><i class='fas fa-trash'></i></button></div></td>";
                row += "</tr>";
              }
            } else {
              row += "<tr class='text-center'>";
              row += "<td colspan='6'>Data tidak ditemukan</td>";
              row += "</tr>";
            }
            selector.html(row);
          }
        });
      }
    </script>
  @endpush
@endsection
