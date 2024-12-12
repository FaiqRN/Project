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
                <h5 class="alert-heading">{{ $kegiatanJurusan ? 'Kegiatan Jurusan' : 'Kegiatan Program Studi' }}</h5>
                <p class="mb-0">Silakan isi detail kegiatan sesuai dengan surat tugas yang telah ditentukan.</p>
            </div>

            <!-- Form Data Dari Admin (Readonly) -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Surat Tugas</label>
                        <input type="text" class="form-control" value="{{ isset($kegiatanJurusan) && $kegiatanJurusan?->surat ? $kegiatanJurusan->surat->judul_surat : ($kegiatanProdi?->surat?->judul_surat ?? '-') }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Penanggung Jawab</label>
                        <input type="text" class="form-control" value="{{ isset($kegiatanJurusan) && $kegiatanJurusan?->user ? $kegiatanJurusan->user->nama_lengkap : ($kegiatanProdi?->user?->nama_lengkap ?? '-') }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Kegiatan</label>
                        <input type="text" class="form-control" value="{{ isset($kegiatanJurusan) ? ($kegiatanJurusan?->nama_kegiatan_jurusan ?? '-') : ($kegiatanProdi?->nama_kegiatan_program_studi ?? '-') }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Penyelenggara</label>
                        <input type="text" class="form-control" value="{{ isset($kegiatanJurusan) ? ($kegiatanJurusan?->penyelenggara ?? '-') : ($kegiatanProdi?->penyelenggara ?? '-') }}" readonly>
                    </div>
                </div>
            </div>

            <!-- Form Data Dari Admin (Readonly) Lanjutan -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input class="form-control" value="{{ isset($kegiatanJurusan) && $kegiatanJurusan?->tanggal_mulai ? date('d-m-y', strtotime($kegiatanJurusan->tanggal_mulai)) : (isset($kegiatanProdi?->tanggal_mulai) ? date('d-m-y', strtotime($kegiatanProdi->tanggal_mulai)) : '-') }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Lokasi Kegiatan</label>
                        <input type="text" class="form-control" value="{{ isset($kegiatanJurusan) ? ($kegiatanJurusan?->lokasi_kegiatan ?? '-') : ($kegiatanProdi?->lokasi_kegiatan ?? '-') }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input class="form-control" value="{{ isset($kegiatanJurusan) && $kegiatanJurusan?->tanggal_selesai ? date('d-m-y', strtotime($kegiatanJurusan->tanggal_selesai)) : (isset($kegiatanProdi?->tanggal_selesai) ? date('d-m-y', strtotime($kegiatanProdi->tanggal_selesai)) : '-') }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Kegiatan</label>
                        <textarea class="form-control" readonly>{{ isset($kegiatanJurusan) ? ($kegiatanJurusan?->deskripsi_kegiatan ?? '-') : ($kegiatanProdi?->deskripsi_kegiatan ?? '-') }}</textarea>
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
                            min="{{ isset($kegiatanJurusan) ? $kegiatanJurusan?->tanggal_mulai : $kegiatanProdi?->tanggal_mulai }}"
                            max="{{ isset($kegiatanJurusan) ? $kegiatanJurusan?->tanggal_selesai : $kegiatanProdi?->tanggal_selesai }}"
                            required>
                        </div>
                        <div class="form-group">
                            <label>Dokumen Pendukung</label>
                            <input type="file" class="form-control-file" name="dokumen_agenda">
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

    // Handle adding agenda to list
    $('#tambahKeAgenda').click(function() {
        const judulAgenda = $('input[name="judul_agenda"]').val();
        const deskripsiAgenda = $('textarea[name="deskripsi_agenda"]').val();
        const tanggalAgenda = $('input[name="tanggal_agenda"]').val();
        const dokumenAgenda = $('input[name="dokumen_agenda"]').prop('files')[0];
        const mode = $(this).data('mode');
        const editId = $(this).data('edit-id');

        if (!judulAgenda || !deskripsiAgenda || !tanggalAgenda) {
            Swal.fire('Error', 'Semua field harus diisi', 'error');
            return;
        }

        if (mode === 'edit') {
            // Update existing agenda in the list
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
            // Add new agenda to list
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


    // Refresh agenda table
    function refreshAgendaTable() {
        const tbody = $('#daftarAgenda');
        tbody.empty();

        agendaList.forEach((agenda, index) => {
            tbody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${agenda.judul}</td>
                    <td>${agenda.tanggal}</td>
                    <td>${agenda.deskripsi}</td>
                    <td>${agenda.dokumen ? agenda.dokumen.name : '-'}</td>
                    <td>
                        <button 
                            class="btn btn-warning btn-sm edit-agenda" 
                            data-id="${agenda.id}" 
                            data-judul="${agenda.judul}" 
                            data-tanggal="${agenda.tanggal}" 
                            data-deskripsi="${agenda.deskripsi}" >
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm delete-agenda" data-id="${agenda.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    // Clear agenda form
    function clearAgendaForm() {
        $('#formAgenda')[0].reset();
        $('#tambahKeAgenda').text('Tambah ke Daftar').removeData('edit-id').removeData('mode');
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
        const judulAgenda = $(this).data('judul');
        const tanggalAgenda = $(this).data('tanggal');
        const deskripsiAgenda = $(this).data('deskripsi');
        const agendaId = $(this).data('id');
        
        // Fill the form with existing data
        $('input[name="judul_agenda"]').val(judulAgenda);
        $('input[name="tanggal_agenda"]').val(tanggalAgenda);
        $('textarea[name="deskripsi_agenda"]').val(deskripsiAgenda);
        
        // Change button text and store agenda ID
        $('#tambahKeAgenda')
            .text('Update Agenda')
            .data('edit-id', agendaId)
            .data('mode', 'edit');

        // Scroll to form
        $('html, body').animate({
            scrollTop: $("#formAgenda").offset().top - 100
        }, 500);
    });


    // Handle upload to database
    $('#unggahDatabase').click(function() {
        if (agendaList.length === 0) {
            Swal.fire('Error', 'Tambahkan minimal satu agenda', 'error');
            return;
        }

        const formData = new FormData();
        
        // Add kegiatan info
        formData.append('kegiatan_type', '{{ isset($kegiatanJurusan) ? "jurusan" : "prodi" }}');
        formData.append('kegiatan_id', '{{ isset($kegiatanJurusan) ? ($kegiatanJurusan?->kegiatan_jurusan_id ?? '-') : ($kegiatanProdi?->kegiatan_program_studi_id ?? '-') }}');        
        // Format agenda data
        agendaList.forEach((agenda, index) => {
            formData.append(`agenda[${index}][nama_agenda]`, agenda.judul);
            formData.append(`agenda[${index}][tanggal_agenda]`, agenda.tanggal);
            formData.append(`agenda[${index}][deskripsi]`, agenda.deskripsi);
            if (agenda.dokumen) {
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
                Swal.fire('Berhasil', 'Agenda berhasil disimpan', 'success')
                .then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire('Error', xhr.responseJSON?.message || 'Gagal menyimpan agenda', 'error');
            }
        });
    });
});
</script>
@endpush