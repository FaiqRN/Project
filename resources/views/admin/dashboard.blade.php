@extends('layouts.template')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Profile Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    @if(session('foto'))
                        <img src="{{ session('foto') }}" class="img-circle elevation-2" alt="User Image" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <img src="{{ asset('adminlte/dist/img/user.jpg') }}" class="img-circle elevation-2" alt="User Image" style="width: 150px; height: 150px; object-fit: cover;">
                    @endif
                </div>
                <div class="col-md-9">
                    <h4>Selamat Datang,</h4>
                    <h5>
                        {{ session('gelar_depan') ? session('gelar_depan') . ' ' : '' }}
                        {{ session('nama_lengkap') }}
                        {{ session('gelar_belakang') ? ', ' . session('gelar_belakang') : '' }}
                    </h5>
                    <p class="text-muted mb-2">
                        Administrator Sistem<br>
                        {{ session('program_studi') }}
                    </p>
                    <a href="{{ route('profile') }}" class="btn btn-primary">
                        <i class="fas fa-user mr-2"></i>Lihat Profil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-user-tie"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Dosen</span>
                    <span class="info-box-number">{{ $totalDosen ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-tasks"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Kegiatan</span>
                    <span class="info-box-number">{{ $totalKegiatan ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-file-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Surat Tugas</span>
                    <span class="info-box-number">{{ $totalSuratTugas ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Kegiatan Pending</span>
                    <span class="info-box-number">{{ $kegiatanPending ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Daftar Dosen -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users mr-1"></i>
                        Daftar Dosen
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.dosen.profile') }}" class="btn btn-tool">
                            <i class="fas fa-users"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>NIDN</th>
                                    <th>Total Kegiatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentDosen ?? [] as $dosen)
                                    <tr>
                                        <td>{{ $dosen->nama_lengkap ?? 'Nama Dosen' }}</td>
                                        <td>{{ $dosen->nidn ?? '-' }}</td>
                                        <td>{{ $dosen->total_kegiatan ?? '0' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada data dosen</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kegiatan Terbaru -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-check mr-1"></i>
                        Kegiatan Terbaru
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.dosen.agenda.kegiatan') }}" class="btn btn-tool">
                            <i class="fas fa-calendar"></i> Lihat Semua
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Kegiatan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentKegiatan ?? [] as $kegiatan)
                                    <tr>
                                        <td>{{ $kegiatan->nama ?? 'Nama Kegiatan' }}</td>
                                        <td>{{ $kegiatan->tanggal ?? 'DD/MM/YYYY' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $kegiatan->status_color ?? 'secondary' }}">
                                                {{ $kegiatan->status ?? 'Pending' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center">Tidak ada kegiatan terbaru</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart Statistik -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Statistik Kegiatan Bulanan
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="kegiatanChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Distribusi Status Kegiatan
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .info-box {
        min-height: 100px;
    }
    .info-box .info-box-icon {
        height: 80px;
        width: 80px;
        line-height: 80px;
    }
    .img-circle {
        border: 3px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .table td, .table th {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kegiatan Bulanan Chart
    const ctxKegiatan = document.getElementById('kegiatanChart').getContext('2d');
    new Chart(ctxKegiatan, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Jumlah Kegiatan',
                data: [12, 19, 3, 5, 2, 3],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Status Kegiatan Chart
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Selesai', 'Berjalan', 'Pending'],
            datasets: [{
                data: [30, 50, 20],
                backgroundColor: [
                    'rgb(40, 167, 69)',
                    'rgb(23, 162, 184)',
                    'rgb(255, 193, 7)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
@endpush
@endsection