<!-- start tab pane -->
<div class="tab-pane detailItem" id="user1" role="tabpanel">
    <div class="container">
        <form action="{{ route('api.web.penilaian-etika.store.penilaian') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="img-user" id="img-user1" style="background-image: url('assets/images/img-user.png');">
                    </div>
                    <div class="nama-id">
                        <h6 class="nama-pegawai">Alfian Labeda</h6>
                        <span class="nip-pegawai">1929298282929000</span>
                    </div>
                    <div class="btn-control float-right">
                        <button type="button" data-pegawai-index="" onclick="navPegawaiButton(this)" class="btn nav-pegawai btn-rounded prevItem"><i class="fas fa-angle-left"></i></button>
                        <button type="button" data-pegawai-index="" onclick="navPegawaiButton(this)" class="btn nav-pegawai btn-rounded active nextItem"><i class="fas fa-angle-right"></i></button>
                    </div>
                    <div class="clearfix"></div>
                    <h6 class="mb-2 mt-4">Nilai Etika</h6>

                    <div class="row">
                        <div class="col-md-9">
                            <div class="btn-slider">
                                <div class="slidecontainer">
                                    <input type="range" class="slider" min="0" max="100" id="rate" name="rate" value="0" onchange="thisRate.value = rate.value" oninput="thisRate.value = rate.value">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 value-slider">
                            <div class="values">
                                <output name="thisRate" for="rate">0</output><span>%</span></p>
                                </form>
                                <div class="clearfix"></div>
                            </div>
                            <div class="ket">Cukup Baik</div>
                        </div>
                    </div>

                    <h6 class="mb-2 mt-4">Keterangan Penilaian Etika</h6>
                    <textarea required autofocus rows="8" class="form-control" id="keterangan-etika"></textarea>
                    <div class="mt-2 float-right">
                        <button type="submit" class="btn btn-custom" id="simpan-penilaian">Simpan</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- end of tab pane --}}
