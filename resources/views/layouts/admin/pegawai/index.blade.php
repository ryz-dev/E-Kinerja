@extends('layouts.admin.partial.main')
@section('content')
  <div class="main-content">
      <div class="container-fluid">
          <div class="row">
              <div class="col-md-9">
                  <div class="row">
                      <div class="col-md-12">
                          <div class="title-nav float-left">
                              <h4 class="mr-3 float-left">Monitoring Absen</h4>
                              <span class="badge text-white">23 September 2018</span>
                          </div>
                          <div class="btn-control float-right mt-1">
                              <div class="date-group float-left">
                                  <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                  <input class="datepicker" placeholder="Pilih Tanggal" />
                              </div>
                              <div class="float-right">
                                  <button class="btn btn-rounded"><i class="fas fa-angle-left"></i></button>
                                  <button class="btn btn-rounded active"><i class="fas fa-angle-right"></i></button>
                              </div>
                          </div>
                          <div class="clearfix"></div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-12">
                          <table class="table table-responsive table-pegawai">
                              <thead>
                                  <tr>
                                      <th scope="col"></th>
                                      <th scope="col">NIP Pegawai</th>
                                      <th scope="col">Nama Pegawai</th>
                                      <th scope="col">Jabatan</th>
                                      <th scope="col">Jenis Kelamin</th>
                                  </tr>
                              </thead>
                              <tbody class="list_pegawai">
                                  {{-- @forelse ($pegawai as $key => $pgw)
                                    <tr>
                                        <td>
                                            <div class="img-user" id="user1" style="background-image: url('{{ asset('assets/images/img-user.png') }}');">
                                            </div>
                                        </td>
                                        <td>{{ $pgw->nip }}</td>
                                        <td>{{ $pgw->nama }}</td>
                                        <td>{{ $pgw->jabatan->jabatan }}</td>
                                        <td>{{ $pgw->jns_kel }}</td>
                                    </tr>
                                  @empty

                                  @endforelse --}}
                              </tbody>

                          </table>
                      </div>
                      <div class="col-md-12 text-center">
                          <ul class="pagination" id="pagination"></ul>
                      </div>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="description">

                  </div>
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
                          row += "<td></td>";
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

