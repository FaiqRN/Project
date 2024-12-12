

@extends('layouts.template')


@section('content')
<div class="container-fluid">
    <!-- Card Utama -->
    <div class="card">
        <div class="card-header" style="background-color: #03346E; color: white;">
            <div class="row align-items-center">
                <div class="col">
                    <h3 class="card-title">Unggah Dokumen Akhir</h3>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info" role="alert">
                <i class="fas fa-info-circle"></i> Hanya kegiatan dengan status "Selesai" yang dapat mengunggah dokumen akhir.
            </div>
            <div class="table-responsive">
                <table id="dokumenTable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th width="30%">Nama Kegiatan</th>
                            <th width="15%">Tanggal Mulai</th>
                            <th width="15%">Tanggal Selesai</th>
                            <th width="15%">Status</th>
                            <th width="10%">Dokumen</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
                   
                    <!-- Informasi Kegiatan -->
                    <div class="form-group">
                        <label class="font-weight-bold">Nama Kegiatan</label>
                        <p id="modalNamaKegiatan" class="text-primary"></p>
                    </div>
                   
                    <!-- Upload File -->
                    <div class="form-group">
                        <label class="font-weight-bold">File Dokumen Akhir</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="file_akhir" accept=".pdf" required>
                            <label class="custom-file-label" for="file_akhir">Pilih file</label>
                        </div>
                        <small class="form-text text-muted">
                            <ul class="pl-3 mb-0">
                                <li>Format file harus PDF</li>
                                <li>Ukuran maksimal 20MB</li>
                                <li>File baru akan menggantikan file lama jika sudah ada</li>
                            </ul>
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload
                    </button>
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
        font-weight: 500;
    }
    .status-selesai {
        background-color: #28a745;
        color: #fff;
    }
    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(0,0,0,.1);
        border-radius: 50%;
        border-top-color: #03346E;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endpush


@push('js')
<script>
$(document).ready(function() {
    // Setup AJAX CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });


    // Initialize DataTable
    const table = $('#dokumenTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route("pic.unggah-dokumen.list") }}',
            type: 'GET',
            error: function(xhr, error, thrown) {
                console.error('Error:', error);
                console.error('Response:', xhr.responseText);
                Swal.fire('Error', 'Gagal memuat data kegiatan', 'error');
            }
        },
        columns: [
            {
                data: 'nama_kegiatan',
                name: 'nama_kegiatan'
            },
            {
                data: 'tanggal_mulai',
                render: function(data) {
                    return moment(data).format('DD/MM/YYYY');
                }
            },
            {
                data: 'tanggal_selesai',
                render: function(data) {
                    return moment(data).format('DD/MM/YYYY');
                }
            },
            {
                data: 'status',
                render: function(data) {
                    return `<span class="status-badge status-${data.toLowerCase()}">${data}</span>`;
                }
            },
            {
                data: 'has_document',
                render: function(data, type, row) {
                    if (data) {
                        return `
                            <a href="{{ url('pic/unggah-dokumen/download') }}/${row.id}/${row.tipe_kegiatan}"
                               class="btn btn-info btn-sm">
                                <i class="fas fa-download"></i> Download
                            </a>`;
                    }
                    return '<span class="badge badge-secondary">Belum ada</span>';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data) {
                    return `
                        <button class="btn btn-primary btn-sm upload-btn"
                                data-id="${data.id}"
                                data-tipe="${data.tipe_kegiatan}"
                                data-nama="${data.nama_kegiatan}">
                            <i class="fas fa-upload"></i> Upload
                        </button>`;
                }
            }
        ],
        order: [[1, 'desc']],
        language: {
            processing: '<div class="loading-spinner"></div>',
            emptyTable: 'Tidak ada kegiatan yang telah selesai',
            zeroRecords: 'Tidak ada kegiatan yang sesuai pencarian'
        }
    });


    // Handle click Upload button
    $('#dokumenTable').on('click', '.upload-btn', function() {
        const id = $(this).data('id');
        const tipe = $(this).data('tipe');
        const nama = $(this).data('nama');
       
        $('#kegiatan_id').val(id);
        $('#tipe_kegiatan').val(tipe);
        $('#modalNamaKegiatan').text(nama);
        $('#uploadModal').modal('show');
    });


    // Handle file input change
    $('input[type="file"]').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').html(fileName);
    });


    // Handle form submission
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
       
        const formData = new FormData(this);
       
        Swal.fire({
            title: 'Mengupload Dokumen',
            text: 'Mohon tunggu...',
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
                $('.custom-file-label').html('Pilih file');
                table.ajax.reload();
               
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                });
            },
            error: function(xhr) {
                let message = 'Terjadi kesalahan saat mengunggah dokumen';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
               
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            }
        });
    });


    // Reset form when modal is closed
    $('#uploadModal').on('hidden.bs.modal', function() {
        $('#uploadForm')[0].reset();
        $('.custom-file-label').html('Pilih file');
    });
});
</script>
@endpush



