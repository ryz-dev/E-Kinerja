@extends('layouts.admin.partial.main')
@section('content')
  <div class="main">
    <div class="nav-top-container">
        <div class="group-search">
            <span><i class="fas fa-search"></i></span>
            <input id="search" type="text" class="form-control" placeholder="NIP Pegawai">
        </div>
        @include('layouts.admin.partial.part.logout')
    </div>
    <div class="main-content">
        <div class="container-fluid">
          <a href="{{route('checkinout.create')}}" class="btn btn-success"><i class="fa fa-plus"></i> Tambah Absen</a>
          <button id="sync-btn"  class="btn btn-info"><i class="fa fa-sync"></i> Sinkronisasi Data Absen</button>
          <br><br>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Nip</th>
                        <th scope="col">Waktu Absen</th>
                        <th scope="col">Tipe Absen</th>
                        <th scope="col">Verify Code</th>
                        <th scope="col">Sensor ID</th>
                        <th scope="col">Work Code</th>
                        <th scope="col">SN</th>
                        <th scope="ccol">Nama Pegawai</th>
                        <th scope="col">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="" id="data_checkinout">
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
  	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/id.js"></script>
  	<script>
  		$(document).ready(function(){
        getPage('');
  		});

      var getPage = function (search) {
          $('#pagination').twbsPagination('destroy');
          $.get('{{route('api.web.master-data.checkinout.page')}}?q='+search)
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
        var selector = $('#data_checkinout');
          $('#preload').show();
          $.ajax({
              url: "{{ route('api.web.master-data.absen-list') }}?page="+page+'&q='+search,
              data: '',
              success: function(res) {
                  var data = res.response.map(function (val) {
                    let date = moment(val.checktime);
                      var row = '';
                      row += "<tr>";
                      row += "<td></td>";
                      row += "<td>"+val.nip+"</td>";
                      row += "<td>"+date.fromNow()+"</td>";
                      row += "<td>"+(val.checktype ? val.checktype : '')+"</td>";
                      row += "<td>"+(val.verifycode ? val.verifycode : '')+"</td>";
                      row += "<td>"+(val.sensorid ? val.sensorid : '')+"</td>";
                      row += "<td>"+(val.workcode ? val.workcode : '')+"</td>";
                      row += "<td>"+(val.sn ? val.sn : '')+"</td>";
                      row += "<td>"+(val.pegawai ? val.pegawai : '')+"</td>";
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

      $(document).on('click','#sync-btn', function(e){
        e.preventDefault();
            Swal.queue([{
            title: 'Sinkronisasi data absensi',
            confirmButtonText: 'Sinkronisasi!',
            text:
                'Data absensi akan di sinkronisasi dengan data kinerja ' +
                'proses ini mungkin membutuhkan beberapa waktu, mohon untuk tidak menutup jendela ini sampai proses selesai',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return fetch('{{ route('checkinout.sync') }}')
                .then(response => response.json())
                .then(data =>
                    swal(
                        'Berhasil',
                        'Data Absen Berhasil Di Sinkronisasi.',
                        'success'
                    )
                )
                .catch(() => {
                    Swal.insertQueueStep({
                    type: 'error',
                    title: 'Terjadi Error Saat Proses Sinkronisasi'
                    })
                })
            }
        }])
      });

      $(document).on('click','.btn-delete',function (e) {
                    e.preventDefault();
                    var delete_uri = $(this).attr('delete-uri');
                    var search = $('#search').val();
                    swal({
                        title: 'Yakin Ingin Menghapus Absen?',
                        text: "Proses tidak dapat di kembalikan",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Iya, Hapus Absen!',
                        cancelButtonText: 'Tidak'
                    }).then((result) => {
                        if (result.value) {
                        $.post(delete_uri)
                            .then(function () {
                                getPage(search);
                                swal(
                                    'Terhapus!',
                                    'Data Absen Berhasil Dihapus.',
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
