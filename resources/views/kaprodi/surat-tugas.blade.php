@extends('layouts.template')

@section('content')
<div class="container">
    <h1>Download Dokumen Surat Tugas</h1>
    
    <!-- Pencarian dan filter status -->
    <div class="search-bar">
        <input type="text" placeholder="Cari Surat Tugas" class="form-control">
        <select class="form-control">
            <option>Semua Status</option>
        </select>
    </div>

    <!-- Looping data surat dari controller -->
    @foreach ($surats as $surat)
        <div class="document">
            <h2>{{ $surat->judul_surat }}</h2>
            <p>{{ $surat->nomer_surat }}</p>
            <p>{{ $surat->tanggal_surat }}</p>
            <div class="actions">
                <!-- Tombol Unduh dan Lihat -->
                <a href="{{ route('surat.download', $surat->surat_id) }}" class="btn">Unduh Dokumen</a>
                <a href="{{ route('surat.show', $surat->surat_id) }}" class="btn view">Lihat</a>
            </div>
        </div>
    @endforeach
</div>
@endsection

@push('css')
<style>
    .container {
        width: 80%;
        margin: 20px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    h1 {
        font-size: 24px;
        margin-bottom: 20px;
    }
    .search-bar {
        display: flex;
        margin-bottom: 20px;
    }
    .search-bar .form-control {
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        margin-right: 10px;
    }
    .document {
        background-color: #e0e0e0;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .document h2 {
        font-size: 18px;
        margin-bottom: 10px;
    }
    .document p {
        margin: 5px 0;
    }
    .document .actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .document .actions a {
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        border-radius: 4px;
        text-decoration: none;
        text-align: center;
        cursor: pointer;
    }
    .document .actions a.view {
        background-color: #6c757d;
    }
    /* Styling tambahan sesuai permintaan */
    .form-control {
        background-color: #f8f9fa;
    }
    .form-group label {
        font-weight: bold;
        color: #666;
    }
</style>
@endpush
