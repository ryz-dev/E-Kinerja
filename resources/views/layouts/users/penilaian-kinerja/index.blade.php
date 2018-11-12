@extends('layouts.users.partial.main')
@section('class','penilaian-kinerja')
@section('content')
  <div class="main">
      <div class="nav-top-container">
          <div class="nav-top">
              <div class="title-nav">
                  <h4 class="mr-4">Penilaian Kinerja</h4>
                  <span class="badge text-white">{{date('d')}} {{ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan)}} {{date('Y')}}</span>
              </div>
              <div class="img-profile" id="user-profile" style="background-image: url('assets/images/img-user.png');"></div>
              <div class="profile">
                  <div class="row">
                      <div class="col-md-12">
                          <div class="profile-img">
                              <div class="img-profile" style="background-image: url('assets/images/img-user.png');"></div>
                          </div>
                          <br>
                          <div class="profile-name">
                              <label>Administrator</label>
                          </div>
                      </div>
                  </div>
                  <a href="login.html" class="btn btn-block" id="btn-logout">Logout</a>
              </div>
          </div>
      </div>
      <div class="sidebar2">
          <div class="burgerBtn">
              <div class="bar1"></div>
              <div class="bar2"></div>
              <div class="bar3"></div>
          </div>
          <div class="group-search">
              <span><i class="fas fa-search"></i></span>
              <input type="text" class="form-control" placeholder="Cari Nama / NIP Pegawai">
          </div>
          <div class="menu">
            <input type="hidden" name="total-index">
            <ul class="nav nav-pills" id="myTab" role="tablist" data="data-bawahan"></ul>
          </div>
      </div>
      <div class="main-content tab-content">

          <div class="tab-pane detailItem" id="user1" role="tabpanel">
              <div class="container">
                  <input type="hidden" name="index" value="0">
                  <div class="row">
                      <div class="col-md-12">
                          <div class="img-user" id="detail-img" style="background-image: url('images/img-user.png');"></div>
                          <div class="nama-id">
                              <h6 id="detail-nama"></h6>
                              <span id="detail-nip"></span>
                          </div>
                          <div class="btn-control float-right">
                            <button id="pegawai-sebelumnya" inc-index="-1" class="btn btn-rounded prev"><i class="fas fa-angle-left"></i></button>
                            <button id="pegawai-selanjutnya" inc-index="1" class="btn btn-rounded next active"><i class="fas fa-angle-right"></i></button>
                          </div>
                          <div class="clearfix"></div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-12">
                          <h6 class="mb-2 mt-4">Rincian Kinerja Harian</h6>
                          <div class="desc-kinerja">
                            <p id="ket_kinerja"></p>
                          </div>
                      </div>
                  </div>
                  <div class="row" id='wrapReply'>
                      <div class="col-md-12">
                          <h6 class="mb-2 mt-4">Keterangan Penilaian Kinerja</h6>
                          <form id='formReply'>
                            <input id="id" type='hidden' name='id' required>
                            <input id="userid" type='hidden' name='userid' required>
                            <textarea autofocus rows="8" name="keterangan_approve" class="form-control"></textarea>
                          </form>
                          <div class="mt-2 float-right">
                            <button type="button" data-action='0' class="btn-approve btn btn-custom-2">Tolak</button>
                            <button type="button" data-action='1' class="btn-approve btn btn-custom">Simpan</button>
                          </div>
                      </div>
                  </div>
              </div>
          </div>

      </div>
  </div>
  @push('script')
      <script>
          var getBawahan = function () {
            $.get('{{route('api.web.rekap-bulanan.get-bawahan')}}')
              .then(function (res) {
                  if (res.response.length > 0) {
                      var data = res.response.map(function (val, i) {
                          var foto = val.foto ? "{{url('')}}/storage/" + val.foto : "{{url('assets/images/img-user.png')}}"
                          return '<li class="list-bawahan" data-foto="' + foto + '" data-index="' + i + '" data-nip="' + val.nip + '" data-nama="' + val.nama + '"><a class="listSelect ' + (i == 0 ? 'active' : '') + '" data-toggle="tab" href="#' + val.nip + '" role="tab" aria-selected="true"><span\n' +
                              'class="img-user" id="img-user1" style="background-image: url(' + foto + ');">\n' +
                              '</span>\n' +
                              '<span>\n' +
                              '<label>' + val.nama + '<br><small>' + val.nip + '</small></label>\n' +
                              '</span>\n' +
                              '<div class="not-list float-right mr-3"><i class="fas fa-lg fa-times"></i></div>\n' +
                              '</a>\n' +
                              '</li>'
                      })
                      $('[data=data-bawahan]').html(data.join(''));
                      $('[name=total-index]').val(data.length - 1);
                      setTimeout(function () {
                        $('[data-index=0]').click();
                      }, 1000)
                  } else {
                      $('[data=data-bawahan]').html("<label>Data Tidak Ditemukan</label>");
                  }
              })
          };

          // $('#search').on('keyup', function (e) {
          //     e.preventDefault();
          //     key = $(this).val()
          //     $('.list-bawahan').hide();
          //     if (key) {
          //         find = $('[data-nip*="' + key + '"],[data-nama*="' + key + '"]');
          //         find.attr('search', true);
          //         find.show()
          //     } else {
          //         $('.list-bawahan').show();
          //         $('.list-bawahan').removeAttr('search');
          //     }
          // })

          window['trigger'] = 0;

          $(document).on('click', '.list-bawahan[data-index]', function (e) {
              e.preventDefault();
              $('.list-bawahan').find('.listSelect').removeClass('active')
              $(this).addClass('active').siblings().removeClass('active');
              $(this).find('.listSelect').addClass('active')
              var nama = $(this).attr('data-nama');
              var nip = $(this).attr('data-nip');
              var foto = $(this).attr('data-foto');
              var index = $(this).attr('data-index');
              $('#date-rekap').val('');
              $('[name=index]').val(index);
              $('#detail-nama').html(nama);
              $('#detail-nip').html(nip);
              $('#detail-img').css({'background-image': 'url(' + foto + ')'})
              if (index == 0) {
                $('#pegawai-sebelumnya').removeClass('active')
              } else {
                $('#pegawai-sebelumnya').addClass('active')
              }
              if (index == $('[name=total-index]').val()) {
                $('#pegawai-selanjutnya').removeClass('active')
              } else {
                $('#pegawai-selanjutnya').addClass('active')
              }
              getKinerja(nip);
          })

          var getKinerja = function (nip) {
              $('#preload').show();
              $.get('{{route('api.web.get-penilaian-kinerja',['nip' => ''])}}/' + nip)
                  .then(function (res) {
                    if (res.response != null) {
                      $('#id').val(res.response.id);
                      $('#userid').val(res.response.userid);
                      $('#ket_kinerja').html(res.response.rincian_kinerja);
                    } else {
                      $('#ket_kinerja').html('Belum ada rincian kinerja hari ini.');
                      $('#wrapReply').remove();
                    }
                    $('#preload').hide();
                    window['trigger'] = 0;
                }, function () {
              }).catch((err) => {
                $('#preload').hide();
              });
          }

          $('#pegawai-sebelumnya,#pegawai-selanjutnya').on('click', function (e) {
              e.preventDefault();
              var index = $('[name=index]').val();
              var i = $(this).attr('inc-index');
              var new_index = parseInt(index) + parseInt(i);
              var total_index = $('[name=total-index]').val();
              if (new_index => 0 && new_index <= total_index) {
                $('[data-index="' + new_index + '"]').click()
              }
          })

          $(document).ready(function () {
            getBawahan();
            $('.btn-approve').click(function(){
              $("#formReply").serialize();
              $.ajax({
                 type: "POST",
                 url: "{{route('api.web.reply-penilaian-kinerja')}}",
                 data: 'type='+$(this).data('action')+'&'+$("#formReply").serialize(),
                 success: function(data) {
                   console.log(data);
                 }
               });
            });
          });

      </script>
  @endpush
@endsection
