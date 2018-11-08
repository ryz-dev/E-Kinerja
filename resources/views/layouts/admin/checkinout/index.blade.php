@extends('layouts.admin.partial.main')
@section('content')
  <div class="main">
    <div class="nav-top-container">
        <div class="group-search">
            <span><i class="fas fa-search"></i></span>
            <input id="search" type="text" class="form-control" placeholder="Cari Nama / NIP Pegawai">
        </div>
        @include('layouts.admin.partial.part.logout')
    </div>
    <div class="main-content">
        {{-- <div class="loading">
            <img src="{{ asset('assets/images/loading.gif') }}" alt="loading">
        </div> --}}
        <div class="container-fluid">
          <a href="{{route('pegawai.add')}}" class="btn btn-success">Tambah Pegawai</a>
            <div class="table-responsive">
                <table class="table table-pegawai">
                    <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">UserID</th>
                        <th scope="col">Waktu Absen</th>
                        <th scope="col">Tipe Absen</th>
                        <th scope="col">Verify Code</th>
                        <th scope="col">Sensor ID</th>
                        <th scope="col">Work Code</th>
                        <th scope="col">SN</th>
                        <th scope="col">Aksi</th>
                    </tr>
                    </thead>
                    <tbody class="list_pegawai" id="data_checkinout">
                    </tbody>
                </table>
                <div class="box-pagination">
                    <ul class="pagination" id="pagination"></ul>
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
	        $.get('{{route('api_index_checkinout')}}')
	        .then(function (res) {
	        	let row;
	        	if (res.data.length > 0) {
	        		for (var i = 0; i < res.data.length; i++) {
	        			row += res.data[i].userid;
	        			row += "<tr id='_id_"+res.data[i].id+"'>";
		                row += "<td>"+(i+1)+"</td>";
		                row += "<td>"+res.data[i].userid+"</td>";
		                row += "<td>"+moment(res.data[i].checktime).fromNow()+"</td>";
		                row += "<td>"+res.data[i].checktype+"</td>";
		                row += "<td>"+res.data[i].verifycode+"</td>";
		                row += "<td>"+res.data[i].sensorid+"</td>";
		                row += "<td>"+res.data[i].workcode+"</td>";
		                row += "<td>"+res.data[i].sn+"</td>";
		                row += "<td><div class='btn-group mr-2' role='group' aria-label='Button Action'><a href='"+res.data[i].id+"' class='btn btn-success'><i class='fas fa-edit'></i></a><button type='button' data-id='"+res.data[i].id+"' class='deleteData btn btn-danger'><i class='fas fa-trash'></i></button></div></td>";
                		row += "</tr>";
	        		}
	        	}else{
	        		row += "<tr class='text-center'>";
		            row += "<td colspan='9'>Tidak ada data</td>";
		            row += "</tr>";
	        	}
	        	$("#data_checkinout").html(row);
	        })
  		});
  	</script>
  @endpush
   
@endsection
