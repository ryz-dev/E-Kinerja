<!-- start tab pane -->
<div class="tab-pane detailItem" id="user1" role="tabpanel">
    <div class="container">
        <form action="{{ route('api.web.penilaian-etika.store.penilaian') }}" method="POST" oninput="thisRate.value=Math.floor((parseInt(rate_upacara.value)*0.3)+(parseInt(rate_perilaku_kerja.value)*0.3)+(parseInt(rate_kegiatan_kebersamaan.value)*0.4))">
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="img-user" id="img-user1" style="background-image: url('assets/images/img-user.png');">
                    </div>
                    <div class="nama-id">
                        <h6 class="nama-pegawai"> </h6>
                        <span class="nip-pegawai"> </span>
                    </div>
                    <div class="btn-control float-right">
                        <button type="button" data-pegawai-index="" onclick="navPegawaiButton(this)" class="btn nav-pegawai btn-rounded prevItem"><i class="fas fa-angle-left"></i></button>
                        <button type="button" data-pegawai-index="" onclick="navPegawaiButton(this)" class="btn nav-pegawai btn-rounded active nextItem"><i class="fas fa-angle-right"></i></button>
                    </div>
                    <div class="clearfix"></div>
                    
                    <div class="row">
                        <div class="col-md-9">
                            <h6 class="mt-4">Upacara dan Apel (30%)</h6>
                            <div class="btn-slider">
                                <div class="slidecontainer">
                                    {{-- <form onsubmit="return false" oninput="amount.value = (principal.valueAsNumber * re.vaatlueAsNumber) / 100"> --}}
        
                                        <input type="range" class="slider" min="0" step="5" max="100" id="rate_upacara" name="rate_upacara"
                                            value="0" onchange="updateKeterangan()" oninput="upacara.value = rate_upacara.value">
                                </div>
                            </div>
                        </div>
                        
        
                        <div class="col-md-3 mt-4 value-slider">
                            <div class="values">
                                <output name="upacara" for="rate_upacara">0</output><span>%</span></p>
                                {{-- </form> --}}
                                <div class="clearfix"></div>
                            </div>
                            <!-- <div class="ket">Cukup Baik</div> -->
                        </div>
                    </div>
        
                    <div class="row">
                        <div class="col-md-9">
                            <h6 class="mt-4">Perilaku Kerja (30%)</h6>
                            <div class="btn-slider">
                                <div class="slidecontainer">
                                    {{-- <form onsubmit="return false" oninput="amount.value = (principal.valueAsNumber * re.vaatlueAsNumber) / 100"> --}}
        
                                        <input type="range" class="slider" min="0" step="5" max="100" id="rate_perilaku_kerja" name="rate_perilaku_kerja"
                                            value="0" onchange="updateKeterangan()" oninput="perilaku_kerja.value = rate_perilaku_kerja.value">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mt-4 value-slider">
                            <div class="values">
                                <output name="perilaku_kerja" for="rate_perilaku_kerja">0</output><span>%</span></p>
                                {{-- </form> --}}
                                <div class="clearfix"></div>
                            </div>
                            <!-- <div class="ket">Cukup Baik</div> -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-9">
                            <h6 class="mt-4">Kegiatan Kebersamaan (40%)</h6>
                            <div class="btn-slider">
                                <div class="slidecontainer">
                                    {{-- <form onsubmit="return false" oninput="amount.value = (principal.valueAsNumber * re.vaatlueAsNumber) / 100"> --}}
        
                                        <input type="range" class="slider" min="0" step="5" max="100" id="rate_kegiatan_kebersamaan" name="rate_kegiatan_kebersamaan"
                                            value="0" onchange="updateKeterangan()" oninput="kegiatan_kebersamaan.value = rate_kegiatan_kebersamaan.value">
                                </div>
                            </div>
                        </div>
                        
        
                        <div class="col-md-3 mt-4 value-slider">
                            <div class="values">
                                <output name="kegiatan_kebersamaan" for="rate_kegiatan_kebersamaan">0</output><span>%</span></p>
                                {{-- </form> --}}
                                <div class="clearfix"></div>
                            </div>
                            <!-- <div class="ket">Cukup Baik</div> -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-9">
                            <h6 class="mb-2 mt-4">Keterangan Penilaian Etika</h6>
                          <textarea autofocus id="keterangan-etika" required rows="5" class="form-control"></textarea>
                        </div>
                        <div class="col-md-3 mt-4 value-conclusion">
                            <h6 class="mb-2 mt-1">Total Presentasi Etika</h6>
                            <div class="values">
                                <output name="thisRate" for="rate_kegiatan_kebersamaan rate_perilaku_kerja rate_upacara">0</output><span>%</span></p>
                                </form>
                                <div class="clearfix"></div>
                            </div>
                            <div class="ket">Cukup Baik</div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <button id="simpan-penilaian" class="btn btn-custom float-right">Simpan</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- end of tab pane --}}
