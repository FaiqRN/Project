@extends('layouts.template')

@section('content')
<div class="container">
    <div class="header">Statistik Beban Kerja Dosen</div>
    <div class="controls">
        <select class="form-control">
            <option>2024</option>
        </select>
        <select class="form-control">
            <option>Semester ganjil</option>
        </select>
        <button class="btn btn-success"><i class="fas fa-download"></i> Unduh File</button>
    </div>
    <div class="chart-title">Grafik Visualisasi Beban Kerja</div>
    <div class="chart">
        @foreach ($poinData as $poin)
            <div class="bar" style="height: {{ $poin->jumlah_poin }}%;"><div class="bar-label">{{ $poin->nama_lengkap }}</div></div>
        @endforeach
    </div>
</div>
@endsection

@push('css')
<style>
    .form-control {
        background-color: #f8f9fa;
    }
    .form-group label {
        font-weight: bold;
        color: #666;
    }
    body {
        font-family: Arial, sans-serif;
        background-color: #d3d3d3;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    .container {
        background-color: #f0f0f0;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    .header {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
    }
    .controls {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
    }
    .controls select, .controls button {
        margin: 0 10px;
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    .controls button {
        background-color: #28a745;
        color: white;
        border: none;
        cursor: pointer;
    }
    .controls button:hover {
        background-color: #218838;
    }
    .chart-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }
    .chart {
        display: flex;
        justify-content: space-around;
        align-items: flex-end;
        height: 200px;
        margin-top: 20px;
    }
    .bar {
        width: 50px;
        background-color: #d4a017;
        text-align: center;
        color: white;
        font-weight: bold;
    }
    .bar-label {
        margin-top: 5px;
        font-size: 14px;
    }
</style>
@endpush
