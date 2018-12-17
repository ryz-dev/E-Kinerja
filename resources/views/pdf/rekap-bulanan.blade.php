<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Rekap Bulanan E-Kinerja Kab. Kolaka</title>
    <style>
    </style>
</head>
<body border=0>
    <table>
        <tr>
            <td rowspan=4><img src="{{ asset('assets\images\logo_kab.kolaka.png') }}" alt=""></td>
        </tr>
        <tr>
            <td colspan="4" style="text-align:center"><h1>REKAP BULANAN E-KINERJA KABUPATEN KOLAKA</h1></td>
        </tr>
        <tr>
            <td style="max-width:10%"><b>PERIODE</b></td>
            <td >:{{ $periode }}</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><b>SKPD</b></td>
            <td>:{{$skpd}}</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    
    <br>
    <table border="1" width="100%" style="border: 1px solid black;border-collapse: collapse;" >
        <thead>
            <tr>
                <td>NIP</td>
                <td>Nama Pegawai</td>
                <td>Jabatan</td>
                <td>Kelas Jabatan</td>
                <td>Kinerja</td>
                <td>Etika</td>
                <td>Absensi</td>
                <td>% Tunjangan</td>
                <td>Nilai Tunjangan</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $value)
                <tr>
                    <td>{{ $value['data_pribadi']['nip'] }}</td>
                    <td>{{ $value['data_pribadi']['nama'] }}</td>
                    <td>{{ $value['data_pribadi']['nip'] }}</td>
                    <td>jabatan</td>
                    <td>{{ floor($value['persentase_total']['kinerja']) }}</td>
                    <td>{{ number_format($value['persentase_total']['etika'],2) }}</td>
                    <td>{{ number_format($value['persentase_total']['absen'],2) }}</td>
                    <td>{{ floor(array_sum($value['persentase_total'])) }}</td>
                    <td>{{ number_format((float)$value['total_tunjangan'],2,',','.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
<br>
    <div style="float: right">Kolaka, {{ $tanggal_cetak }}</div>

<br>
<br>
<br>
<br>
<br>
<br>
<div style="float: right">(____________________)</div>
</body>
</html>