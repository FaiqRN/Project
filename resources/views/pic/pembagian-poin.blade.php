@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <!-- Detail Poin Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Penambahan Poin Anggota Kegiatan</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="poinTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Kegiatan</th>
                            <th>Nama Anggota</th>
                            <th>Nama Kegiatan</th>
                            <th>Jabatan</th>
                            <th>Status Kegiatan</th>
                            <th>Poin Dasar</th>
                            <th>Poin Tambahan</th>
                            <th>Total Poin</th>
                            <th>Status</th>
                            <th>Keterangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Poin -->
<div class="modal fade" id="modalTambahPoin">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Poin</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formTambahPoin" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="id" id="poin_id">
                    <input type="hidden" name="jenis" id="jenis_kegiatan">
                    
                    <div class="form-group">
                        <label>Nama Anggota</label>
                        <input type="text" class="form-control" id="nama_anggota" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Kegiatan</label>
                        <input type="text" class="form-control" id="nama_kegiatan" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Poin Tambahan</label>
                        <input type="number" class="form-control" name="poin_tambahan" min="1" max="3" required>
                        <small class="text-muted">Masukkan poin tambahan antara 1-3</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea class="form-control" name="keterangan_tambahan" rows="3" required minlength="10"></textarea>
                        <small class="text-muted">Minimal 10 karakter</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('css')
<style>
    .badge {
        padding: 0.4em 0.8em;
    }
    .badge-pending {
        background-color: #ffc107;
        color: #000;
    }
    .badge-disetujui {
        background-color: #28a745;
        color: #fff;
    }
    .badge-ditolak {
        background-color: #dc3545;
        color: #fff;
    }
    .table th, .table td {
        vertical-align: middle !important;
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    // Setup CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTable
    const table = $('#poinTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: '{{ route("pic.pembagian-poin.data") }}',
        columns: [
            { 
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + meta.settings._iDisplayStart + 1;
                }
            },
            { data: 'jenis' },
            { data: 'nama_user' },
            { data: 'nama_kegiatan' },
            { data: 'jabatan' },
            { 
                data: 'status_kegiatan',
                render: function(data) {
                    return `<span class="badge badge-${data === 'selesai' ? 'success' : 'warning'}">${data}</span>`;
                }
            },
            { data: 'poin_dasar' },
            { 
                data: 'poin_tambahan',
                render: function(data, type, row) {
                    return row.status_poin_tambahan === 'disetujui' ? data : '0';
                }
            },
            { 
                data: null,
                render: function(data, type, row) {
                    let total = row.poin_dasar;
                    if (row.status_poin_tambahan === 'disetujui') {
                        total += row.poin_tambahan;
                    }
                    return total;
                }
            },
            { 
                data: 'status_poin_tambahan',
                render: function(data) {
                    if (data === '-') return '-';
                    const badges = {
                        'pending': 'warning',
                        'disetujui': 'success',
                        'ditolak': 'danger'
                    };
                    let statusText = data;
                    if (data === 'pending') {
                        statusText = 'Menunggu Persetujuan';
                    }
                    return `<span class="badge badge-${badges[data]}">${statusText}</span>`;
                }
            },
            { data: 'keterangan_tambahan' },
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data) {
                    if (!data.can_add_points) return '';
                    return `
                        <button type="button" class="btn btn-primary btn-sm tambah-poin">
                            <i class="fas fa-plus"></i> Tambah Poin
                        </button>`;
                }
            }
        ]
    });

    // Handle Tambah Poin button
    $('#poinTable').on('click', '.tambah-poin', function() {
        const data = table.row($(this).closest('tr')).data();
        $('#poin_id').val(data.id);
        $('#jenis_kegiatan').val(data.jenis);
        $('#nama_anggota').val(data.nama_user);
        $('#nama_kegiatan').val(data.nama_kegiatan);
        $('#modalTambahPoin').modal('show');
    });

    // Handle form submission
    $('#formTambahPoin').on('submit', function(e) {
        e.preventDefault();
        
        if (!$('input[name="poin_tambahan"]').val() || !$('textarea[name="keterangan_tambahan"]').val()) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Error',
                text: 'Poin tambahan dan keterangan harus diisi'
            });
            return;
        }
        
        $.ajax({
            url: '{{ route("pic.pembagian-poin.tambah") }}',
            type: 'POST',
            data: {
                id: $('#poin_id').val(),
                jenis: $('#jenis_kegiatan').val(),
                poin_tambahan: $('input[name="poin_tambahan"]').val(),
                keterangan_tambahan: $('textarea[name="keterangan_tambahan"]').val()
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#modalTambahPoin').modal('hide');
                        table.ajax.reload();
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat menambahkan poin';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errorMessage
                });
            }
        });
    });

    // Reset form when modal is closed
    $('#modalTambahPoin').on('hidden.bs.modal', function() {
        $('#formTambahPoin')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    });
});
</script>
@endpush