<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Data Gaji</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        h2 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>Data Gaji Pegawai - {{ $selectedOutlet['outlet_name'] }}</h2>
    <h3>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</h3>

    <table>
        <thead>
            <tr>
                <th>Nama Pegawai</th>
                <th>Pekerjaan</th>
                <th>Jumlah Shift</th>
                <th>Total Gaji</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($dataGaji as $gaji)
                <tr>
                    <td>{{ $gaji->nama_pegawai }}</td>
                    <td>{{ $gaji->nama_pekerjaan }}</td>
                    <td>{{ $gaji->jumlah_shift }}</td>
                    <td>Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
