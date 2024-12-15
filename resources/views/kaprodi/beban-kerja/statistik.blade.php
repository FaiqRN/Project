@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistik Beban Kerja Dosen</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#detailModal">
                            <i class="fas fa-list"></i> Lihat Detail Poin
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter Periode -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Periode:</label>
                                <select class="form-control" id="periodFilter">
                                    <option value="current">Januari - Juni 2024</option>
                                    <option value="previous">Juli - Desember 2023</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3 id="totalDosen">5 Dosen</h3>
                                    <p>Total Dosen</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3 id="totalKegiatan">25 Kegiatan</h3>
                                    <p>Total Kegiatan</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3 id="totalPoin">375 Poin</h3>
                                    <p>Total Poin</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grafik -->
                    <div class="chart-container" style="position: relative; height:400px;">
                        <canvas id="bebanKerjaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Poin -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Detail Poin Beban Kerja</h5>
                <div class="float-right">
                    <button type="button" class="btn btn-success btn-sm mr-2" id="btnExcel">
                        <i class="fas fa-file-excel"></i> Excel
                    </button>
                    <button type="button" class="btn btn-danger btn-sm mr-2" id="btnPDF">
                        <i class="fas fa-file-pdf"></i> PDF
                    </button>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="detailTable">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIDN</th>
                                <th>Program Studi</th>
                                <th>Poin Jurusan</th>
                                <th>Poin Prodi</th>
                                <th>Poin Institusi</th>
                                <th>Poin Luar</th>
                                <th>Total Poin</th>
                                <th>Ranking</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
// Variabel global untuk chart
let bebanKerjaChart;

// Inisialisasi Chart
function initChart() {
    const ctx = document.getElementById('bebanKerjaChart').getContext('2d');
    bebanKerjaChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Poin Beban Kerja',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Poin: ${context.raw}`;
                        }
                    }
                }
            }
        }
    });
}

// Load data statistik
function loadStatistikData() {
    const period = $('#periodFilter').val();
    $.ajax({
        url: "{{ route('kaprodi.beban-kerja.statistik') }}",
        data: { 
            period: period,
            _token: "{{ csrf_token() }}"
        },
        method: 'GET',
        success: function(response) {
            if(response.status === 'success') {
                updateChart(response.data);
                updateSummaryCards(response.data);
            }
        },
        error: function(xhr) {
            Swal.fire({
                title: 'Error!',
                text: 'Gagal memuat data statistik',
                icon: 'error'
            });
            console.error('Error:', xhr);
        }
    });
}

// Update chart dengan data baru
function updateChart(data) {
    const labels = data.map(item => item.nama);
    const values = data.map(item => item.total_poin);
    
    bebanKerjaChart.data.labels = labels;
    bebanKerjaChart.data.datasets[0].data = values;
    bebanKerjaChart.update();
}

// Update summary cards
function updateSummaryCards(data) {
    const totalDosen = data.length;
    const totalKegiatan = data.reduce((acc, curr) => acc + curr.total_kegiatan, 0);
    const totalPoin = data.reduce((acc, curr) => acc + curr.total_poin, 0);
    
    $('#totalDosen').text(totalDosen + ' Dosen');
    $('#totalKegiatan').text(totalKegiatan + ' Kegiatan');
    $('#totalPoin').text(totalPoin + ' Poin');
}

// Load data detail untuk modal
function loadDetailData() {
    const period = $('#periodFilter').val();
    $.ajax({
        url: "{{ route('kaprodi.beban-kerja.detail.data') }}",
        data: { 
            period: period,
            _token: "{{ csrf_token() }}"
        },
        method: 'GET',
        beforeSend: function() {
            $('#detailTable tbody').html(`
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </td>
                </tr>
            `);
        },
        success: function(response) {
            if(response.status === 'success') {
                updateDetailTable(response.data);
            }
        },
        error: function(xhr) {
            $('#detailTable tbody').html(`
                <tr>
                    <td colspan="10" class="text-center text-danger">
                        Gagal memuat data detail
                    </td>
                </tr>
            `);
            console.error('Error:', xhr);
        }
    });
}

// Update tabel detail
function updateDetailTable(data) {
    const tbody = $('#detailTable tbody');
    tbody.empty();

    data.forEach((item, index) => {
        tbody.append(`
            <tr>
                <td>${index + 1}</td>
                <td>${item.nama}</td>
                <td>${item.nidn}</td>
                <td>${item.program_studi}</td>
                <td>${formatNumber(item.total_poin.jurusan)}</td>
                <td>${formatNumber(item.total_poin.prodi)}</td>
                <td>${formatNumber(item.total_poin.institusi)}</td>
                <td>${formatNumber(item.total_poin.luar_institusi)}</td>
                <td>${formatNumber(item.total_keseluruhan)}</td>
                <td>${index + 1}</td>
            </tr>
        `);
    });
}

// Format angka dengan 2 desimal
function formatNumber(number) {
    return number.toFixed(2);
}

// Event handlers
$(document).ready(function() {
    // Inisialisasi
    initChart();
    loadStatistikData();

    // Event untuk filter periode
    $('#periodFilter').change(function() {
        loadStatistikData();
        if($('#detailModal').is(':visible')) {
            loadDetailData();
        }
    });

    // Event untuk modal detail
    $('#detailModal').on('show.bs.modal', function() {
        loadDetailData();
    });

    // Event untuk tombol download
    $('#btnExcel').click(function() {
        const period = $('#periodFilter').val();
        window.location.href = `{{ route('kaprodi.beban-kerja.excel') }}?period=${period}`;
    });

    $('#btnPDF').click(function() {
        const period = $('#periodFilter').val();
        window.location.href = `{{ route('kaprodi.beban-kerja.pdf') }}?period=${period}`;
    });
});

// SweetAlert untuk konfirmasi download
function confirmDownload(type) {
    Swal.fire({
        title: 'Download ' + type,
        text: "Apakah Anda yakin ingin mengunduh data?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Download!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const period = $('#periodFilter').val();
            const route = type === 'Excel' ? 
                "{{ route('kaprodi.beban-kerja.excel') }}" : 
                "{{ route('kaprodi.beban-kerja.pdf') }}";
            
            window.location.href = `${route}?period=${period}`;
        }
    });
}
</script>
@endpush