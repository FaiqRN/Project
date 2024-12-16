@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">Detail Keseluruhan Beban Kerja</h3>
                        <div class="d-flex gap-2">
                            <select id="periodFilter" class="form-control">
                                <option value="Januari - Juni 2024">Januari - Juni 2024</option>
                                <option value="Juli - Desember 2023">Juli - Desember 2023</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Info Boxes -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-users"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Dosen</span>
                                    <span class="info-box-number">{{ $totalDosen }} Dosen</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-tasks"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Kegiatan</span>
                                    <span class="info-box-number">{{ $totalKegiatan }} Kegiatan</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-star"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Poin</span>
                                    <span class="info-box-number">{{ $totalPoin }} Poin</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Export Buttons -->
                    <div class="d-flex justify-content-end mb-3">
                        <button class="btn btn-danger mr-2" onclick="exportPDF()">
                            <i class="fas fa-file-pdf"></i> PDF
                        </button>
                        <button class="btn btn-success" onclick="exportExcel()">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </div>

                    <!-- Detail Table -->
                    <div class="table-responsive">
                        <table id="detailTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Nama Dosen</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Jenis Kegiatan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Poin JTI</th>
                                    <th>Poin Non-JTI</th>
                                    <th>Total Keseluruhan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    
<script>
    $(document).ready(function() {
        let detailTable;

        // Tombol Lihat Detail
        $('#btnDetail').on('click', function() {
            $('#detailSection').removeClass('d-none');
            loadDetailTable();
        });

        $('#btnKembali').on('click', function() {
            $('#detailSection').addClass('d-none');
        });

        function loadDetailTable() {
            if (!detailTable) {
                detailTable = $('#detailTable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: '{{ route("kaprodi.beban-kerja.detail.data") }}',
                    columns: [
                        { data: 'nama_dosen' },
                        { data: 'nama_kegiatan' },
                        { data: 'jenis_kegiatan' },
                        { data: 'tanggal' },
                        { data: 'status', render: function(data) {
                            return `<span class="badge badge-success">${data}</span>`;
                        }},
                        { data: 'poin_jti' },
                        { data: 'poin_non_jti' },
                        { data: 'total_poin' }
                    ]
                });
            } else {
                detailTable.ajax.reload();
            }
        }

        function exportPDF() {
            window.location.href = '{{ route("kaprodi.beban-kerja.pdf") }}';
        }

        function exportExcel() {
            window.location.href = '{{ route("kaprodi.beban-kerja.excel") }}';
        }
    });
</script>
@endpush