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
                        NIDN: {{ session('nidn') }}<br>
                        Program Studi: {{ session('program_studi') }}
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
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-tasks"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Kegiatan</span>
                    <span class="info-box-number">{{ $totalKegiatan ?? 0 }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Kegiatan Selesai</span>
                    <span class="info-box-number">{{ $kegiatanSelesai ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-spinner"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Kegiatan Berjalan</span>
                    <span class="info-box-number">{{ $kegiatanBerjalan ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-file-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Surat Tugas</span>
                    <span class="info-box-number">{{ $totalSuratTugas ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Grafik dan Daftar Kegiatan -->
    <div class="row">
        <!-- Grafik Kegiatan -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistik Kegiatan Bulanan</h3>
                </div>
                <div class="card-body">
                    <canvas id="kegiatanChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>

        <!-- Daftar Kegiatan Terbaru -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kegiatan Terbaru</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        @forelse($recentKegiatan ?? [] as $kegiatan)
                        <li class="item">
                            <div class="product-info">
                                <a href="javascript:void(0)" class="product-title">
                                    {{ $kegiatan->nama ?? 'Nama Kegiatan' }}
                                    <span class="badge badge-info float-right">{{ $kegiatan->tanggal ?? 'DD/MM/YYYY' }}</span>
                                </a>
                                <span class="product-description">
                                    {{ $kegiatan->deskripsi ?? 'Deskripsi kegiatan' }}
                                </span>
                            </div>
                        </li>
                        @empty
                        <li class="item">
                            <div class="product-info">
                                <span class="product-description">
                                    Tidak ada kegiatan terbaru
                                </span>
                            </div>
                        </li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ route('kaprodi.kegiatan') }}" class="uppercase">Lihat Semua Kegiatan</a>
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
    .img-circle {
        border: 3px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data untuk grafik (dummy data - ganti dengan data sebenarnya dari controller)
    const ctx = document.getElementById('kegiatanChart').getContext('2d');
    new Chart(ctx, {
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
});
</script>
@endpush
@endsection