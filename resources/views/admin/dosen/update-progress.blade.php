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
                            Tidak ada agenda yang tersedia.
                        </div>
                    @else
                        <!-- Tabel Agenda -->
                        <table id="agenda-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Nama Agenda</th>
                                    <th width="20%">Kegiatan</th>
                                    <th width="10%">Tanggal</th>
                                    <th width="15%">Progress</th>
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
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" 
                                                style="width: {{ $agenda->progress['percentage'] }}%"
                                                aria-valuenow="{{ $agenda->progress['percentage'] }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                                {{ $agenda->progress['percentage'] }}%
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $agenda->progress['uploaded_users'] }}/{{ $agenda->progress['total_users'] }} user
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $agenda->display_status === 'selesai' ? 'success' : ($agenda->display_status === 'tahap penyelesaian' ? 'warning' : 'info') }}">
                                            {{ ucfirst($agenda->display_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm btn-detail" 
                                                data-id="{{ $agenda->agenda_id }}"
                                                data-toggle="tooltip" 
                                                title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-primary btn-sm btn-upload" 
                                                data-id="{{ $agenda->agenda_id }}"
                                                data-toggle="tooltip" 
                                                title="Upload Dokumentasi">
                                            <i class="fas fa-upload"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Modal Detail Agenda -->
                        <div class="modal fade" id="detailModal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Agenda</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Informasi Agenda</h6>
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td>Nama Agenda</td>
                                                        <td>:</td>
                                                        <td id="detail-nama-agenda"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Kegiatan</td>
                                                        <td>:</td>
                                                        <td id="detail-kegiatan"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Tanggal</td>
                                                        <td>:</td>
                                                        <td id="detail-tanggal"></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>Progress</h6>
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td>Total User</td>
                                                        <td>:</td>
                                                        <td id="detail-total-user"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sudah Upload</td>
                                                        <td>:</td>
                                                        <td id="detail-uploaded"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>Status</td>
                                                        <td>:</td>
                                                        <td id="detail-status"></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="submission-list mt-4">
                                            <h6>Daftar Dokumentasi</h6>
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nama User</th>
                                                        <th>Status</th>
                                                        <th>Tanggal Upload</th>
                                                        <th>Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="submission-table-body">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Upload -->
                        <div class="modal fade" id="uploadModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Upload Dokumentasi</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <form id="uploadForm" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label>User: <span class="text-danger">*</span></label>
                                                <select class="form-control" name="user_id" required>
                                                    <option value="">Pilih User</option>
                                                </select>
                                                <div class="invalid-feedback"></div>
                                            </div>

                                            <div class="form-group">
                                                <label>Nama dokumentasi: <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" name="nama_dokumentasi" required>
                                                <div class="invalid-feedback"></div>
                                            </div>

                                            <div class="form-group">
                                                <label>Deskripsi dokumentasi: <span class="text-danger">*</span></label>
                                                <textarea class="form-control" name="deskripsi_dokumentasi" rows="3" required></textarea>
                                                <div class="invalid-feedback"></div>
                                            </div>

                                            <div class="form-group">
                                                <label>Tanggal: <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="tanggal" 
                                                       value="{{ date('Y-m-d') }}" required>
                                                <div class="invalid-feedback"></div>
                                            </div>

                                            <div class="upload-area">
                                                <div class="text-center p-4 upload-box" 
                                                     style="border: 2px dashed #dee2e6; border-radius: 4px;">
                                                    <h6 class="mb-3">UNGGAH DOKUMEN</h6>
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
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                                            <button type="submit" class="btn btn-primary">Upload</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
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
.progress {
    height: 20px;
    margin-bottom: 5px;
}
.progress-bar {
    background-color: #28a745;
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
.badge-info {
    background-color: #17a2b8;
    color: #fff;
}
.upload-area {
    background: #f8f9fa;
    margin-top: 1rem;
}
.upload-box {
    transition: all 0.3s ease;
}
.upload-box.is-invalid {
    border-color: #dc3545;
}
#selected-file-name {
    color: #6c757d;
    font-style: italic;
    margin-top: 10px;
}
.table-borderless td {
    padding: 0.5rem;
}
.modal-lg {
    max-width: 900px;
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

    // Event handler untuk tombol detail
    $('.btn-detail').click(function() {
        const id = $(this).data('id');
        
        // Fetch detail agenda
        $.get(`/admin/dosen/update-progress/${id}/detail`, function(response) {
            if (response.status === 'success') {
                const agenda = response.data.agenda;
                const submissions = response.data.user_submissions;
                
                // Update informasi agenda
                $('#detail-nama-agenda').text(agenda.nama_agenda);
                $('#detail-kegiatan').text(agenda.kegiatan_jurusan ? 
                    agenda.kegiatan_jurusan.nama_kegiatan_jurusan : 
                    agenda.kegiatan_program_studi.nama_kegiatan_program_studi);
                $('#detail-tanggal').text(moment(agenda.tanggal_agenda).format('DD-MM-YYYY'));
                
                // Update informasi progress
                $('#detail-total-user').text(submissions.length);
                $('#detail-uploaded').text(submissions.filter(s => s.has_submitted).length);
                $('#detail-status').html(`<span class="badge badge-${agenda.status_agenda === 'selesai' ? 
                    'success' : (agenda.status_agenda === 'tahap penyelesaian' ? 'warning' : 'info')}">
                    ${agenda.status_agenda}</span>`);
                
                // Render submission table
                const tableBody = submissions.map(submission => `
                    <tr>
                        <td>${submission.user_name}</td>
                        <td>
                            <span class="badge badge-${submission.has_submitted ? 'success' : 'warning'}">
                                ${submission.has_submitted ? 'Sudah Upload' : 'Belum Upload'}
                            </span>
                        </td>
                        <td>${submission.has_submitted ? 
                            moment(submission.submission_date).format('DD-MM-YYYY') : '-'}</td>
                        <td>
                            ${submission.has_submitted ? 
                                `<button class="btn btn-danger btn-sm btn-delete-doc" 
                                    data-agenda-id="${id}" 
                                    data-user-id="${submission.user_id}">
                                    <i class="fas fa-trash"></i>
                                </button>` : 
                                '-'}
                        </td>
                    </tr>
                `).join('');
                
                $('#submission-table-body').html(tableBody);
                
                // Show modal
                $('#detailModal').modal('show');
            }
        });
    });

    // Event handler untuk tombol upload
    $('.btn-upload').click(function() {
        const id = $(this).data('id');
        
        // Reset form
        $('#uploadForm')[0].reset();
        $('#selected-file-name').text('');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        // Fetch users untuk dropdown
        $.get(`/admin/dosen/update-progress/${id}/users`, function(response) {
            if (response.status === 'success') {
                const userOptions = response.data.users.map(user => 
                    `<option value="${user.user_id}">${user.nama}</option>`
                ).join('');
                
                $('select[name="user_id"]').html('<option value="">Pilih User</option>' + userOptions);
                
                // Set agenda ID untuk form upload
                $('#uploadForm').data('agenda-id', id);
                
                // Show modal
                $('#uploadModal').modal('show');
            }
        });
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

    // Handle submit form upload
    $('#uploadForm').submit(function(e) {
        e.preventDefault();
        
        // Reset validasi
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');
        
        const formData = new FormData(this);
        const agendaId = $(this).data('agenda-id');
        
        $.ajax({
            url: `/admin/dosen/update-progress/${agendaId}/update`,
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
                $('#uploadModal').modal('hide');
                
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

    // Handle delete dokumentasi
    $(document).on('click', '.btn-delete-doc', function() {
        const agendaId = $(this).data('agenda-id');
        const userId = $(this).data('user-id');
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Anda yakin ingin menghapus dokumentasi ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/dosen/update-progress/${agendaId}/delete/${userId}`,
                    type: 'DELETE',
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
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus dokumentasi'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endpush