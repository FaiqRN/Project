@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Update Progress Agenda</h3>
                </div>
                
                <div class="card-body">
                    <!-- Tabel Agenda -->
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="40%">Nama Agenda</th>
                                <th width="25%">Status</th>
                                <th width="25%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Rapat ABC</td>
                                <td>
                                    <span class="badge badge-warning">Berlangsung</span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-sm btn-kirim" 
                                            data-id="1"
                                            data-kegiatan="Kegiatan ABC"
                                            data-agenda="Rapat ABC"
                                            data-tanggal="12-12-2024">
                                        Kirim
                                    </button>
                                </td>
                            </tr>
                            <!-- Baris tabel lainnya... -->
                        </tbody>
                    </table>

                    <!-- Informasi Agenda dan Form Upload -->
                    <div id="informasi-agenda" style="display: none;" class="mt-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Informasi Agenda</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td width="200">Nama Kegiatan</td>
                                            <td width="10">:</td>
                                            <td id="detail-kegiatan"></td>
                                        </tr>
                                        <tr>
                                            <td>Nama Agenda</td>
                                            <td>:</td>
                                            <td id="detail-agenda"></td>
                                        </tr>
                                        <tr>
                                            <td>Tanggal</td>
                                            <td>:</td>
                                            <td id="detail-tanggal"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Form Upload -->
                        <form id="uploadForm" enctype="multipart/form-data">
                            <input type="hidden" id="agenda_id" name="agenda_id">
                            <div class="card mt-4">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Nama dokumentasi:</label>
                                        <input type="text" class="form-control" name="nama_dokumentasi" required>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label>Deskripsi dokumentasi:</label>
                                        <textarea class="form-control" name="deskripsi_dokumentasi" rows="3" required></textarea>
                                    </div>

                                    <div class="form-group mt-3">
                                        <label>Tanggal:</label>
                                        <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required>
                                    </div>

                                    <!-- Upload Area -->
                                    <div class="upload-area mt-4">
                                        <div class="text-center p-4" style="border: 2px dashed #dee2e6; border-radius: 4px;">
                                            <h5 class="mb-3">UNGGAH DOKUMEN</h5>
                                            <input type="file" id="file-upload" name="file_dokumentasi" 
                                                   style="display: none;" required>
                                            <label for="file-upload" class="btn btn-outline-primary">
                                                Pilih File
                                            </label>
                                            <div id="selected-file-name" class="mt-2"></div>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <div class="mt-4">
                                        <button type="submit" class="btn btn-primary btn-block">
                                            SIMPAN PROGRES
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
$(document).ready(function() {
    // Handle klik tombol Kirim
    $('.btn-kirim').click(function() {
        const id = $(this).data('id');
        const kegiatan = $(this).data('kegiatan');
        const agenda = $(this).data('agenda');
        const tanggal = $(this).data('tanggal');
        
        // Update informasi detail
        $('#detail-kegiatan').text(kegiatan);
        $('#detail-agenda').text(agenda);
        $('#detail-tanggal').text(tanggal);
        $('#agenda_id').val(id);
        
        // Reset form
        $('#uploadForm')[0].reset();
        $('#selected-file-name').text('');
        
        // Tampilkan section detail
        $('#informasi-agenda').slideDown();
        
        // Scroll ke detail
        $('html, body').animate({
            scrollTop: $("#informasi-agenda").offset().top - 20
        }, 500);
    });

    // Handle perubahan file
    $('#file-upload').change(function() {
        const fileName = $(this).val().split('\\').pop();
        $('#selected-file-name').text(fileName || 'Tidak ada file dipilih');
    });

    // Handle submit form
    $('#uploadForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '/dosen/update-progress/update/' + $('#agenda_id').val(),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                // Tampilkan loading
                Swal.fire({
                    title: 'Mohon Tunggu',
                    text: 'Sedang memproses...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading()
                    }
                });
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Dokumentasi berhasil disimpan'
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan'
                });
            }
        });
    });
});
</script>
@endpush

@push('css')
<style>
.btn-block {
    width: 100%;
}
.card {
    border: 1px solid #dee2e6;
}
.table-borderless td {
    padding: 0.5rem;
}
.upload-area {
    background: #f8f9fa;
}
.badge {
    padding: 8px 12px;
    font-size: 0.9em;
}
.badge-warning {
    background-color: #ffc107;
    color: #000;
}
.badge-success {
    background-color: #28a745;
    color: #fff;
}
#selected-file-name {
    color: #6c757d;
    font-style: italic;
}
</style>
@endpush
@endsection