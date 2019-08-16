@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
        <div class="nav-top-container">
            @include('layouts.admin.partial.part.logout')
        </div>
        <div class="main-content tab-content">
            <div class="container-fluid">
                <form id="form-update-jabatan" action="{{$jabatan->update_uri}}" class="form">
                    <div class="row">
                        <div class="col-md-6">
                            <h2 class="mb-2">Edit Jabatan</h2>
                            <div class="form-group">
                                <label for="jabatan">Jabatan</label>
                                <input id="jabatan" type="text" class="form-control" name="jabatan" placeholder="Jabatan" value="{{$jabatan->jabatan}}" required>
                            </div>
                            <div class="form-group">
                                <label for="golongan">Kelas Jabatan</label>
                                <select class="select2 form-control" name="id_golongan" id="golongan" required>
                                    <option value="">Pilih Kelas Jabatan</option>
                                    @foreach($data_option->golongan AS $gol)
                                        <option value="{{$gol->id}}" {{$gol->id == $jabatan->id_golongan ? 'selected' : ''}}>{{$gol->golongan}} ({{$gol->kriteria}}) (Rp.{{$gol->tunjangan_rp}})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="jabatan">Atasan</label>
                                <select class="select2 form-control" name="id_atasan" id="jabatan">
                                    <option value="">Pilih Atasan</option>
                                    @foreach($data_option->jabatan AS $jb)
                                        <option value="{{$jb->id}}" {{$jb->id == $jabatan->id_atasan ? 'selected' : ''}}>{{$jb->jabatan}} - {{$jb->keterangan}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="keterangan">Keterangan Jabatan</label>
                                <textarea name="keterangan" id="keterangan" rows="3" class="form-control">{{$jabatan->keterangan}}</textarea>
                            </div>
                            {{--<div class="form-group">
                                <label for="urutan">Urutan</label>
                                <input type="number" id="urutan" name="urutan" class="form-control" value="{{$jabatan->urutan}}">
                            </div>--}}
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                        <div class="col-md-6">

                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @push('script')
        <script>
            $('#form-update-jabatan').on('submit',function (e) {
                e.preventDefault();
                var action = this.action;
                var formData = new FormData($(this)[0]);
                swal({
                    title: 'Ingin Menyimpan Data?',
                    text: "",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Iya, simpan data!',
                    cancelButtonText: 'Batalkan'
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
                                    location.href = '{{$jabatan->detail_uri}}'
                                }, 500);
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
