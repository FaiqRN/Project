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
                    @if($agendas->isEmpty())
                        <div class="alert alert-info">
                            Tidak ada agenda yang perlu diupdate.
                        </div>
                    @else
                        <!-- Tabel Agenda -->
                        <table id="agenda-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Nama Agenda</th>
                                    <th width="25%">Kegiatan</th>
                                    <th width="15%">Tanggal</th>
                                    <th width="15%">Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agendas as $index => $agenda)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $agenda->nama_agenda }}</td>
                                    <td>
                                        @if($agenda->kegiatanJurusan)
                                            {{ $agenda->kegiatanJurusan->nama_kegiatan_jurusan }}
                                        @elseif($agenda->kegiatanProgramStudi)
                                            {{ $agenda->kegiatanProgramStudi->nama_kegiatan_program_studi }}
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d-m-Y') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $agenda->display_status === 'selesai' ? 'success' : ($agenda->display_status === 'tahap penyelesaian' ? 'warning' : 'info') }}">
                                            {{ ucfirst($agenda->display_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-sm btn-kirim" 
                                                data-id="{{ $agenda->agenda_id }}"
                                                data-kegiatan="{{ $agenda->kegiatanJurusan ? $agenda->kegiatanJurusan->nama_kegiatan_jurusan : ($agenda->kegiatanProgramStudi ? $agenda->kegiatanProgramStudi->nama_kegiatan_program_studi : '') }}"
                                                data-agenda="{{ $agenda->nama_agenda }}"
                                                data-tanggal="{{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d-m-Y') }}"
                                                {{ $agenda->hasUploaded ? 'disabled' : '' }}>
                                            {{ $agenda->hasUploaded ? 'Selesai' : 'Update' }}
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Form Upload -->
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

                            <form id="uploadForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="agenda_id" name="agenda_id">
                                <div class="card mt-4">
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>Nama dokumentasi: <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="nama_dokumentasi" required>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="form-group mt-3">
                                            <label>Deskripsi dokumentasi: <span class="text-danger">*</span></label>
                                            <textarea class="form-control" name="deskripsi_dokumentasi" rows="3" required></textarea>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="form-group mt-3">
                                            <label>Tanggal: <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="tanggal" 
                                                   value="{{ date('Y-m-d') }}" readonly>
                                            <div class="invalid-feedback"></div>
                                        </div>

                                        <div class="upload-area mt-4">
                                            <div class="text-center p-4 upload-box" 
                                                 style="border: 2px dashed #dee2e6; border-radius: 4px;">
                                                <h5 class="mb-3">UNGGAH DOKUMEN</h5>
                                                <p class="text-muted mb-3">
                                                    Format file: JPEG, PNG, PDF, DOC, DOCX (Maks. 10MB)
                                                </p>
                                                <input type="file" id="file-upload" name="file_dokumentasi" 
                                                       accept=".jpeg,.jpg,.png,.pdf,.doc,.docx"
                                                       style="display: none;" required>
                                                <label for="file-upload" class="btn btn-outline-primary mb-0">
                                                    Pilih File
                                                </label>
                                                <div id="selected-file-name" class="mt-2"></div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>

                                        <div class="mt-4">
                                            <button type="submit" class="btn btn-primary btn-block">
                                                SIMPAN PROGRES
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
.btn-block {
    width: 100%;
}
.card {
    border: 1px solid #dee2e6;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
.table-borderless td {
    padding: 0.5rem;
}
.upload-area {
    background: #f8f9fa;
}
.upload-box {
    transition: all 0.3s ease;
}
.upload-box.is-invalid {
    border-color: #dc3545;
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
    margin-top: 10px;
}
.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 80%;
    color: #dc3545;
}
.is-invalid ~ .invalid-feedback {
    display: block;
}
.form-control.is-invalid {
    border-color: #dc3545;
}
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    $('#agenda-table').DataTable({
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Handle klik tombol Update
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
        
        // Konversi format tanggal dari dd-mm-yyyy ke yyyy-mm-dd untuk input date
        const [day, month, year] = tanggal.split('-');
        $('#tanggal').val(`${year}-${month}-${day}`);
        
        // Reset form dan feedback
        $('#uploadForm')[0].reset();
        $('#selected-file-name').text('');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        // Set kembali tanggal setelah reset form
        $('#tanggal').val(`${year}-${month}-${day}`);
        
        // Tampilkan section detail
        $('#informasi-agenda').slideDown();
        
        // Scroll ke detail
        $('html, body').animate({
            scrollTop: $("#informasi-agenda").offset().top - 20
        }, 500);
    });

    // Handle perubahan file
    $('#file-upload').change(function() {
        const file = this.files[0];
        const fileName = file ? file.name : '';
        const fileSize = file ? (file.size / 1024 / 1024).toFixed(2) : 0;
        let fileInfo = '';
        
        if (file) {
            fileInfo = `${fileName} (${fileSize}MB)`;
            
            // Validasi ukuran file
            if (fileSize > 10) {
                fileInfo += ' - File terlalu besar (max 10MB)';
                $(this).val('');
                $('.upload-box').addClass('is-invalid');
                $('.upload-box').siblings('.invalid-feedback').text('Ukuran file maksimal 10MB');
                return;
            }
            
            // Validasi tipe file
            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowedTypes.includes(file.type)) {
                fileInfo += ' - Tipe file tidak didukung';
                $(this).val('');
                $('.upload-box').addClass('is-invalid');
                $('.upload-box').siblings('.invalid-feedback').text('Format file harus JPEG, PNG, PDF, DOC, atau DOCX');
                return;
            }
            
            $('.upload-box').removeClass('is-invalid');
        } else {
            fileInfo = 'Tidak ada file dipilih';
        }
        
        $('#selected-file-name').text(fileInfo);
    });

    // Handle submit form
    $('#uploadForm').submit(function(e) {
        e.preventDefault();
        
        // Reset validasi
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        const formData = new FormData(this);
        const agendaId = $('#agenda_id').val();
        
        $.ajax({
            url: `/dosen/update-progress/${agendaId}/update`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                // Disable submit button
                $('button[type="submit"]').prop('disabled', true);
                
                // Tampilkan loading
                Swal.fire({
                    title: 'Mohon Tunggu',
                    text: 'Sedang mengupload dokumentasi...',
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
                    text: response.message
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                // Enable submit button
                $('button[type="submit"]').prop('disabled', false);
                
                if (xhr.status === 422) {
                    // Validation errors
                    const errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        const input = $(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.siblings('.invalid-feedback').text(errors[key][0]);
                        
                        if (key === 'file_dokumentasi') {
                            $('.upload-box').addClass('is-invalid');
                            $('.upload-box').siblings('.invalid-feedback').text(errors[key][0]);
                        }
                    });
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Validasi Gagal',
                        text: 'Mohon periksa kembali input Anda'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: xhr.responseJSON?.message || 'Terjadi kesalahan saat mengupload dokumentasi'
                    });
                }
            }
        });
    });
});
</script>
@endpush