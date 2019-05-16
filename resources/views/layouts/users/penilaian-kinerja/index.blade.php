@extends('layouts.users.partial.main')
@section('class','penilaian-kinerja')
@section('content')
    <div class="main">
        <div class="nav-top-container">
            <div class="nav-top">
                <div class="title-nav">
                    <h4 class="mr-4">Penilaian Kinerja</h4>
                    <span class="badge text-white" id='setDate'>
                    {{-- {{date('d')}} {{ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan)}} {{date('Y')}} --}}
                    
                  </span>
                </div>
                <div class="img-profile" id="user-profile"
                     style="background-image: url('assets/images/img-user.png');"></div>
                @include('layouts.users.partial.part.logout')
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
                <input id="search" type="text" class="form-control" placeholder="Cari Nama / NIP Pegawai">
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
                        <div class="col-md-4 wrap-user">
                            <div class="img-user" id="detail-img"
                                 style="background-image: url('images/img-user.png');"></div>
                            <div class="nama-id">
                                <h6 id="detail-nama"></h6>
                                <span id="detail-nip"></span>
                            </div>
                        </div>
                        <div class="col-md-3 col-8 select-date">
                            <select class="custom-select select-penilaian">
                                <option value="Penilaian SKP" selected>
                                    Penilaian SKP
                                </option>
                                <option value="Penilaian KTJ">
                                    Penilaian KTJ
                                </option>
                            </select>
                        </div>
                        <div class="col-md-3 col-8 select-date">
                            <select class="custom-select" id="missedDate"></select>
                        </div>
                        <div class="col-md-2 col-4">
                            <div class="btn-control float-right">
                                <button id="pegawai-sebelumnya" inc-index="-1" class="btn btn-rounded prev"><i
                                        class="fas fa-angle-left"></i></button>
                                <button id="pegawai-selanjutnya" inc-index="1" class="btn btn-rounded next active"><i
                                        class="fas fa-angle-right"></i></button>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <form id='formReply'>
                <div class="row mt-3" id="list-skp">
                    <div class="col-md-12 mb-1" id="progress-bar">
                        <label class="text-secondary" style="font-size: 1.2em">List SKP</label>
                        <div class="wrap-progress">
                            <label>0%</label>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="25"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-1" id="skp-holder">

                    </div>

                    <div class="col-md-12 mt-2" id="doc-holder">
                        <label class="text-secondary" style="font-size: 1.2em">Dokumen SKP</label>
                        <div>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h6 class="mb-2 mt-4">Rincian Kinerja Harian</h6>
                        <div class="desc-kinerja">
                            <p id="ket_kinerja" style="overflow-wrap: break-word;"></p>
                        </div>
                    </div>
                </div>
                <div class="row" id="slider-nilai-kinerja">
                    <div class="col-md-9">
                        <h6 class="mt-4">Nilai Kinerja</h6>
                        <div class="btn-slider">
                            <div class="slidecontainer">
                                <input type="range" class="slider" min="0" step="1" max="10" id="rate" name="nilai_kinerja"
                                       value="0" oninput="$('[name=thisRate]').val(rate.value)">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-4 value-slider">
                        <div class="values">
                            <output name="thisRate" for="rate">0</output>
                            <div class="clearfix"></div>
                        </div>
                        <!-- <div class="ket">Cukup Baik</div> -->
                    </div>
                </div>
                <div id="boxReply"></div>
                <div class="row" id='wrapReply'>
                    <div class="col-md-12">
                        <h6 class="mb-2 mt-4">Keterangan Penilaian Kinerja</h6>
                            <input id="id" type='hidden' name='id' required>
                            <input id="nip" type='hidden' name='nip' required>
                            <textarea autofocus rows="8" name="keterangan_approve" class="form-control"></textarea>

                        <div class="mt-2 float-right">
                            <button type="button" data-action='2' class="btn-approve btn btn-custom">Simpan</button>
                        </div>
                    </div>
                </div>
                </form>
                <form action="" id="form-kepatuhan" style="display: none">
                    <input type="hidden" name="nip">
                    <div class="list-kepatuhan">
                        <div class="row mt-3">
                            <div class="col-md-12 mb-1">
                                <label class="text-secondary" style="font-size: 1.2em">List Kepatuhan & Tanggung Jawab</label>
                            </div>
                            <div class="col-md-12 mb-1" id="kepatuhan-holder">

                            </div>
                            <div class="col-md-12 mb-2">
                                <label class="text-secondary" style="font-size: 1.2em">Nilai Kepatuhan (Dalam Persentase %)</label>
                            </div>
                            <div class="col-md-12 mb-1">
                                <div class="valuePersen">
                                    <h1><span id="persen-kepatuhan">0</span>%</h1>
                                </div>
                                <button type="submit" class="btn btn-custom">Terima</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
    </div>
    @push('script')
        <script>
            var cacheDOM = $("#wrapReply");
            var storePegawai;
            var now = new Date();
            var dateNow = now.getFullYear() + '-' + (now.getMonth() + 1) + '-' + now.getDate();
            var nameMonth = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            var nameDay = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            var setDate = '';
            var viewDate = now.getDate() + ' ' + nameMonth[now.getMonth()] + ' ' + now.getFullYear();
            var nip;

            var loadData = function (result) {
                if (result.length > 0) {
                    var data = result.map(function (val, i) {
                        var foto = val.foto ? "{{url('')}}/storage/" + val.foto : "{{url('assets/images/img-user.png')}}";
                        var status = '';
                        if (val.kinerja.length != 0) {
                            if (val.kinerja[0].approve == 1) {
                                var attrClass = 'not-list';
                                var status = 'fa-times';
                            } else if (val.kinerja[0].approve == 2) {
                                var attrClass = 'check-list';
                                var status = 'fa-check'
                            }
                        }
                        return '<li class="list-bawahan" data-foto="' + foto + '" data-index="' + i + '" data-nip="' + val.nip + '" data-nama="' + val.nama + '"><a class="listSelect ' + (i == 0 ? 'active' : '') + '" data-toggle="tab" href="#' + val.nip + '" role="tab" aria-selected="true"><span\n' +
                            'class="img-user" id="img-user1" style="background-image: url(' + foto + ');">\n' +
                            '</span>\n' +
                            '<span>\n' +
                            '<label style="width: max-content">' + val.nama + '<br><small>' + val.nip + '</small></label>\n' +
                            '</span>\n' +
                            '<div class="' + attrClass + ' float-right mr-3"><i class="fas fa-lg ' + status + '"></i></div>\n' +
                            '</a>\n' +
                            '</li>'
                    });
                    $('[data=data-bawahan]').html(data.join(''));
                    $('[name=total-index]').val(data.length - 1);
                    setTimeout(function () {
                        $('[data-index=0]').click();
                    }, 1000)
                } else {
                    $('[data=data-bawahan]').html("<label>Data Tidak Ditemukan</label>");
                }
            };

            var getBawahan = function () {
                $.get('{{route('penilaian-kinerja.api.bawahan')}}' + '?date=' + setDate)
                    .then(function (res) {
                        storePegawai = res;
                        loadData(res.response);
                    })
            };
            $(document).on('click', '[name*=skp_pegawai]', function () {
                persentaseSkp();
            })
            ;
            $(document).on('click', '#kepatuhan-holder [type=checkbox]', function () {
                persentaseKepatuhan();
            });

            function persentaseSkp() {
                skp = $('[name*=skp_pegawai]').length ? $('[name*=skp_pegawai]').length : 0;
                skp_checked = $('[name*=skp_pegawai]:checked').length ? $('[name*=skp_pegawai]:checked').length : 0;
                persentase = 0;
                if (skp > 0) {
                    persentase = skp_checked / skp * 100;
                }
                $('.wrap-progress').find('label').html(Math.floor(persentase) + '%');
                $('.wrap-progress').find('.progress-bar').css({width: persentase + '%'})
            }
            function persentaseKepatuhan(){
                var total = $('#kepatuhan-holder [type=checkbox]').length
                var checked = $('#kepatuhan-holder [type=checkbox]:checked').length
                var persen = Math.floor(checked/total * 100)
                if (persen > 0) {
                    $('#persen-kepatuhan').html(persen)
                } else {
                    $('#persen-kepatuhan').html(0)
                }
            }
            persentaseKepatuhan();
            persentaseSkp();
            var getKinerja = function (nip) {
                $('#preload').show();
                $('#list-skp').hide();
                $('#slider-nilai-kinerja').hide();
                $('#form-kepatuhan [name=nip]').val(nip);
                $.get('{{route('penilaian-kinerja.api.kinerja',['nip' => ''])}}/' + nip + '?date=' + setDate)
                    .then(function (res) {
                        if (res.response.kepatuhan){
                            $('.list-kepatuhan').show();
                            var kepatuhan = res.response.kepatuhan.map(function(val){
                                return '<label class="container-check">\n' +
                                    '    <p>'+val.text+'</p>\n' +
                                    '    <input name="'+val.key+'" type="checkbox" '+(val.status ? 'checked' : '')+'>\n' +
                                    '    <span class="checkmark"></span>\n' +
                                    '</label>'
                            });
                            $('#kepatuhan-holder').html(kepatuhan);
                        } else {
                            $('.list-kepatuhan').hide();
                        }
                        persentaseKepatuhan();
                        if (res.response.now != null) {
                            $('#slider-nilai-kinerja').show();
                            $('#id').val(res.response.now.id);
                            $('#nip').val(res.response.now.nip);
                            $('#ket_kinerja').html(res.response.now.rincian_kinerja);
                            $('textarea').val(res.response.now.keterangan_approve);
                            $('[name=thisRate]').html(res.response.now.nilai_kinerja)
                            $('[name=nilai_kinerja]').val(res.response.now.nilai_kinerja)
                            if (res.response.now.jenis_kinerja == 'hadir') {
                                $('#list-skp').show();
                                $('#skp-holder').hide();
                                $('#doc-holder').hide();
                                $('#progress-bar').hide();
                                if (res.response.now.skp_pegawai.length > 0) {
                                    skp = res.response.now.skp_pegawai.map(function (val) {
                                        return '<label class="container-check">\n' +
                                            '                          <p>' + val.skp_task.task + '</p>\n' +
                                            '                          <input type="checkbox" name="skp_pegawai[]" value="' + val.id + '" ' + (val.status > 0 ? 'checked' : '') + '>\n' +
                                            '                          <span class="checkmark"></span>\n' +
                                            '                      </label>'
                                    });
                                    $('#skp-holder').html(skp.join(''));
                                    $('#skp-holder').show();
                                    $('#progress-bar').show();
                                    persentaseSkp();
                                }

                                if (res.response.now.media.length > 0) {
                                    media = res.response.now.media.map(function (val) {
                                        return '<div class="downloads mb-3">\n' +
                                            '                              <img src="/assets/images/icons/word.svg">\n' +
                                            '                              <label>' + val.nama_media + '</label>\n' +
                                            '                              <button onclick="window.open(\''+val.media+'\'); return false" type="submit">\n' +
                                            '                                  <i class="material-icons mr-1">cloud_download</i>\n' +
                                            '                                  Download\n' +
                                            '                              </button>\n' +
                                            '                          </div>'
                                    });
                                    $('#doc-holder>div').html(media.join(''));
                                    $('#doc-holder').show();
                                }
                            }
                        } else {
                            $('#ket_kinerja').html('Belum ada rincian kinerja hari ini.');
                            $(cacheDOM).remove();
                        }
                        var missed = ['<option value="' + dateNow + '">Pilih Tanggal Penilaian saat ini</option>'];
                        if (res.response.old.length > 0) {
                            for (let i = 0; i < res.response.old.length; i++) {
                                date = new Date(res.response.old[i]);
                                viewDate = nameDay[date.getDay()] + ' ,' + date.getDate() + ' ' + nameMonth[date.getMonth()] + ' ' + date.getFullYear();
                                if (setDate == res.response.old[i]) {
                                    missed += `<option value="` + res.response.old[i] + `" selected>` + viewDate + `</option>`
                                } else {
                                    missed += `<option value="` + res.response.old[i] + `">` + viewDate + `</option>`
                                }
                            }
                        }
                        $('#missedDate').html(missed);
                        $('#preload').hide();
                        window['trigger'] = 0;
                    }, function () {
                    }).catch((err) => {
                    $('#preload').hide();
                });
            };

            $("#missedDate").change(function (e) {
                setDate = $(this).val();
                date = new Date(setDate);
                viewDate = date.getDate() + ' ' + nameMonth[date.getMonth()] + ' ' + date.getFullYear();
                $('#setDate').html(viewDate);
                getBawahan();
                getKinerja(nip);
            });

            $('#search').on('keyup', function (e) {
                e.preventDefault();
                key = $(this).val();
                $('.list-bawahan').hide();
                if (key) {
                    let result = storePegawai.response.filter((res) => {
                        if (res.nip.toLowerCase().indexOf(key.toLowerCase()) > -1 == true) {
                            return (res.nip.toLowerCase().indexOf(key.toLowerCase()) > -1);
                        } else {
                            return (res.nama.toLowerCase().indexOf(key.toLowerCase()) > -1);
                        }
                    });
                    loadData(result);
                } else {
                    loadData(storePegawai.response);
                }
            });

            window['trigger'] = 0;

            $(document).on('click', '.list-bawahan[data-index]', function (e) {
                e.preventDefault();
                $('.list-bawahan').find('.listSelect').removeClass('active');
                $(this).addClass('active').siblings().removeClass('active');
                $(this).find('.listSelect').addClass('active');
                var nama = $(this).attr('data-nama');
                nip = $(this).attr('data-nip');
                var foto = $(this).attr('data-foto');
                var index = $(this).attr('data-index');
                $('#date-rekap').val('');
                $('[name=index]').val(index);
                $('#detail-nama').html(nama);
                $('#detail-nip').html(nip);
                $('#detail-img').css({'background-image': 'url(' + foto + ')'});
                $("#boxReply").append(cacheDOM);
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
            });

            $('#pegawai-sebelumnya,#pegawai-selanjutnya').on('click', function (e) {
                e.preventDefault();
                var index = $('[name=index]').val();
                var i = $(this).attr('inc-index');
                var new_index = parseInt(index) + parseInt(i);
                var total_index = $('[name=total-index]').val();
                if (new_index => 0 && new_index <= total_index) {
                    $('[data-index="' + new_index + '"]').click()
                }
            });

            $(document).ready(function () {
                getBawahan();
                $('#setDate').html(viewDate);
            });
            $('#form-kepatuhan').on('submit',function (e) {
                e.preventDefault();
                var data = $(this).serialize()
                swal({
                    type: 'info',
                    title: 'Menyimpan Data',
                    text: 'Proses Menyimpan Data !',
                    showConfirmButton: false,
                })
                $.post('{{route('penilaian-kinerja.api.kepatuhan')}}',data)
                    .then(function(){
                        swal({
                            type: 'success',
                            title: 'Data Tersimpan',
                            text: 'Berhasil Menyimpan Data',
                        })
                    },function () {
                        swal({
                            type: 'danger',
                            title: 'Data Gagal Disimpan',
                            text: 'Proses Menyimpan Data Gagal !',
                        })
                    })
            })

            $(document).on('click', '.btn-approve', function () {
                $.ajax({
                    type: "POST",
                    url: "{{route('penilaian-kinerja.api.reply')}}",
                    data: 'type=' + $(this).data('action') + '&' + $("#formReply").serialize(),
                    success: function (data, xhr) {
                        if (xhr == 'success') {
                            $('#formReply').each(function () {
                                this.reset();
                                getBawahan();
                            });
                            swal({
                                type: 'success',
                                title: 'Berhasil',
                                text: 'Penilaian Kinerja berhasil ditambahkan!'
                            })
                        }
                    },
                    error: function (xhr) {
                        swal({
                            type: 'error',
                            title: 'Terjadi kesalahan',
                            text: 'Silahkan periksa formulir kembali!'
                        })
                    }
                });
            });
            $('.select-penilaian').on('change',function () {
                if (this.value == 'Penilaian SKP'){
                    $('#formReply').show();
                    $('#form-kepatuhan').hide();
                } else {
                    $('#formReply').hide();
                    $('#form-kepatuhan').show();
                }
            })
        </script>
    @endpush
@endsection
