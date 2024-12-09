@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-4">Progress Kegiatan</h4>
        </div>
    </div>

    <!-- Kegiatan Jurusan -->
    <div class="row mb-4">
        <div class="col-12">
            <h5 class="text-primary">Kegiatan Jurusan</h5>
        </div>
        @forelse($kegiatanJurusan as $kegiatan)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">{{ $kegiatan->nama_kegiatan_jurusan }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-1">Total Agenda: {{ $kegiatan->progress['total_agendas'] }}</p>
                        <p class="mb-1">Agenda Selesai: {{ $kegiatan->progress['completed_agendas'] }}</p>
                        <p class="mb-3">Jumlah Anggota: {{ $kegiatan->progress['total_members'] }}</p>
                        
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-primary" 
                                 role="progressbar" 
                                 style="width: {{ $kegiatan->progress['percentage'] }}%"
                                 aria-valuenow="{{ $kegiatan->progress['percentage'] }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $kegiatan->progress['percentage'] }}%
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge badge-primary">Kegiatan Jurusan</span>
                        <small>{{ $kegiatan->tanggal_mulai->format('d/m/Y') }} - {{ $kegiatan->tanggal_selesai->format('d/m/Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                Tidak ada kegiatan jurusan yang sedang berlangsung.
            </div>
        </div>
        @endforelse
    </div>

    <!-- Kegiatan Prodi -->
    <div class="row">
        <div class="col-12">
            <h5 class="text-success">Kegiatan Program Studi</h5>
        </div>
        @forelse($kegiatanProdi as $kegiatan)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">{{ $kegiatan->nama_kegiatan_program_studi }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-1">Total Agenda: {{ $kegiatan->progress['total_agendas'] }}</p>
                        <p class="mb-1">Agenda Selesai: {{ $kegiatan->progress['completed_agendas'] }}</p>
                        <p class="mb-3">Jumlah Anggota: {{ $kegiatan->progress['total_members'] }}</p>
                        
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-success" 
                                 role="progressbar" 
                                 style="width: {{ $kegiatan->progress['percentage'] }}%"
                                 aria-valuenow="{{ $kegiatan->progress['percentage'] }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $kegiatan->progress['percentage'] }}%
                            </div>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge badge-success">Kegiatan Prodi</span>
                        <small>{{ $kegiatan->tanggal_mulai->format('d/m/Y') }} - {{ $kegiatan->tanggal_selesai->format('d/m/Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                Tidak ada kegiatan program studi yang sedang berlangsung.
            </div>
        </div>
        @endforelse
    </div>
</div>

@push('css')
<style>
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        transition: all 0.3s ease;
    }
    .card:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .progress-bar {
        transition: width 0.6s ease;
    }
    .badge {
        padding: 0.5em 1em;
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    $('.progress-bar').each(function() {
        $(this).css('width', $(this).attr('aria-valuenow') + '%');
    });
});
</script>
@endpush
@endsection