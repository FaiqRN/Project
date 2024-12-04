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
                        <input type="text" class="form-control" value="{{ $kegiatanJurusan ? $kegiatanJurusan->surat->judul_surat : $kegiatanProdi->surat->judul_surat }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Penanggung Jawab</label>
                        <input type="text" class="form-control" value="{{ $kegiatanJurusan ? $kegiatanJurusan->user->nama_lengkap : $kegiatanProdi->user->nama_lengkap }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Nama Kegiatan</label>
                        <input type="text" class="form-control" value="{{ $kegiatanJurusan ? $kegiatanJurusan->nama_kegiatan_jurusan : $kegiatanProdi->nama_kegiatan_program_studi }}" readonly>
                    </div>

                    <div class="form-group">
                        <label>Penyelenggara</label>
                        <input type="text" class="form-control" value="{{ $kegiatanJurusan ? $kegiatanJurusan->penyelenggara : $kegiatanProdi->penyelenggara }}" readonly>
                    </div>

                </div>
            </div>

            <!-- Form Data Dari Admin (Readonly) Lanjutan -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Lokasi Kegiatan</label>
                        <input type="text" class="form-control" value="{{ $kegiatanJurusan ? $kegiatanJurusan->lokasi_kegiatan : $kegiatanProdi->lokasi_kegiatan }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input class="form-control" value="{{date('d-m-y',strtotime($kegiatanJurusan ? $kegiatanJurusan->tanggal_mulai : $kegiatanProdi->tanggal_mulai))}}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <label>Deskripsi Kegiatan</label>
                    <textarea class="form-control" readonly>{{ $kegiatanJurusan ? $kegiatanJurusan->deskripsi_kegiatan : $kegiatanProdi->deskripsi_kegiatan }}</textarea>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Selesai</label>
                            <input class="form-control" value="{{date('d-m-y',strtotime($kegiatanJurusan ? $kegiatanJurusan->tanggal_selesai : $kegiatanProdi->tanggal_selesai))}}" readonly>
                    </div>
                </div>
            </div>

            <div class="col-12 mb-4">
                @if($kegiatanJurusan && $kegiatanJurusan->surat)
                    <a href="{{ route('surat-tugas.download-file', $kegiatanJurusan->surat->surat_id) }}"
                       class="btn btn-primary btn-block">
                        <i class="fas fa-download mr-2"></i>Download Surat Tugas ({{ $kegiatanJurusan->surat->judul_surat }})
                    </a>
                @elseif($kegiatanProdi && $kegiatanProdi->surat)
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
                            min="{{ $kegiatanJurusan ? $kegiatanJurusan->tanggal_mulai : $kegiatanProdi->tanggal_mulai}}"
                            max="{{ $kegiatanJurusan ? $kegiatanJurusan->tanggal_selesai : $kegiatanProdi->tanggal_selesai }}"
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

@push('scripts')
<!-- DataTables & Plugins -->
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

<script>
// Inisialisasi variabel global
let tempAgendaJurusan = [];
let tempAgendaProdi = [];

