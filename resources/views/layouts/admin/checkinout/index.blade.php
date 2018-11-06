@extends('layouts.admin.partial.main')

@section('content')
  <div class="main">
      <div class="nav-top-container">
          <div class="group-search">
              <span><i class="fas fa-search"></i></span>
              <input type="text" class="form-control" placeholder="Cari Nama / NIP Pegawai">
          </div>
          @include('layouts.admin.partial.part.logout')
      </div>
    <div class="main-content">
        <div class="container-fluid">
            <a href="{{route('pegawai.add')}}" class="btn btn-success">Tambah Data Absen</a>
          <table class="table table-responsive table-pegawai">
              <thead>
                <tr>
                  <th scope="col">user id</th>
                  <th scope="col">Waktu Ceklok</th>
                  <th scope="col">Tipe Ceklok</th>
                  <th scope="col">ID Sensor</th>
                  <th scope="col">Work Code</th>
                  <th scope="col">SN</th>
                  <th scope="col">Aksi</th>
                </tr>
              </thead>
              <tbody class="list_pegawai">
              	<tr>
              		<td>19229829</td>
              		<td>{{ Carbon\Carbon::now() }}</td>
              		<td>Masuk</td>
              		<td>10</td>
              		<td>0</td>
              		<td>0</td>
              		<td><div class='btn-group mr-2' role='group' aria-label='Edit'><a href='"+res.response.data[i].edit_uri+"' class='btn btn-success'><i class='fas fa-edit'></i></a><button type='button' delete-uri='"+res.response.data[i].delete_uri+"' class='btn btn-danger btn-delete'><i class='fas fa-trash'></i></button></div></td>
              	</tr>
              </tbody>
          </table>
          <div class="box-pagination">
            <ul class="pagination" id="pagination"></ul>
          </div>
        </div>
    </div>
  </div>