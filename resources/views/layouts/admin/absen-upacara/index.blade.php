@extends('layouts.admin.partial.main')

@section('content')
<div class="main">
    <div class="nav-top-container">
        <div class="group-search">
        <span><i class="fas fa-search"></i></span>
        <input type="text" id='search' class="form-control" placeholder="Cari Mesin">
        </div>
        @include('layouts.admin.partial.part.logout')
    </div>
    <div class="main-content"">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-2">
                    <a href="#" id="add-new" class="btn btn-sm btn-success">Tambah Mesin</a>
                </div>
            </div>
        <div class="table-responsive">
            <table  class="table table-responsive table-pegawai">
            <thead>
                <tr>
                    <th scope="col" >Serial Number Mesin</th>
                    <th scope="col" >Action</th>
                </tr>
            </thead>
            <tbody class="list_mesin">
            </tbody>
            </table>
        </div>
        <nav style="margin-bottom:1em" aria-label="...">
            <ul class="pagination pagination-custome" id="pagination"></ul>
        </nav>
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
        $.get('{{route('api.web.master-data.absen-upacara.page')}}?q='+search)
            .then(function (res) {
                $('#pagination').twbsPagination({
                    totalPages: res.page,
                    visiblePages: 5,
                    onPageClick: function (event, page) {
                        getData(page,search);
                    }
                });
            })
    };
    $("#search").on('keyup',function(){
        getPage($(this).val());
    });
    var getData = function (page, search) {
        $("#preload").show();
        var row = '';
        var selector = $('.list_mesin');
        $.ajax({
            url: "{{ route('api.web.master-data.absen-upacara.list') }}?page="+page+(search?'&q='+search:''),
            data: '',
            success: function(res) {
                var data = res.response.map(function (val) {
                    var row = '';
                    row += "<tr>";
                    row += "<td><a >"+val.SN+"</a></td>";
                    row += "<td><button data-value="+val.SN+" data-uuid="+val.uuid+" class='btn btn-sm btn-info edit-mesin'><i class='fas fa-edit'></i></button><button type='button' data-uuid="+val.uuid+" class='btn btn-sm remove-mesin btn-danger btn-delete'><i class='fas fa-trash'></i></button></td>";
                    row += "</tr>";
                    return row;
                });
                selector.html(data.join(''));
                $("#preload").hide();
            }
        });
    }
    
    $("#search").on('keyup',function(){
        if ($(this).val()) {
            getPage($(this).val());
        }
        else{
            getPage('');
        }
    });

    $(document).on('click','#add-new', function(){
        Swal({
            title: 'Tambah data baru',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'on'
            },
            showCancelButton: true,
            confirmButtonText: 'Tambah',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                return !value && 'Masukkan nomor mesin absen!'
            },
            showLoaderOnConfirm: true,
            preConfirm: (sn) => {
                $.post('{{ route('api.web.master-data.absen-upacara.store') }}',{SN:sn})
                .done(res => {
                    swal({title:'Data berhasi tersimpan!',type:'success',showConfirmButton:false});
                    setTimeout(()=>{ location.reload() },1000);
                })
                .fail(function(xhr,status,error){
                    swal('Data gagal disimpan',xhr.responseJSON.errors.SN[0],'error');
                });
            }
        });
    });

    $(document).on('click','.edit-mesin',function(){
        var uuid = $(this).attr('data-uuid');
        var sn = $(this).attr('data-value');
        Swal({
            title: 'Edit nomor mesin absen',
            input: 'text',
            inputValue:sn,
            inputAttributes: {
                autocapitalize: 'on'
            },
            showCancelButton: true,
            confirmButtonText: 'Edit',
            cancelButtonText: 'Batal',
            inputValidator: (value) => {
                return !value && 'Masukkan nomor mesin absen!'
            },
            showLoaderOnConfirm: true,
            preConfirm: (value) => {
                $.post('{{ route('api.web.master-data.absen-upacara.update') }}',{SN:value,uuid:uuid})
                .done(res => {
                    swal({title:'Data berhasi terupdate!',type:'success',showConfirmButton:false});
                    setTimeout(()=>{ location.reload() },1000);
                })
                .fail(function(xhr,status,error){
                    swal('Data gagal diupdate',xhr.responseJSON.errors.SN[0],'error');
                });
            }
        });
    });

    $(document).on('click', '.remove-mesin', function(){
        var uuid = $(this).attr('data-uuid');
        var formData = new FormData();
        formData.append('uuid', uuid);
            swal({
                title: 'Yakin menghapus data?',
                type:'warning',
                showCancelButton: true,
                cancelButtonText: 'Batalkan',
                confirmButtonText: 'Ya, hapus data'
            }).then(function(result){
                if (result.value) {
                    $.ajax({
                        url:"{{ route('api.web.master-data.absen-upacara.delete') }}",
                        type:'POST',
                        data: formData,
                        success: function(res){
                            if (res.diagnostic.code ==200) {
                                swal({title:'Berhasi menghapus data!',type:'success',showConfirmButton:false});
                                setTimeout(function(){
                                    location.reload();
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
        });
</script>
@endpush
@endsection