$(function () {
    // Inisialisasi komponen
    bsCustomFileInput.init();

    // Inisialisasi DataTables
    const tableAgendaJurusan = $('#tabelAgendaJurusan').DataTable({
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: `/dosen/kegiatan/agenda/jurusan/${$('#formAgendaJurusan input[name="kegiatan_id"]').val()}`,
            type: 'GET'
        },
        columns: [
            { data: 'DT_RowIndex', searchable: false },
            { data: 'nama_agenda' },
            { data: 'tanggal_agenda' },
            { data: 'deskripsi' },
            { data: 'dokumentasi', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    const tableAgendaProdi = $('#tabelAgendaProdi').DataTable({
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: `/dosen/kegiatan/agenda/prodi/${$('#formAgendaProdi input[name="kegiatan_id"]').val()}`,
            type: 'GET'
        },
        columns: [
            { data: 'DT_RowIndex', searchable: false },
            { data: 'nama_agenda' },
            { data: 'tanggal_agenda' },
            { data: 'deskripsi' },
            { data: 'dokumentasi', orderable: false, searchable: false },
            { data: 'action', orderable: false, searchable: false }
        ]
    });

    // Event Handlers
    $('#btnTambahAgendaJurusan, #btnTambahAgendaProdi').click(function() {
        const type = $(this).attr('id').includes('Jurusan') ? 'jurusan' : 'prodi';
        handleTambahAgenda(type);
    });

    $('#btnUnggahAgendaJurusan, #btnUnggahAgendaProdi').click(function() {
        const type = $(this).attr('id').includes('Jurusan') ? 'jurusan' : 'prodi';
        handleUnggahAgenda(type);
    });

    // Fungsi untuk menangani penambahan agenda
    function handleTambahAgenda(type) {
        const form = type === 'jurusan' ? $('#formAgendaJurusan') : $('#formAgendaProdi');
        
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        const agendaData = {
            nama_agenda: form.find('input[name="nama_agenda"]').val(),
            tanggal_agenda: form.find('input[name="tanggal_agenda"]').val(),
            deskripsi: form.find('textarea[name="deskripsi"]').val(),
            file: form.find('input[type="file"]')[0].files[0],
            temp_id: Date.now()
        };

        if (type === 'jurusan') {
            tempAgendaJurusan.push(agendaData);
            refreshTempTable('jurusan');
        } else {
            tempAgendaProdi.push(agendaData);
            refreshTempTable('prodi');
        }

        form[0].reset();
        form.find('.custom-file-label').text('Pilih file');
        
        Swal.fire({
            icon: 'success',
            title: 'Berhasil',
            text: 'Agenda berhasil ditambahkan ke daftar',
            timer: 1500,
            showConfirmButton: false
        });
    }

    // Fungsi untuk refresh tabel temporary
    function refreshTempTable(type) {
        const tempData = type === 'jurusan' ? tempAgendaJurusan : tempAgendaProdi;
        const table = $(`#tabelAgenda${type === 'jurusan' ? 'Jurusan' : 'Prodi'} tbody`);
        
        table.empty();
        tempData.forEach((item, index) => {
            table.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${agenda.judul}</td>
                    <td>${agenda.tanggal}</td>
                    <td>${agenda.deskripsi}</td>
                    <td>${agenda.dokumen ? agenda.dokumen.name : '-'}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeTempAgenda('${item.temp_id}', '${type}')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    // Fungsi untuk handle unggah agenda
    function handleUnggahAgenda(type) {
        const tempData = type === 'jurusan' ? tempAgendaJurusan : tempAgendaProdi;
        
        if (tempData.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Tidak ada agenda untuk diunggah'
            });
            return;
        }

        const formData = new FormData();
        formData.append('kegiatan_type', type);
        formData.append('kegiatan_id', $(`#formAgenda${type === 'jurusan' ? 'Jurusan' : 'Prodi'} input[name="kegiatan_id"]`).val());

tempData.forEach((item, index) => {
    formData.append(`agenda[${index}][nama_agenda]`, item.nama_agenda);
    formData.append(`agenda[${index}][tanggal_agenda]`, item.tanggal_agenda);
    formData.append(`agenda[${index}][deskripsi]`, item.deskripsi);
    if (item.file) {
        formData.append(`agenda[${index}][file_surat_agenda]`, item.file);
    }
});

$.ajax({
    url: '/dosen/kegiatan/agenda/store',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    beforeSend: function() {
        Swal.fire({
            title: 'Mohon tunggu',
            text: 'Sedang mengunggah agenda...',
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
            text: 'Agenda berhasil disimpan'
        });

        // Reset data temporary dan refresh tabel
        if (type === 'jurusan') {
            tempAgendaJurusan = [];
            $('#tabelAgendaJurusan').DataTable().ajax.reload();
        } else {
            tempAgendaProdi = [];
            $('#tabelAgendaProdi').DataTable().ajax.reload();
        }
        refreshTempTable(type);
    },
    error: function(xhr) {
        let errorMessage = 'Terjadi kesalahan saat menyimpan agenda';
        if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        }
        
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: errorMessage
        });
    }
});
}
});

