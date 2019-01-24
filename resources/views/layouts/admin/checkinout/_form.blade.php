<div class="row">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="col-md-12">
        @if ($errors->any())
                {{-- {{ $errors->all() }} --}}
        @endif
        <h2 class="mb-2">Tambah Absensi</h2>
        <div class="form-group">
            <label for="nip">NIP</label>
            {{ Form::number('nip', null, ['class'=>'form-control', 'required' => '']) }}
        </div>
        <div class='form-group'>
            <label for="datetimepicker">Masukkan Tgl dan jam kehadiaran</label>
            <input class="me" id="datetimepicker" disabled="" placeholder="Klik icon di seblah" name="checktime" value="{{ !empty($checkinout) ? $checkinout->checktime : "" }}" required/>
        </div>
        <div class="form-group">
            <label for="verifycode">Verify Code</label>
            {{ Form::number('verifycode', null, ['class'=>'form-control', 'required' => '']) }}
        </div>
        <div class="form-group">
            <label for="sensorid">Sensor ID</label>
            {{ Form::number('sensorid', null, ['class'=>'form-control', 'required' => '']) }}
        </div>
        <div class="form-group">
            <label for="checktype">Check Type</label>
            {{ Form::select('checktype', [0=>'Absen Masuk', 1=>'Absen Pulang' ],!empty($checkinout) ?$checkinout->checktype:"", ['class'=>'form-control', 'required' => '']) }}
        </div>
        <div class="form-group">
            <label for="workcode">Work Code</label>
            {{ Form::number('workcode', null, ['class'=>'form-control', 'required' => '']) }}
        </div>
        <div class="form-group">
            <label for="sn">SN</label>
            {{ Form::number('sn', null, ['class'=>'form-control', 'required' => '']) }}
        </div>
    </div>
    <div class="col-md-12 right">
        <div class="form-group">
            <button type="submit" class="btn btn-primary">{{ $btnText }}</button>
        </div>
    </div>
</div>
