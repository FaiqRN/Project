@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <!-- Filter & Button -->
        <div class="col-12 d-flex justify-content-between align-items-center">
            <select class="form-control" style="width: 200px;" id="bulanFilter">
                @for($i = 0; $i < 12; $i++)
                    @php
                        $date = now()->subMonths($i);
                        $value = $date->format('Y-m');
                        $label = $date->format('F Y');
                    @endphp
                    <option value="{{ $value }}" {{ $i == 0 ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endfor
            </select>
            <button class="btn btn-primary" id="btnDetail">
                <i class="fas fa-eye"></i> Lihat Detail Poin
            </button>
        </div>
    </div>

    <!-- Info Boxes -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info">
                    <i class="fas fa-users"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Dosen</span>
                    <span class="info-box-number">{{ $totalDosen }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success">
                    <i class="fas fa-tasks"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Kegiatan</span>
                    <span class="info-box-number">{{ $totalKegiatan }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning">
                    <i class="fas fa-star"></i>
                </span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Poin</span>
                    <span class="info-box-number">{{ $totalPoin }}</span>
                </div>
            </div>
        </div>
    </div>

    <!--chart-->
    <div class="card">
        <div class="card-body">
            <!-- Tambahkan class untuk mengontrol ukuran -->
            <div class="chart-container" style="height: 300px;">
                <canvas id="bebanKerjaChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detail Table Section -->
    <div class="row d-none" id="detailSection">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <button class="btn btn-link" id="btnKembali">
                                <i class="fas fa-arrow-left"></i>
                            </button>
                            <h3 class="card-title mb-0">Detail Kegiatan dan Poin</h3>
                        </div>
                        <div>
                            <button class="btn btn-danger" onclick="exportPDF()">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                            <button class="btn btn-success ml-2" onclick="exportExcel()">
                                <i class="fas fa-file-excel"></i> Excel
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="detailTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Dosen</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Jenis Kegiatan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Total Poin</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>


<script>
    $(document).ready(function() {
        let bebanKerjaChart = null;
    
        function loadChart() {
            $.ajax({
                url: '{{ route("kaprodi.beban-kerja.statistik") }}',
                method: 'GET',
                data: { bulan: $('#bulanFilter').val() },
                success: function(response) {
                    if (bebanKerjaChart) {
                        bebanKerjaChart.destroy();
                    }
    
                    const ctx = document.getElementById('bebanKerjaChart');
                    bebanKerjaChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: response.labels,
                            datasets: [{
                                label: 'Total Poin',
                                data: response.poin,
                                backgroundColor: '#4e73df',
                                maxBarThickness: 50 // Kontrol lebar maksimum bar
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 10, // Mengatur interval skala
                                        callback: function(value) {
                                            return Math.round(value); // Bulatkan nilai
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return 'Total Poin: ' + Math.round(context.raw);
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            });
        }
    
        // Load chart saat halaman dimuat
        loadChart();
    
        // Reload chart saat filter berubah
        $('#bulanFilter').change(loadChart);
    });
</script>
@endpush