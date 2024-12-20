@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <!-- Card Utama -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Penambahan Poin Kegiatan</h3>
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
                            <th width="10%">Status</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Poin -->
<div class="modal fade" id="modal-tambah-poin">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title">Tambah Poin</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-tambah-poin">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="jabatan_id" id="jabatan_id">
                    <input type="hidden" name="tipe_kegiatan" id="tipe_kegiatan">
                    
                    <div class="form-group">
                        <label>Nama Anggota</label>
                        <input type="text" class="form-control" id="nama_anggota" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Nama Kegiatan</label>
                        <input type="text" class="form-control" id="nama_kegiatan" readonly>
                    </div>

                    <div class="form-group">
                        <label>Tipe Kegiatan</label>
                        <input type="text" class="form-control" id="display_tipe_kegiatan" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Poin Tambahan (1-3)</label>
                        <input type="number" class="form-control" name="poin_tambahan" min="1" max="3" required>
                        <small class="form-text text-muted">Masukkan nilai antara 1-3 poin</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan Tambahan</label>
                        <textarea class="form-control" name="keterangan_tambahan" rows="3" required></textarea>
                        <small class="form-text text-muted">Berikan alasan penambahan poin</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Keterangan -->
<div class="modal fade" id="modal-detail">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title">Detail Keterangan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Keterangan Tambahan:</label>
                    <p id="detail-keterangan" class="text-muted"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
    .small-box {
        border-radius: 0.25rem;
        position: relative;
        display: block;
        margin-bottom: 20px;
        box-shadow: 0 1px 1px rgba(0,0,0,0.1);
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

    // Inisialisasi DataTable
    let table = $('#tabel-poin').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route("pic.pembagian-poin.data") }}',
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
                data: 'tipe_kegiatan',
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
                data: 'status_poin',
                render: function(data) {
                    switch(data) {
                        case 'pending':
                            return '<span class="badge badge-warning">Pending</span>';
                        case 'disetujui':
                            return '<span class="badge badge-success">Disetujui</span>';
                        case 'ditolak':
                            return '<span class="badge badge-danger">Ditolak</span>';
                        case 'belum_ada':
                            return '<span class="badge badge-secondary">Belum Ada Poin Tambahan</span>';
                        default:
                            return '<span class="badge badge-secondary">-</span>';
                    }
                }
            },
            {
                data: null,
                render: function(data) {
                    let buttons = '';
                    
                    if (data.can_add_points) {
                        buttons += `<button class="btn btn-sm btn-primary btn-tambah-poin" 
                            data-jabatan-id="${data.jabatan_id}"
                            data-nama="${data.nama_anggota}"
                            data-kegiatan="${data.nama_kegiatan}"
                            data-tipe="${data.tipe_kegiatan}">
                            <i class="fas fa-plus"></i> Tambah
                        </button>`;
                    }
                    
                    if (data.keterangan_tambahan) {
                        buttons += `<button class="btn btn-sm btn-info btn-detail" 
                            data-keterangan="${data.keterangan_tambahan}">
                            <i class="fas fa-info-circle"></i> Detail
                        </button>`;
                    }
                    
                    return buttons || '-';
                }
            }
        ],
        order: [[1, 'asc']],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Load ringkasan poin
    function loadRingkasanPoin() {
        $.get('{{ route("pic.pembagian-poin.ringkasan") }}', function(response) {
            $('#total-poin-jurusan').text(response.total_poin_jurusan.toFixed(1));
            $('#total-poin-prodi').text(response.total_poin_prodi.toFixed(1));
            $('#total-poin-institusi').text(response.total_poin_institusi.toFixed(1));
            $('#total-poin-luar-institusi').text(response.total_poin_luar_institusi.toFixed(1));
            $('#total-poin-keseluruhan').text(response.total_keseluruhan.toFixed(1));
        });
    }

    // Event handler untuk tombol tambah poin
    $('#tabel-poin').on('click', '.btn-tambah-poin', function() {
        let button = $(this);
        let tipeKegiatan = button.data('tipe');
        
        $('#jabatan_id').val(button.data('jabatan-id'));
        $('#tipe_kegiatan').val(tipeKegiatan);
        $('#nama_anggota').val(button.data('nama'));
        $('#nama_kegiatan').val(button.data('kegiatan'));
        $('#display_tipe_kegiatan').val(TIPE_KEGIATAN_LABELS[tipeKegiatan]);
        
        $('#modal-tambah-poin').modal('show');
    });

    // Event handler untuk tombol detail
    $('#tabel-poin').on('click', '.btn-detail', function() {
        $('#detail-keterangan').text($(this).data('keterangan'));
        $('#modal-detail').modal('show');
    });

    // Handle form submission
    $('#form-tambah-poin').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '{{ route("pic.pembagian-poin.tambah") }}',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                }).then(() => {
                    $('#modal-tambah-poin').modal('hide');
                    $('#form-tambah-poin')[0].reset();
                    table.ajax.reload();
                    loadRingkasanPoin();
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
    });

    // Load ringkasan poin saat halaman dimuat
    loadRingkasanPoin();

    // Refresh data setiap 30 detik
    setInterval(function() {
        table.ajax.reload(null, false);
        loadRingkasanPoin();
    }, 30000);
});
</script>
@endpush