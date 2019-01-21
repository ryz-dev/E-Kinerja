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
            <input id="datetimepicker" name="checktime" value="{{ !empty($checkinout) ? $checkinout->checktime : "" }}" required/>
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
            {{ Form::number('checktype', null, ['class'=>'form-control', 'required' => '']) }}
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
