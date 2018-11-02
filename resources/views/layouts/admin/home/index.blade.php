@extends('layouts.admin.partial.main')
@section('content')
    <div class="main">
      <div class="container-fluid">
          <div class="row">
              <div class="col-md-9">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="title-nav float-left">
                        <h4 class="mr-3 float-left">Monitoring Absen</h4>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                      <div class="col-md-12">
                          Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
                      </div>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="description">
                      <h6 class="font-weight-bold">Keterangan</h6>
                      <ul>
                          <li>
                              <span class="hadir">
                                  <img src="assets/images/icons/hadir.svg">
                              </span>
                              <label>Hadir</label>
                              <label class="float-right count">21</label>
                          </li>
                          <li>
                              <span class="dinas">
                                  <img src="assets/images/icons/perjalanan_dinas.svg">
                              </span>
                              <label>Perj. Dinas</label>
                              <label class="float-right count">21</label>
                          </li>
                          <li>
                              <span class="cuti">
                                  <img src="assets/images/icons/cuti.svg">
                              </span>
                              <label>Cuti</label>
                              <label class="float-right count">3</label>
                          </li>
                          <li>
                              <span class="izin">
                                  <img src="assets/images/icons/izin.svg">
                              </span>
                              <label>Izin</label>
                              <label class="float-right count">4</label>
                          </li>
                          <li>
                              <span class="sakit">
                                  <img src="assets/images/icons/sakit.svg">
                              </span>
                              <label>Sakit</label>
                              <label class="float-right count">2</label>
                          </li>
                          <li>
                              <span class="alpha">
                                  <img src="assets/images/icons/alpha.svg">
                              </span>
                              <label>Alpha</label>
                              <label class="float-right count">0</label>
                          </li>
                      </ul>
                  </div>

                  <div class="clock-side">
                      <span>17:10</span>
                  </div>
                  <div class="day-side">
                      <small>Selasa, 23 September 2018</small>
                  </div>
              </div>
          </div>
      </div>
  </div>
@endsection
