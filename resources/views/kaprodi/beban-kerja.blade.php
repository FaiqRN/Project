{{-- resources/views/statistik/hasil.blade.php --}}

@extends('layouts.template')

@push('css')
<style>
    body {
        background-color: #e0e0e0;
    }
    .container {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        margin: 20px auto;
    }
    h1 {
        text-align: center;
        font-size: 24px;
        margin-bottom: 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        border: 1px solid #000000;
        padding: 8px;
        text-align: center;
    }
    th {
        background-color: #d3d3d3;
    }
    .form-control {
        background-color: #f8f9fa;
    }
    .form-group label {
        font-weight: bold;
        color: #666;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h1>Hasil Statistik Beban Kerja Dosen</h1>
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>NIDN</th>
                <th>Rapat Kurikulum</th>
                <th>Workshop</th>
                <th>Seminar Akademik</th>
                <th>Pengabdian</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($poinData as $poin)
                <tr>
                    <td>{{ $poin->nama_lengkap }}</td>
                    <td>{{ $poin->nidn }}</td>
                    <td>{{ $poin->jumlah_poin }}</td> {{-- Misalnya poin rapat --}}
                    <td>{{ $poin->jumlah_poin }}</td> {{-- Misalnya poin workshop --}}
                    <td>{{ $poin->jumlah_poin }}</td> {{-- Misalnya poin seminar akademik --}}
                    <td>{{ $poin->jumlah_poin }}</td> {{-- Misalnya poin pengabdian --}}
                    <td>{{ $poin->jumlah_poin * 4 }}</td> {{-- Total poin --}}
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
