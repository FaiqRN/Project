<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Statistik Beban Kerja Dosen</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f5f5f5;
        }
        .chart-image {
            width: 100%;
            margin: 20px 0;
        }
        .periode {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Statistik Beban Kerja Dosen</h2>
        <p>Jurusan Teknologi Informasi</p>
        <p>Periode: {{ $periode }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Nama Dosen</th>
                <th>Nama Kegiatan</th>
                <th>Jenis Kegiatan</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Poin JTI</th>
                <th>Poin Non-JTI</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td>{{ $item->nama_dosen }}</td>
                <td>{{ $item->nama_kegiatan }}</td>
                <td>{{ $item->jenis_kegiatan }}</td>
                <td>{{ Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td>Selesai</td>
                <td>{{ $item->poin_jti }}</td>
                <td>{{ $item->poin_non_jti }}</td>
                <td>{{ $item->total_poin }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>