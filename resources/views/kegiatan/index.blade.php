@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Kegiatan</h3>
    </div>
    <div class="card-body">
        <!-- Search Form -->
        <div class="row mb-3">
            <div class="col-md-6">
                <form action="{{ url('/kegiatan') }}" method="GET" class="form-inline">
                    <div class="input-group w-100">
                        <input type="text" class="form-control" name="cari" placeholder="Cari Kegiatan" value="{{ $cari ?? '' }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary">Cari</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Nama Kegiatan</th>
                        <th>Tanggal</th>
                        <th>Status Penugasan</th>
                        <th>Berkas kegiatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kegiatans as $kegiatan)
                        <tr>
                            <td>{{ $kegiatan->nama_kegiatan }}</td>
                            <td>{{ \Carbon\Carbon::parse($kegiatan->tanggal_mulai)->format('d-m-Y') }}</td>
                            <td>
                                @php
                                    $statusClass = match($kegiatan->status_penugasan) {
                                        'Selesai' => 'success',
                                        'Sedang Berlangsung' => 'warning',
                                        'Terjadwal' => 'info',
                                        default => 'primary'
                                    };
                                    
                                    $badgeColor = match($kegiatan->status_penugasan) {
                                        'Selesai' => 'background-color: #d4edda; color: #155724;',
                                        'Sedang Berlangsung' => 'background-color: #fff3cd; color: #856404;',
                                        'Terjadwal' => 'background-color: #cce5ff; color: #004085;',
                                        default => 'background-color: #e2e3e5; color: #383d41;'
                                    };
                                @endphp
                                <span class="px-3 py-2 rounded" style="{{ $badgeColor }}">
                                    {{ $kegiatan->status_penugasan }}
                                </span>
                            </td>
                            <td class="text-center">
                                @if($kegiatan->file_surat)
                                    <a href="{{ route('kegiatan.unduh', $kegiatan->kegiatan_id) }}" 
                                       class="btn btn-success">
                                        <i class="fas fa-download"></i> Unduh
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada berkas</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                Tidak ada kegiatan yang ditemukan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $kegiatans->links() }}
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .pagination {
        justify-content: center;
    }
    .table td {
        vertical-align: middle;
    }
    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        color: white;
        padding: 0.375rem 0.75rem;
    }
    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
</style>
@endpush