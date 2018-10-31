@extends('layouts.partial.main')
@section('content')
  <div class="main-content">
      <div class="container-fluid">
          <div class="row">
              <div class="col-md-9">
                  <div class="row">
                      <div class="col-md-12">
                          <div class="title-nav float-left">
                              <h4 class="mr-3 float-left">Monitoring Absen</h4>
                              <span class="badge text-white">23 September 2018</span>
                          </div>
                          <div class="btn-control float-right mt-1">
                              <div class="date-group float-left">
                                  <span class="icon-date"><i class="fas fa-calendar-alt"></i></span>
                                  <input class="datepicker" placeholder="Pilih Tanggal" />
                              </div>
                              <div class="float-right">
                                  <button class="btn btn-rounded"><i class="fas fa-angle-left"></i></button>
                                  <button class="btn btn-rounded active"><i class="fas fa-angle-right"></i></button>
                              </div>
                          </div>
                          <div class="clearfix"></div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-12">
                          <table class="table table-responsive table-pegawai">
                              <thead>
                                  <tr>
                                      <th scope="col"></th>
                                      <th scope="col">NIP Pegawai</th>
                                      <th scope="col">Nama Pegawai</th>
                                      <th scope="col">Jabatan</th>
                                      <th scope="col">Jenis Kelamin</th>
                                  </tr>
                              </thead>
                              <tbody class="list_pegawai">
                              </tbody>
                          </table>
                      </div>
                  </div>
              </div>
              <div class="col-md-3">
                  <div class="description">
                      <h6 class="font-weight-bold">Keterangan</h6>
                      <ul>
                          <li>
                              <span class="hadir">
                                  <img src="{{ asset('assets/images/icons/hadir.svg') }}">
                              </span>
                              <label>Hadir</label>
                              <label class="float-right count">21</label>
                          </li>
                          <li>
                              <span class="dinas">
                                  <img src="{{ asset('assets/images/icons/perjalanan_dinas.svg') }}">
                              </span>
                              <label>Perj. Dinas</label>
                              <label class="float-right count">21</label>
                          </li>
                          <li>
                              <span class="cuti">
                                  <img src="{{ asset('assets/images/icons/cuti.svg') }}">
                              </span>
                              <label>Cuti</label>
                              <label class="float-right count">3</label>
                          </li>
                          <li>
                              <span class="izin">
                                  <img src="{{ asset('assets/images/icons/izin.svg') }}">
                              </span>
                              <label>Izin</label>
                              <label class="float-right count">4</label>
                          </li>
                          <li>
                              <span class="sakit">
                                  <img src="{{ asset('assets/images/icons/sakit.svg') }}">
                              </span>
                              <label>Sakit</label>
                              <label class="float-right count">2</label>
                          </li>
                          <li>
                              <span class="alpha">
                                  <img src="{{ asset('assets/images/icons/alpha.svg') }}">
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
