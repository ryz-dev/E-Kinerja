@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
        <div class="main-content tab-content">
            <div class="container-fluid">
            {!! Form::model($checkinout, ['route' => ['checkinout.update', $checkinout->id] ]) !!}
  
              @include('layouts.admin.checkinout._form', ['btnText' => 'Simpan Perubahan'])

            {!! Form::close() !!}
            </div>
        </div>
    </div>
    @push('script')
        <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>

        <script>
            $('#datetimepicker').datetimepicker({ footer: true, modal: true, format: 'yyyy-dd-mm HH:MM:SS' });
        </script>
    @endpush
@endsection

{!! Form::close() !!}