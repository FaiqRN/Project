@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Update Progress Agenda</h3>
        </div>

        <div class="card-body">
            <!-- Tabel Agenda -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="40%">Nama Agenda</th>
                            <th width="20%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agendas as $index => $agenda)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $agenda->nama_agenda }}</td>
                            <td>
                                <span class="badge badge-warning">{{ ucfirst($agenda->status_agenda) }}</span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-primary btn-sm btn-kirim" 
                                        data-id="{{ $agenda->agenda_id }}">
                                    Kirim
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Form Upload (Initially Hidden) -->
            <div id="uploadSection" style="display: none;">
                <!-- Informasi Agenda Box -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Agenda</h5>
                    </div>
                    <div class="card-body" id="infoAgenda">
                        <!-- Diisi via JavaScript -->
                    </div>
                </div>

                <!-- Form Upload -->
                <form id="formUpload" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama dokumentasi:</label>
                        <input type="text" class="form-control" name="nama_dokumentasi" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi dokumentasi:</label>
                        <textarea class="form-control" name="deskripsi_dokumentasi" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Tanggal:</label>
                        <input type="date" class="form-control" name="tanggal" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <!-- Upload Area -->
                    <div class="upload-area mb-3">
                        <div class="card">
                            <div class="card-body text-center">
                                <div class="upload-icon mb-2">
                                    <i class="fas fa-upload fa-2x"></i>
                                </div>
                                <h5>UNGGAH DOKUMEN</h5>
                                <input type="file" class="form-control-file" name="file_dokumentasi" id="file_dokumentasi" required 
                                       style="opacity: 0; position: absolute;">
                                <label for="file_dokumentasi" class="btn btn-outline-primary">
                                    Pilih File
                                </label>
                                <div id="selected-file" class="mt-2"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary btn-block">
                        SIMPAN PROGRESS
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    let selectedAgendaId = null;

    // Handle click pada tombol Kirim
    $('.btn-kirim').click(function() {
        selectedAgendaId = $(this).data('id');
        
        // Ambil detail agenda
        $.get(`/dosen/update-progress/${selectedAgendaId}/detail`, function(data) {
            // Tampilkan informasi agenda
            $('#infoAgenda').html(`
                <div class="row">
                    <div class="col-md-12">
                        <p class="mb-2"><strong>Nama kegiatan:</strong> ${data.kegiatan_jurusan ? 
                            data.kegiatan_jurusan.nama_kegiatan_jurusan : 
                            data.kegiatan_program_studi.nama_kegiatan_program_studi}</p>
                        <p class="mb-2"><strong>Nama agenda:</strong> ${data.nama_agenda}</p>
                        <p class="mb-0"><strong>Tanggal:</strong> ${data.tanggal_agenda}</p>
                    </div>
                </div>
            `);
            
            // Tampilkan form upload
            $('#uploadSection').slideDown();
            
            // Scroll ke form upload
            $('html, body').animate({
                scrollTop: $("#uploadSection").offset().top - 100
            }, 500);
        });
    });

    // Handle file input change
    $('#file_dokumentasi').change(function() {
        let fileName = $(this).val().split('\\').pop();
        $('#selected-file').text(fileName || 'Tidak ada file dipilih');
    });

    // Handle form submission
    $('#formUpload').submit(function(e) {
        e.preventDefault();
        
        let formData = new FormData(this);
        
        $.ajax({
            url: `/dosen/update-progress/${selectedAgendaId}/update`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Mohon Tunggu',
                    text: 'Sedang memproses...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                }).then(() => {
                    window.location.reload();
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
});
</script>

<style>
.upload-area {
    border: 2px dashed #ddd;
    padding: 20px;
    text-align: center;
    background: #f8f9fa;
    border-radius: 4px;
}

.upload-area:hover {
    border-color: #aaa;
}

.upload-icon {
    color: #6c757d;
}

#selected-file {
    color: #6c757d;
    font-style: italic;
}
</style>
@endpush