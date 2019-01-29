@extends('layouts.admin.partial.main')
@section('content')
  <div class="main">
      <div class="nav-top-container">
          @include('layouts.admin.partial.part.logout')
      </div>
    <div class="main-content tab-content">
      <div class="container-fluid">
        {{ Form::open(array('route' => array('hari_kerja_update',$harikerja->id),'id'=>'formHariKerja','class'=>'form')) }}
          <div class="row">
            <div class="col-md-6">
              @if(Session::has('message'))
                <div class="alert alert-danger" role="alert">
                  {{Session::get('message')}}
                </div>
              @endif
              <h2 class="mb-2">Tambah Hari Kerja</h2>
              <div class="form-group">
                <label for="id_status_hari">Status Hari</label>
                <select class="form-control" name="id_status_hari" id="id_status_hari" required>
                  @foreach ($status as $key => $sts)
                    @if ($harikerja->id_status_hari == $sts->id)
                        <option value="{{ $sts->id }}" selected>{{ $sts->status_hari }}</option>
                      @else
                        <option value="{{ $sts->id }}">{{ $sts->status_hari }}</option>
                    @endif
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <input id="tanggal" name="tanggal" type="date" class="form-control" placeholder="Tanggal Lahir" value="{{ $harikerja->tanggal }}" required>
              </div>
              <div class="form-group">
                <button type="submit" class="btn btn-primary">Simpan</button>
              </div>
            </div>
          </div>
        {{ Form::close() }}
      </div>
    </div>
  </div>
@endsection
