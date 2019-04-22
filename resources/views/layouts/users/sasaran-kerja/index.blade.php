@extends('layouts.users.partial.main')
@section('class','input-skp')
@section('content')
<div class="main">
    <div class="nav-top-container">
        <div class="nav-top">
            <div class="title-nav float-left">
                <h4 class="mr-3 float-left">Sasaran Kerja Pegawai</h4>
                <span class="badge text-white">{{ucfirst(\App\Models\MasterData\Hari::find(date('N'))->nama_hari)}}, {{date('d')}} {{ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan)}} {{date('Y')}}</span>
            </div>

            <div class="img-profile" id="user-profile" style="background-image: url('assets/images/img-user.png');">
            </div>

        </div>
        @include('layouts.users.partial.part.logout')
    </div>
    <div class="main-content">
        <div class="container-fluid">
            <div class="row">
                @if (Session::has('message'))
                <div class="col-md-12">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <p>{{ Session::get('message') }}</p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                </div>
                @endif
                <div class="col-md-12">
                    <div class="col-md-12 mt-3 control-date-btn">
                        <div class="select-group">
                            <div class="date-group">
                                <span class="icon-date"><i class="material-icons">event</i></span>
                                <input id="date-rekap" class="datepicker" placeholder="Pilih Periode SKP" />
                            </div>
                        </div>

                        <div class="add-skp" style="display:none">
                            <a href="tambah-skp.html">
                                <button class="btn-style primary pt-2 pb-2"><i class="material-icons">playlist_add</i>Tambah SKP</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive table-pegawai table-hover">
                        <table class="table">
                            <thead>
                                <tr>
                                <th scope="col"></th>
                                <th scope="col">NIP PEGAWAI</th>
                                <th scope="col">NAMA PEGAWAI</th>
                                <th scope="col" class="text-center">JUMLAH SKP</th>
                                <th scope="col" class="text-center">EDIT SKP</th>
                                </tr>
                            </thead>
                            <tbody class="list_pegawai" style="font-size: 1.1em;">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
<script>
    $(document).ready(function(){
        var dates = new Date(); 
        $('.datepicker').datepicker('setDate', dates);
        getData(dates.getFullYear()+'-'+(dates.getMonth()+1)+'-01');
    });

    $('.datepicker').on('change', function(){
        getData($(this).val());
    })

    var actionButton = function(flag, uuid, periode){
        if (flag) {
            return "<div class='flex'><a href='{{ route('sasaran-kerja.edit') }}/?uuid="+uuid+"&periode="+periode+"'>" +
                    '<button class="btn-style warning mr-2">' +
                        '<i class="material-icons">edit</i> Edit '+
                    '</button>' +
                '</a>' +
                "<a href='#' onclick=hapus('"+uuid+"','"+periode+"')><button class='btn-style danger'>" +
                '    <i class="material-icons">delete</i> ' +
                '    Hapus' +
                '</button></a></div>' ;
        }
        else{
            return '<div class="add-skp">' +
                "<a href='{{ route('sasaran-kerja.add') }}/?uuid="+uuid+"&periode="+periode+"'>" +
                '    <button class="btn-style primary"><i class="material-icons">playlist_add</i>Tambah SKP</button>' +
                '</a>' +
                '</div>';
        }
    }

    var  getData = function(date) {
        var selector = $('.list_pegawai');
        $('#preload').show();
        $.ajax({
            url: "{{ route('sasaran-kerja.api.index') }}?d="+date,
            data: '',
            success: function(res) {
                
                if (res.response.length > 0) {
                    
                    var data = res.response.map(function (val) { 
                        let row = '';
                        let foto = val.foto ? "{{url('')}}/storage/" + val.foto : "{{url('assets/images/img-user.png')}}"
                        row += "<tr data-nip='"+val.nip+"' >";
                        row += "<td><div class='img-user' id='user1' style='background-image: url(" + foto + ");'></div></td>";
                        row += "<td><a href=''>" + val.nip + "</a></td>";
                        row += "<td>" + val.nama +"</td>";
                        row += "<td class='text-center'>"+ val.jumlah_skp +"</td>";
                        row += "<td>"+ actionButton(val.f_edit, val.uuid, val.periode) +"</td>";
                        row += "</tr>";
                        return row;
                    })
                    selector.html(data.join(''));
                } else {
                    selector.html('<tr style="text-align: center"><td colspan="100">Data Tidak Ditemukan</td></tr>')
                }
            },
            complete: function () {
                $('#preload').hide();
            }
        });
    }

    var hapus = function (pegawai, periode){
        console.log('sgfddgdf');
        var formData = new FormData();
        formData.append('uuid', pegawai);
        formData.append('periode', periode);
        swal({
            title: 'Yakin menghapus data?',
            type:'warning',
            showCancelButton: true,
            cancelButtonText: 'Batalkan',
            confirmButtonText: 'Ya, hapus data'
        }).then(function(result){
            if (result.value) {
                $.ajax({
                    url:"{{ route('sasaran-kerja.api.delete') }}",
                    type:'POST',
                    data: formData,
                    success: function(res){
                        console.log(res);
                        if (res.response) {
                            swal('Berhasil Menghapus Data!','','success');
                            setTimeout(function(){
                                location.href = "{{ route('sasaran-kerja.index') }}"
                            },1000);
                        }
                        else{
                            swal('Gagal Menghapus Data!',res.diagnostic.message,'error')
                        }
                    },
                    error: function () {
                        swal('Gagal menghapus Data!','','error')
                    },
                    cache: false,
                    contentType: false,
                    processData: false
                });
            }
        });
    }
</script>
@endpush
@endsection