@extends('layouts.admin.partial.main')
@section('content')
  <div class="main">
    <div class="nav-top-container">
        <div class="group-search">
          <span><i class="fas fa-search"></i></span>
          <input type="text" id='search' class="datepicker form-control" placeholder="Atur tanggal">
        </div>
        @include('layouts.admin.partial.part.logout')
    </div>
    <div class="main-content" id="wrapBox">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <div class="loading">
          <img src="{{ asset('assets/images/loading.gif') }}" alt="loading">
        </div>
        <div class="container-fluid">
          <a href="{{route('hari_kerja_add')}}" class="btn btn-success">Tambah Hari Kerja</a>
          <br><br>
          <div class="table-responsive">
            <table class="table table-hari-kerja">
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
        // getData('',1);
        var getKey = '';
      });

      $("#search").on("change",function(e) {
        var getkey = this.value.replace(/(\d\d)\/(\d\d)\/(\d{4})/, "$3-$1-$2");
        // getData(getkey,1);
        getPage(getkey);
      });

      var getPage = function (getKey) {
        $('#pagination').twbsPagination('destroy');
        $.get('{{route('page_hari_kerja')}}?q='+getKey)
        .then(function (res) {
            if (res.halaman == 0){
                $('.loading').hide();
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
          beforeSend: function() {
            $('.loading').show();
          },
          success: function(res) {
            if (res.diagnostic.status == 'HTTP_OK') {
              for(i = 0; i < res.response.length; i++) {
                row += "<tr id='_id_"+res.response[i].id+"'>";
                row += "<td>"+res.response[i].tanggal+"</td>";
                row += "<td>"+res.response[i].tahun+"</td>";
                row += "<td>"+res.response[i].bulan+"</td>";
                row += "<td>"+res.response[i].hari+"</td>";
                row += "<td>"+res.response[i].status_hari+"</td>";
                row += "<td><div class='btn-group mr-2' role='group' aria-label='Button Action'><a href='"+res.response[i].url_edit+"' class='btn btn-success'><i class='fas fa-edit'></i></a><button type='button' data-id='"+res.response[i].id+"' class='deleteData btn btn-danger'><i class='fas fa-trash'></i></button></div></td>";
                row += "</tr>";
              }
            } else {
              row += "<tr class='text-center'>";
              row += "<td colspan='6'>Data tidak ditemukan</td>";
              row += "</tr>";
            }
            selector.html(row);
              $('.loading').hide();
          },
          error: function(xhr) {
            console.log(xhr.statusText + xhr.responseText);
          },
          complete: function() {
            $('.loading').hide();
          }
        });
      }

      $(document).on('click','.deleteData',function (e) {
          swal({
            title: 'Yakin Ingin Menghapus Pegawai?',
            text: "Proses tidak dapat di kembalikan",
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Hapus'
          }).then((result) => {
            if (result.value) {
              var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
              var target = $(this).data('id');
              $.ajax({
                url: '{{ route('delete_hari_kerja') }}',
                type: 'POST',
                data: {_token: CSRF_TOKEN, id:target},
                dataType: 'JSON',
                success: function (data) {
                  $("#_id_"+target).hide(100);
                  getPage('');
                }
              });
              swal(
                'Terhapus!',
                'Data berhasil di hapus.',
                'success'
              )
            }
          })
      });
    </script>
  @endpush
@endsection
