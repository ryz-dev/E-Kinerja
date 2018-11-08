@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
        <div class="main-content tab-content">
            <div class="container-fluid">
                <form id="form-store-eselon" method="POST" action="{{route('checkinout.store')}}" class="form">
                    <div class="row">
                        <div class="col-md-12">
                            @if ($errors->any())
                                    {{ $errors->all() }}
                            @endif
                            <h2 class="mb-2">Tambah Absensi</h2>
                            <div class="form-group">
                                <label for="userid">UserID</label>
                                <input id="userid" type="number" class="form-control" name="userid" placeholder="userid" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="checktime">Check Date</label>
                                <input class="form-control" name="checktime" placeholder="Checktimel" id="datetimepicker" />
                            </div>
                            <div class="form-group">
                                <label for="verifycode">Verify Code</label>
                                <input id="verifycode" type="number" class="form-control" name="verifycode" placeholder="verify code" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="sensorid">Sensor ID</label>
                                <input id="sensorid" type="number" class="form-control" name="sensorid" placeholder="Sensor ID" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="checktype">Check Type</label>
                                <input id="checktype" type="number" class="form-control" name="checktype" placeholder="Check Type" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="workcode">Work Code</label>
                                <input id="workcode" type="number" class="form-control" name="workcode" placeholder="Work Code" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="sn">SN</label>
                                <input id="sn" type="number" class="form-control" name="sn" placeholder="Serial Number" value="" required>
                            </div>
                        </div>
                        <div class="col-md-12 right">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('script')
        <script>
            $('#datetimepicker').datetimepicker({
                // locale: 'ru'
            });
            // $('#form-store-eselon').on('submit',function (e) {
            //     e.preventDefault();
            //     var action = this.action;
            //     var formData = new FormData($(this)[0]);
            //     swal({
            //         title: 'Ingin Menyimpan Data?',
            //         text: "",
            //         type: 'warning',
            //         showCancelButton: true,
            //         confirmButtonText: 'Iya, simpan data!',
            //         cancelButtonText: 'Batalakan'
            //     }).then((result) => {
            //         if (result.value) {
            //             $.ajax({
            //                 url: action,
            //                 type: "POST",
            //                 data: formData,
            //                 success: function (res) {
            //                     swal(
            //                         'Berhasil Menyimpan Data!',
            //                         '',
            //                         'success'
            //                     )
            //                     setTimeout(function () {
            //                         location.href = res.response.detail_uri
            //                     }, 3000);
            //                 },
            //                 error: function () {
            //                     swal(
            //                         'Gagal Menyimpan Data!',
            //                         '',
            //                         'error'
            //                     )
            //                 },
            //                 cache: false,
            //                 contentType: false,
            //                 processData: false
            //             });
            //         }
            //     })
            // })
        </script>
    @endpush
@endsection
