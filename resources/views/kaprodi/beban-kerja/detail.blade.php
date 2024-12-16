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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    let table;
    
    $(document).ready(function() {
        table = $('#detailTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("kaprodi.beban-kerja.detail.data") }}',
                data: function(d) {
                    d.period = $('#periodFilter').val();
                }
            },
            columns: [
                {data: 'nama_dosen', name: 'nama_dosen'},
                {data: 'nama_kegiatan', name: 'nama_kegiatan'},
                {data: 'jenis_kegiatan', name: 'jenis_kegiatan'},
                {data: 'tanggal', name: 'tanggal'},
                {data: 'status', name: 'status'},
                {data: 'poin_jti', name: 'poin_jti'},
                {data: 'poin_non_jti', name: 'poin_non_jti'},
                {data: 'total_poin', name: 'total_poin'}
            ]
        });

        $('#periodFilter').change(function() {
            table.ajax.reload();
        });
    });

    function exportPDF() {
        window.location.href = '{{ route("kaprodi.beban-kerja.pdf") }}?period=' + $('#periodFilter').val();
    }

    function exportExcel() {
        window.location.href = '{{ route("kaprodi.beban-kerja.excel") }}?period=' + $('#periodFilter').val();
    }
</script>
@endpush