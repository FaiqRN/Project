@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Persetujuan Poin Kegiatan</h3>
        </div>
        <div class="card-body">
            <!-- Tabel Data Poin -->
            <div class="table-responsive">
                <table id="tabel-poin" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Nama Kegiatan</th>
                            <th width="10%">Tipe Kegiatan</th>
                            <th width="15%">Nama Anggota</th>
                            <th width="10%">Jabatan</th>
                            <th width="8%">Poin Dasar</th>
                            <th width="8%">Poin Tambahan</th>
                            <th width="8%">Total</th>
                            <th width="8%">Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modal-detail">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title">Detail Pengajuan Poin</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Nama Kegiatan:</label>
                    <p id="detail-kegiatan" class="font-weight-bold"></p>
                </div>
                <div class="form-group">
                    <label>Nama Anggota:</label>
                    <p id="detail-anggota" class="font-weight-bold"></p>
                </div>
                <div class="form-group">
                    <label>Jabatan:</label>
                    <p id="detail-jabatan" class="font-weight-bold"></p>
                </div>
                <div class="form-group">
                    <label>Poin Dasar:</label>
                    <p id="detail-poin-dasar" class="font-weight-bold"></p>
                </div>
                <div class="form-group">
                    <label>Poin Tambahan:</label>
                    <p id="detail-poin-tambahan" class="font-weight-bold"></p>
                </div>
                <div class="form-group">
                    <label>Keterangan:</label>
                    <p id="detail-keterangan" class="text-muted"></p>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <div>
                    <button type="button" class="btn btn-danger btn-tolak">
                        <i class="fas fa-times"></i> Tolak
                    </button>
                    <button type="button" class="btn btn-success btn-setuju">
                        <i class="fas fa-check"></i> Setujui
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .table th, .table td {
        vertical-align: middle !important;
    }
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.8em;
    }
    .btn {
        margin: 2px;
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    const TIPE_KEGIATAN_LABELS = {
        'jurusan': 'Jurusan',
        'prodi': 'Program Studi',
        'institusi': 'Institusi',
        'luar_institusi': 'Luar Institusi'
    };

    let selectedPoin = null;

    let table = $('#tabel-poin').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route("admin.dosen.agenda.persetujuan-poin.data") }}',
            dataSrc: 'data'
        },
        columns: [
            {
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'nama_kegiatan' },
            { 
                data: 'tipe_poin',
                render: function(data) {
                    return TIPE_KEGIATAN_LABELS[data] || data;
                }
            },
            { data: 'nama_anggota' },
            { data: 'jabatan' },
            { 
                data: 'poin_dasar',
                render: function(data) {
                    return parseFloat(data).toFixed(1);
                }
            },
            { 
                data: 'poin_tambahan',
                render: function(data) {
                    return parseFloat(data || 0).toFixed(1);
                }
            },
            { 
                data: 'total_poin',
                render: function(data) {
                    return parseFloat(data).toFixed(1);
                }
            },
            {
                data: 'status',
                render: function(data) {
                    switch(data) {
                        case 'pending':
                            return '<span class="badge badge-warning">Pending</span>';
                        case 'disetujui':
                            return '<span class="badge badge-success">Disetujui</span>';
                        case 'ditolak':
                            return '<span class="badge badge-danger">Ditolak</span>';
                        default:
                            return '<span class="badge badge-secondary">-</span>';
                    }
                }
            },
            {
                data: null,
                render: function(data) {
                    if (data.status === 'pending') {
                        let buttons = '';
                        buttons += `
                            <button class="btn btn-sm btn-success btn-approve" data-id="${data.id}" data-tipe="${data.tipe_poin}">
                                <i class="fas fa-check"></i> Setuju
                            </button>
                            <button class="btn btn-sm btn-danger btn-reject" data-id="${data.id}" data-tipe="${data.tipe_poin}">
                                <i class="fas fa-times"></i> Tolak
                            </button>
                            <button class="btn btn-sm btn-info btn-detail" 
                                data-kegiatan="${data.nama_kegiatan}"
                                data-anggota="${data.nama_anggota}"
                                data-jabatan="${data.jabatan}"
                                data-poindasar="${data.poin_dasar}"
                                data-pointambahan="${data.poin_tambahan}"
                                data-keterangan="${data.keterangan}">
                                <i class="fas fa-info-circle"></i> Detail
                            </button>
                        `;
                        return buttons;
                    }
                    return '-';
                }
            }
        ],
        order: [[1, 'asc']],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Event handler untuk tombol detail
    $('#tabel-poin').on('click', '.btn-detail', function() {
        const button = $(this);
        $('#detail-kegiatan').text(button.data('kegiatan'));
        $('#detail-anggota').text(button.data('anggota'));
        $('#detail-jabatan').text(button.data('jabatan'));
        $('#detail-poin-dasar').text(parseFloat(button.data('poindasar')).toFixed(1));
        $('#detail-poin-tambahan').text(parseFloat(button.data('pointambahan')).toFixed(1));
        $('#detail-keterangan').text(button.data('keterangan'));
        $('#modal-detail').modal('show');
    });

    // Event handler untuk tombol setujui langsung dari tabel
    $('#tabel-poin').on('click', '.btn-approve', function() {
        const id = $(this).data('id');
        const tipe = $(this).data('tipe');
        
        Swal.fire({
            title: 'Konfirmasi Persetujuan',
            text: "Apakah Anda yakin akan menyetujui penambahan poin ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Setujui',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                updateStatus(id, tipe, 'disetujui');
            }
        });
    });

    // Event handler untuk tombol tolak langsung dari tabel
    $('#tabel-poin').on('click', '.btn-reject', function() {
        const id = $(this).data('id');
        const tipe = $(this).data('tipe');
        
        Swal.fire({
            title: 'Konfirmasi Penolakan',
            text: "Apakah Anda yakin akan menolak penambahan poin ini?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Tolak',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                updateStatus(id, tipe, 'ditolak');
            }
        });
    });

    function updateStatus(id, tipePoin, status) {
        $.ajax({
            url: '{{ route("admin.dosen.agenda.persetujuan-poin.update-status") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                id: id,
                tipe_poin: tipePoin,
                status: status
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                }).then(() => {
                    table.ajax.reload();
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                });
            }
        });
    }

    // Refresh data setiap 30 detik
    setInterval(function() {
        table.ajax.reload(null, false);
    }, 30000);
});
</script>
@endpush