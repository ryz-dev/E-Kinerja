@extends('layouts.admin.partial.main')

@section('content')
<div class="main">
    <div class="nav-top-container">
        <div class="group-search">
        <span><i class="fas fa-search"></i></span>
        <input type="text" id='search' class="form-control" placeholder="Cari Pegawai">
        </div>
        @include('layouts.admin.partial.part.logout')
    </div>
    <div class="main-content" id="wrapBox">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        {{-- <div class="loading">
        <img src="{{ asset('assets/images/loading.gif') }}" alt="loading">
        </div> --}}
        <div class="container-fluid">
        <div class="table-responsive">
            <table class="table table-responsive table-pegawai">
            <thead>
                <tr>
                    <th></th>
                    <th scope="col">Nip</th>
                    <th scope="col">Nama Pegawai</th>
                    <th scope="col" width="50%">Jabatan</th>
                    <th scope="col">Role Pegawai</th>
                    <th scope="col" width="20%">Aksi</th>
                </tr>
            </thead>
            <tbody class="list_role">
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
    window.roles = [];
    $(document).ready(function(){
        getPage();
        getRoles();
    });
    var getPage = function (search) {
        $("#preload").show();
        $('#pagination').twbsPagination('destroy');
        $.get('{{route('api.web.master-data.page.role.pegawai')}}?q='+search)
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
    var getData = function (page, search) {
        var row = '';
        var selector = $('.list_role');
        $.ajax({
            url: "{{ route('api.web.master-data.list.role') }}?page="+page+(search?'&q='+search:''),
            data: '',
            success: function(res) {
                var data = res.response.map(function (val) {
                    var row = '';
                    var foto = val.foto ? "{{url('')}}/storage/"+val.foto : "{{url('assets/images/img-user.png')}}";
                    var role = '';
                    if (val.role.length > 0) {
                        var role ="<span class='badge badge-table badge-red'>";
                        role += val.role.map(function(current,index){
                            return current.nama_role;
                            
                        });
                        role +="</span>";
                    }
                    else{
                        role = "<span class='badge badge-table badge-green'>User</span>";
                    }
                    // length>0?"<span class='badge badge-table badge-red'>"+val.role[0].nama_role+"</span>":;
                    var action = function(role){
                        var button = '<div style="display:inline-flex;">';
                        if (role.role.length > 0){    
                            button +="<button type='button' data-nip="+val.nip+" data-role-id="+role.role[0].id+" class='btn btn-sm remove-role-pegawai btn-danger btn-delete'><i class='fas fa-trash'></i></button>";
                            button +="<button data-nip="+val.nip+" class='btn btn-sm btn-info add-role-pegawai'><i class='fas fa-plus'></i></button>";
                        }else{
                            button +="<button data-nip="+val.nip+" class='btn btn-sm btn-info add-role-pegawai'><i class='fas fa-plus'></i></button>";
                        }
                        button+="</div>";

                        return button;
                    }
                    row += "<tr>";
                    row += "<td><div class='img-user' id='user1' style='background-image: url("+foto+");'></div></td>";
                    row += "<td><a href='"+val.detail_uri+"'>"+val.nip+"</a></td>";
                    row += "<td>"+val.nama+"</td>";
                    row += "<td>"+ ( val.jabatan ? val.jabatan.jabatan : '')+"</td>";
                    row += "<td>"+role+"</td>";
                    row += "<td>"+action(val)+"</td>";
                    row += "</tr>";
                    return row;
                });
                selector.html(data.join(''));
                $("#preload").hide();
            }
        });
    }
    var getRoles = function(){
        $.get('{{route('api.web.master-data.role.get')}}')
            .then(function(res){
                if (res.response) {
                    window.roles = res.response;
                }
            });
    }

    $(document).on('click', '.add-role-pegawai', function(){
        var nip = $(this).attr('data-nip');

        swal({
            title: 'Silakan pilih role',
            input: 'select',
            inputOptions: window.roles,
            inputPlaceholder:'Pilih Role',
            showCancelButton: true
        }).then(function(result){
            if (result.value) {
                var formData = new FormData();
                formData.append('nip',nip);
                formData.append('role',result.value);
                $.ajax({
                    url:"{{ route('api.web.master-data.role.store') }}",
                    type:'POST',
                    data: formData,
                    success: function(res){
                        if (res.diagnostic.code ==200) {
                            swal('Berhasil Menyimpan Data!','','success')
                            setTimeout(function(){
                                location.href = "{{ route('role-pegawai.index') }}"
                            },2000);
                        }
                        else{
                            swal('Gagal Menyimpan Data!',res.diagnostic.message,'error')
                        }
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
    $("#search").on('keyup',function(){
        // console.log('halo');
        getPage($(this).val());
    });
    $(document).on('click', '.remove-role-pegawai', function(){
        var role_id = $(this).attr('data-role-id');
        var nip = $(this).attr('data-nip');
        var formData = new FormData();
        formData.append('role_id', role_id);
        formData.append('nip', nip);
            swal({
                title: 'Yakin menghapus data?',
                type:'warning',
                showCancelButton: true,
                cancelButtonText: 'Batalkan',
                confirmButtonText: 'Ya, hapus data'
            }).then(function(result){
                if (result.value) {
                    $.ajax({
                        url:"{{ route('api.web.master-data.role.delete') }}",
                        type:'POST',
                        data: formData,
                        success: function(res){
                            if (res.diagnostic.code ==200) {
                                swal('Berhasil Menghapus Data!','','success');
                                setTimeout(function(){
                                    location.href = "{{ route('role-pegawai.index') }}"
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