// Fungsi global untuk menghapus agenda temporary
function removeTempAgenda(tempId, type) {
if (type === 'jurusan') {
tempAgendaJurusan = tempAgendaJurusan.filter(item => item.temp_id !== parseInt(tempId));
refreshTempTable('jurusan');
} else {
tempAgendaProdi = tempAgendaProdi.filter(item => item.temp_id !== parseInt(tempId));
refreshTempTable('prodi');
}
}

// Event handler untuk edit agenda
$(document).on('click', '.edit-agenda', function() {
const id = $(this).data('id');
const nama = $(this).data('nama');
const tanggal = $(this).data('tanggal');
const deskripsi = $(this).data('deskripsi');

$('#edit_agenda_id').val(id);
$('#edit_nama_agenda').val(nama);
$('#edit_tanggal_agenda').val(tanggal);
$('#edit_deskripsi').val(deskripsi);

$('#modalEditAgenda').modal('show');
});

// Submit handler untuk form edit
$('#formEditAgenda').submit(function(e) {
e.preventDefault();

const formData = new FormData(this);
const id = $('#edit_agenda_id').val();

$.ajax({
url: `/dosen/kegiatan/agenda/update/${id}`,
type: 'POST',
data: formData,
processData: false,
contentType: false,
headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
},
beforeSend: function() {
    Swal.fire({
        title: 'Mohon tunggu',
        text: 'Sedang memperbarui agenda...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
},
success: function(response) {
    $('#modalEditAgenda').modal('hide');
    
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Agenda berhasil diperbarui'
    });

    // Refresh kedua tabel
    $('#tabelAgendaJurusan').DataTable().ajax.reload();
    $('#tabelAgendaProdi').DataTable().ajax.reload();
},
error: function(xhr) {
    let errorMessage = 'Terjadi kesalahan saat memperbarui agenda';
    if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMessage = xhr.responseJSON.message;
    }
    
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: errorMessage
    });
}
});
});

// Event handler untuk delete agenda
$(document).on('click', '.delete-agenda', function() {
const id = $(this).data('id');

Swal.fire({
title: 'Apakah Anda yakin?',
text: "Agenda yang dihapus tidak dapat dikembalikan!",
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#d33',
cancelButtonColor: '#3085d6',
confirmButtonText: 'Ya, hapus!',
cancelButtonText: 'Batal'
}).then((result) => {
if (result.isConfirmed) {
    $.ajax({
        url: `/dosen/kegiatan/agenda/delete/${id}`,
        type: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            Swal.fire(
                'Terhapus!',
                'Agenda berhasil dihapus.',
                'success'
            );
            
            // Refresh kedua tabel
            $('#tabelAgendaJurusan').DataTable().ajax.reload();
            $('#tabelAgendaProdi').DataTable().ajax.reload();
        },
        error: function(xhr) {
            let errorMessage = 'Terjadi kesalahan saat menghapus agenda';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            
            Swal.fire(
                'Gagal!',
                errorMessage,
                'error'
            );
        }
    });
}
});
});

// Validasi file upload
$('input[type="file"]').change(function() {
const file = this.files[0];
const maxSize = 2 * 1024 * 1024; // 2MB
const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

if (file) {
if (file.size > maxSize) {
    Swal.fire({
        icon: 'error',
        title: 'File terlalu besar',
        text: 'Ukuran file maksimal 2MB'
    });
    this.value = '';
    return false;
}

if (!allowedTypes.includes(file.type)) {
    Swal.fire({
        icon: 'error',
        title: 'Format file tidak valid',
        text: 'File harus berformat PDF, DOC, atau DOCX'
    });
    this.value = '';
    return false;
}
}
});
</script>
@endpush







