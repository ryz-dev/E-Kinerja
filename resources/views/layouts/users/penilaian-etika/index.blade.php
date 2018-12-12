@extends('layouts.users.partial.main')
@section('class','penilaian-etika')
@section('content')

<div class="main">
    <div class="nav-top-container">
        <div class="nav-top">
            <div class="title-nav">
                <h4 class="mr-4">Penilaian Etika</h4>
                <span class="badge text-white">{{ date('d F Y') }}</span>
            </div>
            <div class="img-profile" id="user-profile" style="background-image: url('assets/images/img-user.png');">
            </div>
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
            <ul class="nav nav-pills" id="tabPegawai" role="tablist">
            </ul>
        </div>
    </div>
    <!-- isi tab pane -->
    <div class="main-content tab-content">
            @include('layouts.users.penilaian-etika.tab-penilaian')
    </div>
</div>\
@push('script')
    <script>
        window.pegawai = [];

        $(document).ready(function(){
            listPegawai();
        });

        $("#search").on('keyup', function(){
            var searchkey = $(this).val().toUpperCase();
            if (searchkey == 0) {
                $("#tabPegawai li").show();
                $(".nav-pegawai").attr('disabled', false);
            }
            if (searchkey.length > 2) {
                window.pegawai.forEach(function(data,index){
                    // console.log(data.nama.indexOf(searchkey));
                    if (data.nama.toUpperCase().indexOf(searchkey) > -1 || data.nip.toUpperCase().indexOf(searchkey) > -1) {
                        $(".nav-pegawai").attr('disabled', true);
                        $("#tabPegawai li:nth-child("+(index+1)+")").show();
                    }
                    else{
                        $(".nav-pegawai").attr('disabled', true);
                        $("#tabPegawai li:nth-child("+(index+1)+")").hide();
                    }
                });
            }
        });

        $("form").on('submit', function(e){
            e.preventDefault();

            var formData = new FormData();
            var nip_pegawai = $('.nip-pegawai').text();
            formData.append('nip',nip_pegawai);
            formData.append('persentase',parseInt($("output[name='thisRate']").val()));
            formData.append('mengikuti_upacara',parseInt($("#rate_upacara").val()));
            formData.append('perilaku_kerja',parseInt($("#rate_perilaku_kerja").val()));
            formData.append('kegiatan_kebersamaan',parseInt($("#rate_kegiatan_kebersamaan").val()));
            formData.append('keterangan',$("#keterangan-etika").val());
            formData.append('_token','{{ csrf_token() }}')
            var action = this.action;

            swal({
                    title: 'Ingin Menyimpan Data?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Iya, simpan data!',
                    cancelButtonText: 'Batalkan'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: "{{ route('api.web.penilaian-etika.store.penilaian') }}",
                            type: "POST",
                            data: formData,
                            success: function (res) {
                                swal('Berhasil Menyimpan Data!','','success');
                                updateList(nip_pegawai,res.response);
                                $("#simpan-penilaian").prop('disabled',true);

                            },
                            error: function () {
                                swal('Gagal Menyimpan Data!','','error')
                            },
                            cache: false,
                            contentType: false,
                            processData: false
                        });
                    }
                })
        });

        var listPegawai = function(){
            $.get("{{ route('api.web.penilaian-etika.get-pegawai') }}").then(function(res){
                if(res.response.length > 0){
                    var output='';
                    window.pegawai = res.response;
                    viewNavPegawai(0,window.pegawai.length);
                    res.response.map( function(data,i){
                        output += viewListPegawai(data,i);
                    });
                    $("#tabPegawai").html(output);
                    showFormPegawai(res.response[0]);
                }
            });
        }

        $(document).on('click', '.listItems', function(){
            var selectedNip = $(this).attr('data-nip');
            window.pegawai.forEach(function(data,i){
                if (data.nip == selectedNip) {
                    viewNavPegawai(i,window.pegawai.length);
                    showFormPegawai(data);
                }
            });
        });

        function updateList(nip, response){
            window.pegawai.forEach(function(data,index){
                if (data.nip == nip) {
                    window.pegawai[index].etika[0] = {
                        'persentase' : response.persentase,
                        'keterangan' : response.keterangan,
                        'nip' : response.nip
                    };

                    $('#tabPegawai li:nth-child('+(index+1)+')').slideUp().remove();
                    if (index==0) {
                        $('#tabPegawai li:nth-child('+(index+1)+')').before(viewListPegawai(data,index)).slideDown();
                    }
                    else{
                        $('#tabPegawai li:nth-child('+(index)+')').after(viewListPegawai(data,index)).slideDown();
                    }
                    console.log($("#tabPegawai li:nth-child(0)"));
                }
            });
        }

        function navPegawaiButton(el){
            var index = $(el).attr('data-pegawai-index');
            viewNavPegawai(index,window.pegawai.length);
            showFormPegawai(window.pegawai[index]);
            $(".listItems").removeClass('active');
            $("[data-nip='"+window.pegawai[index].nip+"']").addClass('active');
        }

        function showFormPegawai(data){
            var rate=$("output[name='thisRate']");
            var mengikuti_upacara = $("#rate_upacara");
            var perilaku_kerja = $("#rate_perilaku_kerja");
            var kegiatan_kebersamaan = $("#rate_kegiatan_kebersamaan");
            var keterangan=$("#keterangan-etika");
            var buttonSimpan = $("#simpan-penilaian");
            $(".nama-pegawai").text(data.nama);
            $(".nip-pegawai").text(data.nip);
            
            if (data.etika.length > 0) {
                rate.val(data.etika[0].persentase);
                mengikuti_upacara.val(data.etika[0].mengikuti_upacara);
                perilaku_kerja.val(data.etika[0].perilaku_kerja);
                kegiatan_kebersamaan.val(data.etika[0].kegiatan_kebersamaan);
                keterangan.val(data.etika[0].keterangan);
                buttonSimpan.prop('disabled', true);
            }
            else{
                rate.val(0);
                keterangan.val('');
                mengikuti_upacara.val(0);
                perilaku_kerja.val(0);
                kegiatan_kebersamaan.val(0);
                buttonSimpan.prop('disabled', false); 
            }

            mengikuti_upacara.trigger('oninput');
            mengikuti_upacara.trigger('onchange');
            perilaku_kerja.trigger('oninput');
            kegiatan_kebersamaan.trigger('oninput');
        }

        function updateKeterangan()
        {
            var total = parseInt($("output[name='thisRate']").val());
            var selector = $(".ket");
            if (total < 25) {selector.text('Buruk')};
            if (total >= 25) {selector.text('Cukup Baik')};
            if (total >= 50) {selector.text('Baik')};
            if (total >= 75) {selector.text('Sangat Baik')};
        }

        function viewListPegawai(data, key){
            var output='';
            output += "<li><a class='listItems "+(key==0?'active':'')+"' data-toggle='tab' data-nip="+data.nip+" href='#"+data.nip+"' role='tab' aria-selected='true'>"
            output += "<span class='img-user' id='img-user1' style='background-image: url(assets/images/img-user.png');'></span>";
            output += "<span style='width:max-content'><label>"+data.nama+" <br><small>"+data.nip+"</small></label></span>";
            if(data.etika.length > 0){
                output += viewBadgeEtika(data.etika[0].persentase);
            }
            output += "</a></li>";
            return output;
        }

        function viewBadgeEtika(data){
            if(data > 75) {
                return "<div class='float-right badge badge-blue text-white mr-2'>"+data+" %</div>"
            }
            if(data > 45) {
                return "<div class='float-right badge badge-green text-white mr-2'>"+data+" %</div>"
            }
            if(data > 15) {
                return "<div class='float-right badge badge-orange text-white mr-2'>"+data+" %</div>"
            }

            return   "<div class='float-right badge badge-red text-white mr-2'>"+data+" %</div>"
            
        }

        function viewNavPegawai(index,size){
            $(".prevItem").attr('data-pegawai-index',function(){
                if ( (parseInt(index)-1)>=0 ) {
                    if ($("#search").val().length == 0) {
                        $(this).addClass('active').prop('disabled',false);
                    }
                    return  parseInt(index)-1;
                }
                else{
                    $(this).removeClass('active').prop('disabled',true);
                }
            });
            $(".nextItem").attr('data-pegawai-index',function(){
                if ( (parseInt(index)+1) < size ) {
                    if ($("#search").val().length == 0) {
                        $(this).addClass('active').prop('disabled',false);
                    }
                    return  parseInt(index)+1;
                }
                else{
                    $(this).removeClass('active').prop('disabled',true);
                }
            });
        }
    </script>
@endpush
@endsection
