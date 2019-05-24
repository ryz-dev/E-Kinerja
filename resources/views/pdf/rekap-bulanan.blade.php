<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Rekap Bulanan E-Kinerja Kab. Kolaka</title>
    <style>
        body{
            font-family: Calibri;
        }

        .main thead tr{
            background-color: #aac1bf;
        }

        .main tr td{
            padding:5px 5px;
            font-size: 13px;
            border: 2px solid white;
        }

        .main tr:nth-child(even) {
            background-color: #7db5e0;
        }

        .main thead{
            text-align: center;
            font-weight: bold;
        }

        .center-text{
            text-align: center;
        }

        .right-text{
            text-align: right;
        }
    </style>
</head>
<body border=0>
    <table>
        <tr>
            <td rowspan=4><img src="{{ base_path() }}/public/assets/images/logo_kab.kolaka.png" alt=""></td>
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
            <td>:{{$namaSkpd}}</td>
            <td></td>
            <td></td>
        </tr>
    </table>
    
    <br>
    <table border="1" width="100%" class="main" style="border: 2px solid white;border-collapse: collapse;" >
        <thead>
            <tr>
                <td rowspan="2">NIP</td>
                <td rowspan="2">NAMA PEGAWAI</td>
                <td rowspan="2">JABATAN</td>
                <td rowspan="2">KELAS JABATAN</td>
                <td rowspan="2">KINERJA (%)</td>
                <td rowspan="2">ABSEN (%)</td>
                <td rowspan="2">KEPATUHAN (%)</td>
                <td colspan="2">TUNJANGAN</td>
            </tr>
            <tr>
                <td>%</td>
                <td>NILAI</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $key => $value)
                <tr>
                    <td>{{ $value['data_pribadi']['nip'] }}</td>
                    <td>{{ $value['data_pribadi']['nama'] }}</td>
                    <td>{{ $value['jabatan'] }}</td>
                    <td>{{ $value['kelas_jabatan'] }}</td>
                    <td class="center-text">{{ floor($value['persentase_kinerja']) }}</td>
                    <td class="center-text">{{ number_format($value['persentase_absen'],2) }}</td>
                    <td class="center-text">{{ number_format($value['persentase_kepatuhan'],2) }}</td>
                    <td class="center-text">{{ floor($value['total_persentase']) }}</td>
                    <td class="right-text">{{ number_format((float)$value['total_tunjangan'],2,',','.') }}</td>
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
