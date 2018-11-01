<div class="sidebar1">
    <div class="brand">
        <img src="{{ asset('assets/images/logo.svg') }}" width="120">
    </div>
    <ul>
        <li><a href="{{ route('pegawai') }}" class="active">Pegawai</a></li>
        <li><a href="{{ route('hari_kerja') }}">Hari Kerja</a></li>
        <li><a href="input-kinerja.html">Eselon</a></li>
        <li><a href="penilaian-kinerja.html">Jabatan</a></li>
        {{--<li><a href="penilaian-etika.html">Penilaian Etika</a></li>--}}
        {{--<li><a href="tunjangan-kinerja.html">Tunjangan Kinerja</a></li>--}}
        @stack('sidebar')
    </ul>
    <div class="toggleSwitch">
        <label class="mr-2">Mode Malam</label>
        <label class="switch">
            <input id="toggle-switch" type="checkbox" checked>
            <span class="slider round"></span>
        </label>
    </div>
</div>
