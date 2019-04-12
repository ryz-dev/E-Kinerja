@extends('layouts.users.partial.main')
@section('class','input-skp tambah-skp')
@section('content')
<div class="main">
    <div class="nav-top-container">
        <div class="nav-top">
            <div class="title-nav float-left">
                <h4 class="mr-3 float-left">Sasaran Kerja Pegawai</h4>
                <span class="badge text-white">{{ucfirst(\App\Models\MasterData\Hari::find(date('N'))->nama_hari)}}, {{date('d')}} {{ucfirst(\App\Models\MasterData\Bulan::find((int)date('m'))->nama_bulan)}} {{date('Y')}}</span>
            </div>

            {{-- <div class="img-profile" id="user-profile" style="background-image: url('{{ \Auth::user()->foto?url('').'/storage/'.\Auth::user()->foto:asset('assets/images/img-user.png') }}');">
            </div> --}}
            @include('layouts.users.partial.part.logout')
        </div>

    </div>
    <form action="{{ route('sasaran-kerja.store') }}" method="POST">
    <div class="main-content mt-1">
        <div class="container-fluid">
            <div class="row wrap-skp">
              <div class="sub-wrap-skp mb-3">
                <div>
                    <div class="img-user" style="background-image:url('{{ \Auth::user()->foto?url('').'/storage/'.\Auth::user()->foto:asset('assets/images/img-user.png') }}');">
                    </div>
                    <div class="nama-id">
                      <input type="hidden" name="nip" value="{{$sasaran_kerja['dataPegawai']->nip}}">
                      <input type="hidden" name="periode" value="{{$sasaran_kerja['periode']}}">
                        <h6>{{ $sasaran_kerja['dataPegawai']->nama }}</h6>
                        <span>{{ $sasaran_kerja['dataPegawai']->nip }}</span>
                    </div>
                </div>
                <div>
                  <span class="badge badge-primary"><h6 class="text-white">{{ namaBulan(month($sasaran_kerja['periode'])) }} {{ year($sasaran_kerja['periode']) }}</h6></span>
                </div>

                <div>
                  <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
              </div>
                    <div class="clearfix"></div>
            </div>
            
            <div class="row skp-content">
                
                    {{ csrf_field() }}
                    <div class="bg-white">
                    <div class="title-skp">
                        <h4>Sasaran Kerja Pegawai</h4>
                        <button type="button" class="btn-style primary" id="showAddSkp">
                        <i class="material-icons">playlist_add</i>
                        Tambah SKP
                        </button>
                    </div>
                    <div class="skp-desc parent-sasaran mt-3" id="sasaran">
                        @foreach ($sasaran_kerja['sasaranKerja'] as $item => $task)
                            <div class="child-skp">
                                <textarea style="border: none" class="form-control values-skp" name="skp[]" readonly>{{ $task['task'] }}</textarea>
                                <div class="flex">
                                    <div class="btn-style gray warning btn-edit-skp" style="border:none;">
                                    <i class="material-icons">edit</i>
                                    </div>
                                    <div class="btn-style gray danger btn-delete-skp" style="border:none;">
                                        <i class="material-icons">delete</i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <!-- Add SKP -->
                        <div class="modal-add-skp" id="modal-add-skp">
                        <div class="container">
                            <div class="row">
                            <div class="col-md-12 mb-3">
                                <textarea class="form-control" id="value-skp" rows="2"></textarea>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-primary float-right" id="tambah-skp">Tambah</button>
                            </div>
                            </div>
                        </div>
                        </div>
                        <!-- end -->
                    </div>
                    </div>

                </form>

                <div class="wrap-list-skp">
                  <div class="bg-white mb-3">
                    <div>
                      <h4>SKP Distribusi</h4>
                    </div>
                    <div class="skp-distribusi parent-distribusi mt-3" id="distribusi">
                        @if($sasaran_kerja['sasaranKerjaAtasan'])
                          @foreach ($sasaran_kerja['sasaranKerjaAtasan'] as $item => $task)
                          <div class="child-skp">
                              <input name="skpDistribusi[]" type="text" style="border:none;" class="form-control values-skp" value="{{ $task['task'] }}" disabled/>
                              <!-- display none here -->
                              <div class="flex">
                                  <div class="btn-style gray warning btn-edit-skp" style="border:none;">
                                  <i class="material-icons">edit</i>
                                  </div>
                                  <div class="btn-style gray danger btn-delete-skp" style="border:none;">
                                      <i class="material-icons">delete</i>
                                  </div>
                              </div>
                              <!-- display none -->
                          </div>
                          @endforeach
                        @endif
                    </div>
                  </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('script')
