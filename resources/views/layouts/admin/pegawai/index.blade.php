@extends('layouts.partial.main')
@section('content')
  <div class="main">
      <div class="nav-top-container">
          <div class="group-search">
              <span><i class="fas fa-search"></i></span>
              <input type="text" class="form-control" placeholder="Cari Nama / NIP Pegawai">
          </div>
          @include('layouts.partial.part.logout')
      </div>
    <div class="main-content">
        <div class="container-fluid">
          <table class="table table-responsive table-pegawai">
              <thead>
                <tr>
                  <th scope="col"></th>
                  <th scope="col">NIP Pegawai</th>
                  <th scope="col">Nama Pegawai</th>
                  <th scope="col">Jabatan</th>
                  <th scope="col">Jenis Kelamin</th>
                  <th scope="col">Aksi</th>
                </tr>
              </thead>
              <tbody class="list_pegawai">
              </tbody>
          </table>
        </div>
    </div>
  </div>
@endsection
