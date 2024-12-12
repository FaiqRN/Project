@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header" style="background-color: #03346E; color: white;">
            <h3 class="card-title">Unggah Dokumen Akhir</h3>
        </div>
        <div class="card-body">
            <table id="dokumenTable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="30%">Nama Kegiatan</th>
                        <th width="20%">Tanggal Mulai</th>
                        <th width="20%">Tanggal Selesai</th>
                        <th width="15%">Status</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Dokumen Akhir</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="kegiatan_id" id="kegiatan_id">
                    <input type="hidden" name="tipe_kegiatan" id="tipe_kegiatan">
                    <div class="form-group">
                        <label>File Dokumen (PDF, max 20MB)</label>
                        <input type="file" class="form-control" name="file_akhir" accept=".pdf" required>
                        <small class="form-text text-muted">Format file harus PDF dengan ukuran maksimal 20MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .status-badge {
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
        text-align: center;
        min-width: 100px;
    }
    .status-berlangsung {
        background-color: #ffc107;
        color: #000;
    }
    .status-selesai {
        background-color: #28a745;
        color: #fff;
    }
    .status-tahap_penyelesaian {
        background-color: #17a2b8;
        color: #fff;
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    const table = $('#dokumenTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route("pic.unggah-dokumen.list") }}',
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                Swal.fire('Error', 'Gagal memuat data', 'error');
            }
        },
        columns: [
            {data: 'nama_kegiatan'},
            {
                data: 'tanggal_mulai',
                render: function(data) {
                    return data ? moment(data).format('DD/MM/YYYY') : '-';
                }
            },
            {
                data: 'tanggal_selesai',
                render: function(data) {
                    return data ? moment(data).format('DD/MM/YYYY') : '-';
                }
            },
            {
                data: 'status',
                render: function(data) {
                    const status = data.toLowerCase().replace(' ', '_');
                    return `<span class="status-badge status-${status}">${data}</span>`;
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data) {
                    if (data.status.toLowerCase() === 'tahap penyelesaian') {
                        return `
                            <button class="btn btn-primary btn-sm upload-btn" 
                                    data-id="${data.id}" 
                                    data-tipe="${data.tipe_kegiatan}">
                                <i class="fas fa-upload"></i> Upload
                            </button>
                        `;
                    }
                    return '-';
                }
            }
        ],
        language: {
            emptyTable: 'Tidak ada data kegiatan',
            zeroRecords: 'Tidak ada data yang sesuai pencarian',
            processing: 'Memuat data...'
        },
        order: [[1, 'desc']]
    });

    $('#dokumenTable').on('click', '.upload-btn', function() {
        const id = $(this).data('id');
        const tipe = $(this).data('tipe');
        $('#kegiatan_id').val(id);
        $('#tipe_kegiatan').val(tipe);
        $('#uploadModal').modal('show');
    });

    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'Memproses...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '{{ route("pic.unggah-dokumen.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#uploadModal').modal('hide');
                $('#uploadForm')[0].reset();
                table.ajax.reload();
                Swal.fire('Sukses', 'Dokumen berhasil diunggah', 'success');
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan saat mengunggah dokumen';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                Swal.fire('Error', message, 'error');
            }
        });
    });

    // Reset form saat modal ditutup
    $('#uploadModal').on('hidden.bs.modal', function() {
        $('#uploadForm')[0].reset();
    });
});
</script>
@endpush