<script src="{{ asset('assets/js/script.js') }}"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/autosize.js/4.0.2/autosize.min.js"></script>
<script>
  
  let sasaran = $('#sasaran');
  let distribusi = $('#distribusi')
  let terealisasi = $('#terealisasi')
  let valueSkp = $('#value-skp')
  let tambahSkp = $('#tambah-skp')
  let idModalSkp = $('#modal-add-skp')
  let btnShowAddSkp = $('#showAddSkp')
  
  $(function() {
    $(btnShowAddSkp).on('click', function(e){
      $(idModalSkp).toggleClass('show')    
      e.stopPropagation()
    })
    $(idModalSkp).on('click', function(e) {
      e.stopPropagation()
    })
    $(document).on('click', function(e) {
      if($(e.target).is(idModalSkp) === false) {
        $(idModalSkp).removeClass('show')
      }
    })
  })

  $(document).ready(function() {
    autosize($("input[name='skp[]']"));
    
    $(tambahSkp).on('click', function(e) {
      e.preventDefault()
      addSkp()
    })

    function addSkp() {
        
      let valueTarget = valueSkp.val()
      if (valueTarget) {
        sasaran.append(
          `
          <div class="child-skp">
            <textarea class="form-control values-skp" name="skp[]" readonly>${valueTarget}</textarea>
            <div class="flex">
              <div class="btn-style gray warning btn-edit-skp" style="border:none;">
                <i class="material-icons">edit</i>
              </div>
              <div class="btn-style gray danger btn-delete-skp" style="border:none;">
                  <i class="material-icons">delete</i>
              </div>
            </div>
          </div>
          `
        )
        idModalSkp.removeClass('show')
        valueSkp.val('')
        $('.parent-sasaran textarea.values-skp').css('border','none')
        autosize($("input[name='skp[]']"));
      }
    }

    // DRAG DROP
    $('.child-skp').draggable({
      revert: true
    })
    
    $('.parent-sasaran').droppable({
      accept: '.child-skp',
      drop: function(event, ui) {
        $(this)
        .append($(ui.draggable))
      }
    })
    

    $('.parent-distribusi, .parent-terealisasi').droppable({
      accept: '.child-skp',
      drop: function(event, ui) {
        $(this)
        .append($(ui.draggable))
      }
    })
    // end drag

    // DELETE
    $('.parent-sasaran').on('click','.btn-delete-skp', function() {
      if(confirm('Yakin Ingin Hapus ?')){
        $(this).parents('.child-skp').remove()
      }
    })

    // EDIT

    $('.parent-sasaran').on('click','.btn-edit-skp', function() {
      $('.parent-sasaran textarea.values-skp').prop('readonly', false)
      $('.parent-sasaran textarea.values-skp').focus()
      $(this).attr('class','btn-style gray primary btn-save-skp')
      $('.btn-save-skp i').html('save')
      $('.parent-sasaran textarea.values-skp').css('border','1px solid #8a9195')
    })

    $('.parent-sasaran').on('click','.btn-save-skp', function() {
      $(this).attr('class','btn-style gray warning btn-edit-skp')
      $('.btn-edit-skp i').html('edit')
      $('.parent-sasaran textarea.values-skp').attr('readonly', true)
      let value = $('.parent-sasaran textarea.values-skp').val()
      $('.parent-sasaran textarea.values-skp').append(value)
      $('.parent-sasaran textarea.values-skp').css('border','none')
    })
  })
</script>
@endpush
@endsection
