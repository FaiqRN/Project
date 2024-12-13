@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-primary text-white">
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
<div class="modal fade" id="modalTambahPoin" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Poin</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formTambahPoin">
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
                        <small class="form-text text-muted">Masukkan poin tambahan antara 1-3</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Keterangan Tambahan</label>
                        <textarea class="form-control" name="keterangan_tambahan" rows="3" required 
                                 minlength="10" placeholder="Minimal 10 karakter"></textarea>
                        <small class="form-text text-muted">Berikan keterangan mengenai alasan penambahan poin</small>
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

@push('js')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

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
            { data: 'poin_tambahan' },
            { data: 'total_poin' },
            { 
                data: 'status_poin_tambahan',
                render: function(data) {
                    if (!data || data === '-') return '-';
                    const badges = {
                        'pending': 'warning',
                        'disetujui': 'success',
                        'ditolak': 'danger'
                    };
                    return `<span class="badge badge-${badges[data]}">${data}</span>`;
                }
            },
            { data: 'keterangan_tambahan' },
            {
                data: null,
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

    $('#poinTable').on('click', '.tambah-poin', function() {
        const data = table.row($(this).closest('tr')).data();
        $('#poin_id').val(data.id);
        $('#jenis_kegiatan').val(data.jenis);
        $('#nama_anggota').val(data.nama_user);
        $('#nama_kegiatan').val(data.nama_kegiatan);
        $('#modalTambahPoin').modal('show');
    });

    $('#formTambahPoin').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("pic.pembagian-poin.tambah") }}',
            type: 'POST',
            data: {
                id: formData.get('id'),
                jenis: formData.get('jenis'),
                poin_tambahan: formData.get('poin_tambahan'),
                keterangan_tambahan: formData.get('keterangan_tambahan')
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
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
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menambahkan poin'
                });
            }
        });
    });

    $('#modalTambahPoin').on('hidden.bs.modal', function() {
        $('#formTambahPoin')[0].reset();
    });
});
</script>
@endpush

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
</style>
@endpush