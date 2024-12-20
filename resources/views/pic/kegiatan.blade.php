@extends('layouts.template')

@section('content')
<div class="container-fluid p-0">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detail Kegiatan</h3>
        </div>
        <div class="card-body">
            <!-- Info Jenis Kegiatan -->
            <div class="alert alert-info" role="alert">
                <h5 class="alert-heading">                    
                @if($kegiatanJurusan)
                    Kegiatan Jurusan
                @elseif($kegiatanProdi)
                    Kegiatan Program Studi
                @elseif($kegiatanInstitusi)
                    Kegiatan Institusi
                @elseif($kegiatanLuarInstitusi)
                    Kegiatan Luar Institusi
                @endif</h5>
                <p class="mb-0">Silakan isi detail kegiatan sesuai dengan surat tugas yang telah ditentukan.</p>
            </div>

            <!-- Form Data Dari Admin (Readonly) -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- Tambahkan ini di form -->
                        <input type="hidden" id="kegiatan_type" value="{{ 
                            isset($kegiatanJurusan) ? 'jurusan' : 
                            (isset($kegiatanProdi) ? 'prodi' : 
                            (isset($kegiatanInstitusi) ? 'institusi' : 
                            (isset($kegiatanLuarInstitusi) ? 'luar_institusi' : ''))) 
                        }}">
                        <input type="hidden" id="kegiatan_id" value="{{ 
                            isset($kegiatanJurusan) ? $kegiatanJurusan->kegiatan_jurusan_id : 
                            (isset($kegiatanProdi) ? $kegiatanProdi->kegiatan_program_studi_id : 
                            (isset($kegiatanInstitusi) ? $kegiatanInstitusi->kegiatan_institusi_id : 
                            (isset($kegiatanLuarInstitusi) ? $kegiatanLuarInstitusi->kegiatan_luar_institusi_id : ''))) 
                        }}">
                        <label>Surat Tugas</label>
                        <input type="text" class="form-control" value="{{ 
                            isset($kegiatanJurusan) && $kegiatanJurusan?->surat ? $kegiatanJurusan->surat->judul_surat : 
                            (isset($kegiatanProdi) && $kegiatanProdi?->surat ? $kegiatanProdi->surat->judul_surat : 
                            (isset($kegiatanInstitusi) && $kegiatanInstitusi?->surat ? $kegiatanInstitusi->surat->judul_surat :
                            (isset($kegiatanLuarInstitusi) && $kegiatanLuarInstitusi?->surat ? $kegiatanLuarInstitusi->surat->judul_surat : '-')))
                        }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Penanggung Jawab</label>
                        <input type="text" class="form-control" value="{{ 
                            isset($kegiatanJurusan) && $kegiatanJurusan?->user ? $kegiatanJurusan->user->nama_lengkap : 
                            (isset($kegiatanProdi) && $kegiatanProdi?->user ? $kegiatanProdi->user->nama_lengkap :
                            (isset($kegiatanInstitusi) && $kegiatanInstitusi?->user ? $kegiatanInstitusi->user->nama_lengkap :
                            (isset($kegiatanLuarInstitusi) && $kegiatanLuarInstitusi?->user ? $kegiatanLuarInstitusi->user->nama_lengkap : '-')))
                        }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Kegiatan</label>
                        <input type="text" class="form-control" value="{{ 
                            isset($kegiatanJurusan) ? ($kegiatanJurusan?->nama_kegiatan_jurusan ?? '-') : 
                            (isset($kegiatanProdi) ? ($kegiatanProdi?->nama_kegiatan_program_studi ?? '-') :
                            (isset($kegiatanInstitusi) ? ($kegiatanInstitusi?->nama_kegiatan_institusi ?? '-') :
                            (isset($kegiatanLuarInstitusi) ? ($kegiatanLuarInstitusi?->nama_kegiatan_luar_institusi ?? '-') : '-')))
                        }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Penyelenggara</label>
                        <input type="text" class="form-control" value="{{
                            isset($kegiatanJurusan) ? ($kegiatanJurusan?->penyelenggara ?? '-') : 
                            (isset($kegiatanProdi) ? ($kegiatanProdi?->penyelenggara ?? '-') :
                            (isset($kegiatanInstitusi) ? ($kegiatanInstitusi?->penyelenggara ?? '-') :
                            (isset($kegiatanLuarInstitusi) ? ($kegiatanLuarInstitusi?->penyelenggara ?? '-') : '-')))
                        }}" readonly>
                    </div>
                </div>
            </div>

            <!-- Form Data Dari Admin (Readonly) Lanjutan -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input class="form-control" value="{{
                            isset($kegiatanJurusan) && $kegiatanJurusan?->tanggal_mulai ? date('d-m-y', strtotime($kegiatanJurusan->tanggal_mulai)) : 
                            (isset($kegiatanProdi) && $kegiatanProdi?->tanggal_mulai ? date('d-m-y', strtotime($kegiatanProdi->tanggal_mulai)) :
                            (isset($kegiatanInstitusi) && $kegiatanInstitusi?->tanggal_mulai ? date('d-m-y', strtotime($kegiatanInstitusi->tanggal_mulai)) :
                            (isset($kegiatanLuarInstitusi) && $kegiatanLuarInstitusi?->tanggal_mulai ? date('d-m-y', strtotime($kegiatanLuarInstitusi->tanggal_mulai)) : '-')))
                        }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Lokasi Kegiatan</label>
                        <input type="text" class="form-control" value="{{ 
                            isset($kegiatanJurusan) ? ($kegiatanJurusan?->lokasi_kegiatan ?? '-') : 
                            (isset($kegiatanProdi) ? ($kegiatanProdi?->lokasi_kegiatan ?? '-') :
                            (isset($kegiatanInstitusi) ? ($kegiatanInstitusi?->lokasi_kegiatan ?? '-') :
                            (isset($kegiatanLuarInstitusi) ? ($kegiatanLuarInstitusi?->lokasi_kegiatan ?? '-') : '-')))
                        }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input class="form-control" value="{{ 
                            isset($kegiatanJurusan) && $kegiatanJurusan?->tanggal_selesai ? date('d-m-y', strtotime($kegiatanJurusan->tanggal_selesai)) : 
                            (isset($kegiatanProdi) && $kegiatanProdi?->tanggal_selesai ? date('d-m-y', strtotime($kegiatanProdi->tanggal_selesai)) :
                            (isset($kegiatanInstitusi) && $kegiatanInstitusi?->tanggal_selesai ? date('d-m-y', strtotime($kegiatanInstitusi->tanggal_selesai)) :
                            (isset($kegiatanLuarInstitusi) && $kegiatanLuarInstitusi?->tanggal_selesai ? date('d-m-y', strtotime($kegiatanLuarInstitusi->tanggal_selesai)) : '-')))
                        }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Kegiatan</label>
                        <textarea class="form-control" readonly>{{ 
                            isset($kegiatanJurusan) ? ($kegiatanJurusan?->deskripsi_kegiatan ?? '-') : 
                            (isset($kegiatanProdi) ? ($kegiatanProdi?->deskripsi_kegiatan ?? '-') :
                            (isset($kegiatanInstitusi) ? ($kegiatanInstitusi?->deskripsi_kegiatan ?? '-') :
                            (isset($kegiatanLuarInstitusi) ? ($kegiatanLuarInstitusi?->deskripsi_kegiatan ?? '-') : '-')))
                        }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-4">
                @if(isset($kegiatanJurusan) && $kegiatanJurusan?->surat)
                    <a href="{{ route('surat-tugas.download-file', $kegiatanJurusan->surat->surat_id) }}"
                       class="btn btn-primary btn-block">
                        <i class="fas fa-download mr-2"></i>Download Surat Tugas ({{ $kegiatanJurusan->surat->judul_surat }})
                    </a>
                @elseif(isset($kegiatanProdi) && $kegiatanProdi?->surat)
                    <a href="{{ route('surat-tugas.download-file', $kegiatanProdi->surat->surat_id) }}"
                       class="btn btn-primary btn-block">
                        <i class="fas fa-download mr-2"></i>Download Surat Tugas ({{ $kegiatanProdi->surat->judul_surat }})
                    </a>
                @elseif(isset($kegiatanInstitusi) && $kegiatanInstitusi?->surat)
                    <a href="{{ route('surat-tugas.download-file', $kegiatanInstitusi->surat->surat_id) }}"
                       class="btn btn-primary btn-block">
                        <i class="fas fa-download mr-2"></i>Download Surat Tugas ({{ $kegiatanInstitusi->surat->judul_surat }})
                    </a>
                @elseif(isset($kegiatanLuarInstitusi) && $kegiatanLuarInstitusi?->surat)
                    <a href="{{ route('surat-tugas.download-file', $kegiatanLuarInstitusi->surat->surat_id) }}"
                       class="btn btn-primary btn-block">
                        <i class="fas fa-download mr-2"></i>Download Surat Tugas ({{ $kegiatanLuarInstitusi->surat->judul_surat }})
                    </a>
                @else
                    <button type="button" class="btn btn-secondary btn-block" disabled>
                        <i class="fas fa-exclamation-circle mr-2"></i>Surat Tugas Belum Tersedia
                    </button>
                @endif
            </div>

            <hr>

            <!-- Agenda Section -->
            <div class="row">
                <div class="col-md-4">
                    <h5>Tambah Agenda</h5>
                    <form id="formAgenda">
                        <div class="form-group">
                            <label>Judul Agenda</label>
                            <input type="text" class="form-control" name="judul_agenda" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea class="form-control" name="deskripsi_agenda" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Agenda</label>
                            <input type="date" class="form-control" name="tanggal_agenda" 
                            min="{{ 
                                isset($kegiatanJurusan) ? $kegiatanJurusan?->tanggal_mulai : 
                                (isset($kegiatanProdi) ? $kegiatanProdi?->tanggal_mulai :
                                (isset($kegiatanInstitusi) ? $kegiatanInstitusi?->tanggal_mulai :
                                (isset($kegiatanLuarInstitusi) ? $kegiatanLuarInstitusi?->tanggal_mulai : '')))
                            }}"
                            max="{{ 
                                isset($kegiatanJurusan) ? $kegiatanJurusan?->tanggal_selesai : 
                                (isset($kegiatanProdi) ? $kegiatanProdi?->tanggal_selesai :
                                (isset($kegiatanInstitusi) ? $kegiatanInstitusi?->tanggal_selesai :
                                (isset($kegiatanLuarInstitusi) ? $kegiatanLuarInstitusi?->tanggal_selesai : '')))
                            }}"
                            required>
                        </div>
                        <div class="form-group">
                            <label>Dokumen Pendukung</label>
                            <input type="file" class="form-control-file" name="dokumen_agenda">
                            <small class="form-text text-muted">Format PDF,Doc,dll maksimal 2MB</small>
                        </div>
                        <button type="button" class="btn btn-success btn-block" id="tambahKeAgenda">
                            Tambah ke Daftar
                        </button>
                    </form>
                </div>
                <div class="col-md-8">
                    <h5>Daftar Agenda</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    <th>Dokumen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="daftarAgenda">
                                <!-- Agenda items will be dynamically added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <button type="button" class="btn btn-primary btn-block" id="unggahDatabase">
                        Unggah ke Database
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
    .card {
        margin-bottom: 20px;
    }
    .form-control[readonly] {
        background-color: #e9ecef;
    }
    .text-danger {
        color: #dc3545;
    }
    .btn-block {
        margin-top: 10px;
    }
    .table th {
        background-color: #f8f9fa;
    }
    .alert-info {
        color: #0c5460;
        background-color: #d1ecf1;
        border-color: #bee5eb;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .thead-light th {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    let agendaList = [];
    let currentAgendaId = 1;

    // Function to validate file
    function validateFile(file) {
        if (!file) return true;
        
        const maxSize = 2 * 1024 * 1024; // 2MB
        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        
        if (!allowedTypes.includes(file.type)) {
            Swal.fire('Error', 'Format file harus PDF, DOC, atau DOCX', 'error');
            return false;
        }
        
        if (file.size > maxSize) {
            Swal.fire('Error', 'Ukuran file tidak boleh lebih dari 2MB', 'error');
            return false;
        }
        
        return true;
    }

    // Handle file input change
    $('input[name="dokumen_agenda"]').change(function() {
        const file = this.files[0];
        if (file && !validateFile(file)) {
            this.value = ''; // Clear file input if validation fails
        }
    });

    // Handle adding agenda to list
    $('#tambahKeAgenda').click(function() {
        const judulAgenda = $('input[name="judul_agenda"]').val().trim();
        const deskripsiAgenda = $('textarea[name="deskripsi_agenda"]').val().trim();
        const tanggalAgenda = $('input[name="tanggal_agenda"]').val();
        const dokumenAgenda = $('input[name="dokumen_agenda"]').prop('files')[0];
        const mode = $(this).data('mode');
        const editId = $(this).data('edit-id');

        // Validation
        if (!judulAgenda || !deskripsiAgenda || !tanggalAgenda) {
            Swal.fire('Error', 'Semua field harus diisi', 'error');
            return;
        }

        if (judulAgenda.length > 200) {
            Swal.fire('Error', 'Judul agenda tidak boleh lebih dari 200 karakter', 'error');
            return;
        }

        if (dokumenAgenda && !validateFile(dokumenAgenda)) {
            return;
        }

        // Date validation
        const selectedDate = new Date(tanggalAgenda);
        const minDate = new Date($('input[name="tanggal_agenda"]').attr('min'));
        const maxDate = new Date($('input[name="tanggal_agenda"]').attr('max'));

        if (selectedDate < minDate || selectedDate > maxDate) {
            Swal.fire('Error', 'Tanggal agenda harus dalam rentang waktu kegiatan', 'error');
            return;
        }

        if (mode === 'edit') {
            const index = agendaList.findIndex(a => a.id === editId);
            if (index !== -1) {
                agendaList[index] = {
                    id: editId,
                    judul: judulAgenda,
                    tanggal: tanggalAgenda,
                    deskripsi: deskripsiAgenda,
                    dokumen: dokumenAgenda || agendaList[index].dokumen
                };
                Swal.fire('Berhasil', 'Agenda berhasil diupdate', 'success');
            }
        } else {
            const newAgenda = {
                id: currentAgendaId++,
                judul: judulAgenda,
                tanggal: tanggalAgenda,
                deskripsi: deskripsiAgenda,
                dokumen: dokumenAgenda
            };
            agendaList.push(newAgenda);
            Swal.fire('Berhasil', 'Agenda berhasil ditambahkan', 'success');
        }

        refreshAgendaTable();
        clearAgendaForm();
    });

    // Refresh agenda table with XSS prevention
    function refreshAgendaTable() {
        const tbody = $('#daftarAgenda');
        tbody.empty();

        agendaList.forEach((agenda, index) => {
            const row = $('<tr>');
            row.append($('<td>').text(index + 1));
            row.append($('<td>').text(agenda.judul));
            row.append($('<td>').text(formatDate(agenda.tanggal)));
            row.append($('<td>').text(agenda.deskripsi));
            row.append($('<td>').text(agenda.dokumen ? agenda.dokumen.name : '-'));
            
            const actionCell = $('<td>');
            const editButton = $('<button>')
                .addClass('btn btn-warning btn-sm edit-agenda')
                .attr('data-id', agenda.id)
                .html('<i class="fas fa-edit"></i>');
            
            const deleteButton = $('<button>')
                .addClass('btn btn-danger btn-sm delete-agenda ms-1')
                .attr('data-id', agenda.id)
                .html('<i class="fas fa-trash"></i>');
            
            actionCell.append(editButton, deleteButton);
            row.append(actionCell);
            tbody.append(row);
        });
    }

    // Format date for display
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    // Clear form
    function clearAgendaForm() {
        $('#formAgenda')[0].reset();
        $('#tambahKeAgenda')
            .text('Tambah ke Daftar')
            .removeData('edit-id')
            .removeData('mode');
    }

    // Handle delete agenda
    $(document).on('click', '.delete-agenda', function() {
        const agendaId = $(this).data('id');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Agenda akan dihapus dari daftar",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                agendaList = agendaList.filter(agenda => agenda.id !== agendaId);
                refreshAgendaTable();
                Swal.fire('Terhapus', 'Agenda berhasil dihapus', 'success');
            }
        });
    });

    // Handle edit agenda
    $(document).on('click', '.edit-agenda', function() {
        const agendaId = $(this).data('id');
        const agenda = agendaList.find(a => a.id === agendaId);
        
        if (agenda) {
            $('input[name="judul_agenda"]').val(agenda.judul);
            $('input[name="tanggal_agenda"]').val(agenda.tanggal);
            $('textarea[name="deskripsi_agenda"]').val(agenda.deskripsi);
            
            $('#tambahKeAgenda')
                .text('Update Agenda')
                .data('edit-id', agendaId)
                .data('mode', 'edit');

            $('html, body').animate({
                scrollTop: $("#formAgenda").offset().top - 100
            }, 500);
        }
    });

    // Handle upload to database
    $('#unggahDatabase').click(function() {
        if (agendaList.length === 0) {
            Swal.fire('Error', 'Tambahkan minimal satu agenda', 'error');
            return;
        }

        Swal.fire({
            title: 'Mohon Tunggu',
            text: 'Sedang mengupload data...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const formData = new FormData();
        
        // Get kegiatan type and ID
        const kegiatanType = $('#kegiatan_type').val();
        const kegiatanId = $('#kegiatan_id').val();

        if (!kegiatanType || !kegiatanId) {
            Swal.fire('Error', 'Data kegiatan tidak lengkap', 'error');
            return;
        }

        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('kegiatan_type', kegiatanType);
        formData.append('kegiatan_id', kegiatanId);

        // Add agenda data to FormData
        agendaList.forEach((agenda, index) => {
            formData.append(`agenda[${index}][nama_agenda]`, agenda.judul);
            formData.append(`agenda[${index}][tanggal_agenda]`, agenda.tanggal);
            formData.append(`agenda[${index}][deskripsi]`, agenda.deskripsi);
            if (agenda.dokumen instanceof File) {
                formData.append(`agenda[${index}][file_surat_agenda]`, agenda.dokumen);
            }
        });

        $.ajax({
            url: '{{ route("pic.agenda.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Agenda berhasil disimpan'
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                let errorMessage = 'Gagal menyimpan agenda';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire('Error', errorMessage, 'error');
            }
        });
    });
});
</script>
@endpush