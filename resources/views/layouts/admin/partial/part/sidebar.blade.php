<div class="sidebar1">
    <div class="brand">
        <a href="{{ url('/master-data') }}">
          <img src="{{ asset('assets/images/logo.svg') }}" width="120">
        </a>
    </div>
    <ul>
        <li><a href="{{route('pegawai.index')}}" class="{{str_contains(url()->current(),route('pegawai.index')) ? 'active' : ''}}">Pegawai</a></li>
        <li><a href="{{route('hari_kerja')}}" class="{{str_contains(url()->current(),route('hari_kerja')) ? 'active' : ''}}">Hari Kerja</a></li>
        <li><a href="{{route('eselon.index')}}" class="{{str_contains(url()->current(),route('eselon.index')) ? 'active' : ''}}">Eselon</a></li>
        <li><a href="{{route('jabatan.index')}}" class="{{str_contains(url()->current(),route('jabatan.index')) ? 'active' : ''}}">Jabatan</a></li>
        <li><a href="{{route('checkinout.index')}}" class="{{str_contains(url()->current(),route('checkinout.index')) ? 'active' : ''}}">Absensi</a></li>
        <li><a href="{{route('skpd.index')}}" class="{{str_contains(url()->current(),route('skpd.index')) ? 'active' : ''}}">SKPD</a></li>
        {{--<li><a href="penilaian-etika.html">Penilaian Etika</a></li>--}}
        {{--<li><a href="tunjangan-kinerja.html">Tunjangan Kinerja</a></li>--}}
        @stack('sidebar')
    </ul>
    <div class="toggleSwitch">
        <label class="mr-2">Mode Malam</label>
        <label class="switch">
            <input id="toggle-switch" type="checkbox">
            <span class="slider round"></span>
        </label>
    </div>
</div>
