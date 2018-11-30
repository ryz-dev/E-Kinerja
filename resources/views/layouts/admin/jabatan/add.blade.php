@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
        <div class="main-content tab-content">
            <div class="container-fluid">
                <form id="form-store-jabatan" action="{{route('api.web.master-data.jabatan.store')}}" class="form">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="mb-2">Tambah Jabatan</h2>
                            <div class="form-group">
                                <label for="jabatan">Jabatan</label>
                                <input id="jabatan" type="text" class="form-control" name="jabatan" placeholder="Jabatan" value="" required>
                            </div>
                            <div class="form-group">
                                <label for="eselon">Kelas Jabatan</label>
                                <select class="form-control" name="id_eselon" id="eselon" required>
                                    <option value="">Pilih Kelas Jabatan</option>
                                    @foreach($data_option->eselon AS $ag)
                                        <option value="{{$ag->id}}">{{$ag->eselon}} (Rp.{{$ag->tunjangan_rp}})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jabatan">Atasan</label>
                                <select class="form-control" name="id_atasan" id="jabatan">
                                    <option value="">Pilih Atasan</option>
                                    @foreach($data_option->jabatan AS $jb)
                                        <option value="{{$jb->id}}">{{$jb->jabatan}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="keterangan">Keterangan Jabatan</label>
                                <textarea name="keterangan" id="keterangan" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
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
            $('#form-store-jabatan').on('submit',function (e) {
                e.preventDefault();
                var action = this.action;
                var formData = new FormData($(this)[0]);
                swal({
                    title: 'Ingin Menyimpan Data?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Iya, simpan data!',
                    cancelButtonText: 'Batalakan'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: action,
                            type: "POST",
                            data: formData,
                            success: function (res) {
                                swal(
                                    'Berhasil Menyimpan Data!',
                                    '',
                                    'success'
                                )
                                setTimeout(function () {
                                    location.href = res.response.detail_uri
                                }, 3000);
                            },
                            error: function () {
                                swal(
                                    'Gagal Menyimpan Data!',
                                    '',
                                    'error'
                                )
                            },
                            cache: false,
                            contentType: false,
                            processData: false
                        });
                    }
                })
            })
        </script>
    @endpush
@endsection
