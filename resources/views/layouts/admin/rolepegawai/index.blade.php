@extends('layouts.admin.partial.main')

@section('content')
<div class="main">
    <div class="nav-top-container">
        <div class="group-search">
        <span><i class="fas fa-search"></i></span>
        <input type="text" id='search' class="datepicker form-control" placeholder="Cari Pegawai">
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
            <table class="table table-hari-kerja">
            <thead>
                <tr>
                    <th></th>
                    <th scope="col">Nip</th>
                    <th scope="col">Nama Pegawai</th>
                    <th scope="col">Jabatan</th>
                    <th scope="col">Role Pegawai</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody class="list_role">
            </tbody>
            </table>
        </div>
        <div class="box-pagination">
            <ul class="pagination pagination-custome" id="pagination"></ul>
        </div>
        </div>
    </div>
</div>
@push('script')
<script>
    $(document).ready(function(){
        getPage();
    });
    var getPage = function () {
        $('#pagination').twbsPagination('destroy');
        $.get('{{route('api.web.master-data.page.role.pegawai')}}')
            .then(function (res) {
                $('#pagination').twbsPagination({
                    totalPages: res.page,
                    visiblePages: 5,
                    onPageClick: function (event, page) {
                        getData(page);
                    }
                });
            })
    };
    var getData = function (page) {
        var row = '';
        var selector = $('.list_role');
        $.ajax({
            url: "{{ route('api.web.master-data.list.role') }}?page="+page,
            data: '',
            success: function(res) {
                var data = res.response.map(function (val) {
                    console.log(val.role);
                    var row = '';
                    var foto = val.foto ? "{{url('')}}/storage/"+val.foto : "{{url('assets/images/img-user.png')}}";
                    var role = val.role.length>0?"<span class='badge badge-table badge-red'>"+val.role[0].nama_role+"</span>":"<span class='badge badge-table badge-green'>User</span>";
                    var action = function(role){
                        if (role.role.length > 0){
                            switch(role.role[0].id){
                                case 2 :
                                    return "<button type='button' delete-uri='' class='btn btn-danger btn-delete'><i class='fas fa-trash'></i></button>";
                                    break;
                                default:
                                    return "";
                                    break;
                            }
                        }else{
                            return "<a class='btn btn-info'><i class='fas fa-plus'></i></a>";
                        }
                    }
                    row += "<tr>";
                    row += "<td><div class='img-user' id='user1' style='background-image: url("+foto+");'></div></td>";
                    row += "<td><a href='"+val.detail_uri+"'>"+val.nip+"</a></td>";
                    row += "<td>"+val.nama+"</td>";
                    row += "<td>"+val.jabatan.jabatan+"</td>";
                    row += "<td>"+role+"</td>";
                    row += "<td>"+action(val)+"</td>";
                    row += "</tr>";
                    return row;
                });
                selector.html(data.join(''));
            }
        });
    }
</script>
@endpush
@endsection
