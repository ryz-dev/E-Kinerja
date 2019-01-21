@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
        <div class="main-content tab-content">
            <div class="container-fluid">
            {!! Form::open(['url' => route('checkinout.store'), 'id' => 'form-store-eselon', 'class' => 'from']) !!}
  
              @include('layouts.admin.checkinout._form', ['btnText' => 'Simpan Data Baru'])

            {!! Form::close() !!}
            </div>
        </div>
    </div>
    @push('script')
        <script src="https://cdn.jsdelivr.net/npm/gijgo@1.9.10/js/gijgo.min.js" type="text/javascript"></script>

        <script>
            $('#datetimepicker').datetimepicker({ footer: true, modal: true, format: 'yyyy-mm-dd HH:MM:SS' });
        </script>
    @endpush
@endsection
