@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Kegiatan</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Kegiatan Jurusan Card -->
            @if($kegiatanJurusan)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kegiatan Jurusan</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Nama Kegiatan:</label>
                                <p class="form-control">{{ $kegiatanJurusan->nama_kegiatan_jurusan }}</p>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Mulai:</label>
                                <p class="form-control">{{ date('d-m-Y', strtotime($kegiatanJurusan->tanggal_mulai)) }}</p>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Selesai:</label>
                                <p class="form-control">{{ date('d-m-Y', strtotime($kegiatanJurusan->tanggal_selesai)) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Penyelenggara:</label>
                                <p class="form-control">{{ $kegiatanJurusan->penyelenggara }}</p>
                            </div>
                            <div class="form-group">
                                <label>Lokasi:</label>
                                <p class="form-control">{{ $kegiatanJurusan->lokasi_kegiatan }}</p>
                            </div>
                            <div class="form-group">
                                <label>Deskripsi:</label>
                                <p class="form-control">{{ $kegiatanJurusan->deskripsi_kegiatan }}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-primary btn-block" onclick="downloadSuratTugas('jurusan', {{ $kegiatanJurusan->kegiatan_jurusan_id }})">
                                <i class="fas fa-download mr-2"></i>Download Surat Tugas
                            </button>
                        </div>
                    </div>

                    <!-- Agenda Section -->
                    <div class="row mt-4">
                        <!-- Form Agenda -->
                        <div class="col-md-4">
                            <h4>Simpan Agenda</h4>
                            <form id="formAgendaJurusan" class="bg-gray-light p-3 rounded">
                                <input type="hidden" name="kegiatan_type" value="jurusan">
                                <input type="hidden" name="kegiatan_id" value="{{ $kegiatanJurusan->kegiatan_jurusan_id }}">
                                <div class="form-group">
                                    <input type="text" name="nama_agenda" class="form-control mb-2" placeholder="Judul Agenda" required>
                                    <textarea name="deskripsi" class="form-control mb-2" placeholder="Deskripsi" required></textarea>
                                    <input type="date" name="tanggal_agenda" class="form-control mb-2" required>
                                    <div class="custom-file mb-2">
                                        <input type="file" class="custom-file-input" name="file_surat_agenda" id="file_agenda_jurusan">
                                        <label class="custom-file-label" for="file_agenda_jurusan">Pilih file</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-plus mr-2"></i>Simpan Agenda
                                </button>
                            </form>
                        </div>
                        
                        <!-- Tabel Agenda -->
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4>Daftar Agenda</h4>
                            </div>
                            <div class="table-responsive">
                                <table id="tableAgendaJurusan" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Agenda</th>
                                            <th>Tanggal</th>
                                            <th>Deskripsi</th>
                                            <th>Dokumentasi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Kegiatan Prodi Card -->
            @if($kegiatanProdi)
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kegiatan Program Studi</h3>
                </div>
                <div class="card-body">
                    <!-- [Previous Prodi details code remains the same] -->
                    
                    <!-- Agenda Section -->
                    <div class="row mt-4">
                        <!-- Form Agenda -->
                        <div class="col-md-4">
                            <h4>Simpan Agenda</h4>
                            <form id="formAgendaProdi" class="bg-gray-light p-3 rounded">
                                <input type="hidden" name="kegiatan_type" value="prodi">
                                <input type="hidden" name="kegiatan_id" value="{{ $kegiatanProdi->kegiatan_program_studi_id }}">
                                <div class="form-group">
                                    <input type="text" name="nama_agenda" class="form-control mb-2" placeholder="Judul Agenda" required>
                                    <textarea name="deskripsi" class="form-control mb-2" placeholder="Deskripsi" required></textarea>
                                    <input type="date" name="tanggal_agenda" class="form-control mb-2" required>
                                    <div class="custom-file mb-2">
                                        <input type="file" class="custom-file-input" name="file_surat_agenda" id="file_agenda_prodi">
                                        <label class="custom-file-label" for="file_agenda_prodi">Pilih file</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-plus mr-2"></i>Simpan Agenda
                                </button>
                            </form>
                        </div>
                        
                        <!-- Tabel Agenda -->
                        <div class="col-md-8">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4>Daftar Agenda</h4>
                            </div>
                            <div class="table-responsive">
                                <table id="tableAgendaProdi" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Agenda</th>
                                            <th>Tanggal</th>
                                            <th>Deskripsi</th>
                                            <th>Dokumentasi</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@endpush

@push('scripts')
<!-- DataTables & Plugins -->
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- bs-custom-file-input -->
<script src="{{ asset('adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

<script>
$(function() {
    bsCustomFileInput.init();

    // Inisialisasi DataTables
    let tableAgendaJurusan = $('#tableAgendaJurusan').DataTable({
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: `/agenda/jurusan/${$('#formAgendaJurusan input[name="kegiatan_id"]').val()}`,
            type: 'GET'
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'nama_agenda', name: 'nama_agenda'},
            {data: 'tanggal_agenda', name: 'tanggal_agenda'},
            {data: 'deskripsi', name: 'deskripsi'},
            {
                data: 'dokumentasi', 
                name: 'dokumentasi',
                render: function(data) {
                    if (data) {
                        return `<a href="${data}" target="_blank" class="btn btn-info btn-sm">
                                    <i class="fas fa-file-download"></i> Download
                               </a>`;
                    }
                    return '-';
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });

    let tableAgendaProdi = $('#tableAgendaProdi').DataTable({
        responsive: true,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: {
            url: `/agenda/prodi/${$('#formAgendaProdi input[name="kegiatan_id"]').val()}`,
            type: 'GET'
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'nama_agenda', name: 'nama_agenda'},
            {data: 'tanggal_agenda', name: 'tanggal_agenda'},
            {data: 'deskripsi', name: 'deskripsi'},
            {
                data: 'dokumentasi', 
                name: 'dokumentasi',
                render: function(data) {
                    if (data) {
                        return `<a href="${data}" target="_blank" class="btn btn-info btn-sm">
                                    <i class="fas fa-file-download"></i> Download
                               </a>`;
                    }
                    return '-';
                }
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });

// Handle form submit untuk agenda jurusan
$('#formAgendaJurusan').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    // Validasi tanggal agenda
    $.ajax({
        url: '/kegiatan/validate-tanggal-agenda',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status === 'success') {
                // Jika validasi berhasil, submit agenda
                submitAgenda(formData, 'jurusan');
            }
        },
        error: function(xhr) {
            let errors = xhr.responseJSON;
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: errors.message || 'Terjadi kesalahan saat validasi tanggal'
            });
        }
    });
});

// Handle form submit untuk agenda prodi
$('#formAgendaProdi').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    // Validasi tanggal agenda
    $.ajax({
        url: '/kegiatan/validate-tanggal-agenda',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status === 'success') {
                // Jika validasi berhasil, submit agenda
                submitAgenda(formData, 'prodi');
            }
        },
        error: function(xhr) {
            let errors = xhr.responseJSON;
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: errors.message || 'Terjadi kesalahan saat validasi tanggal'
            });
        }
    });
});

// Function untuk submit agenda
function submitAgenda(formData, type) {
    $.ajax({
        url: '/agenda',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Agenda berhasil ditambahkan'
                });
                
                // Reset form
                $(`#formAgenda${type.charAt(0).toUpperCase() + type.slice(1)}`)[0].reset();
                
                // Refresh table
                if (type === 'jurusan') {
                    tableAgendaJurusan.ajax.reload();
                } else {
                    tableAgendaProdi.ajax.reload();
                }
            }
        },
        error: function(xhr) {
            let errors = xhr.responseJSON;
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: errors.message || 'Terjadi kesalahan saat menyimpan agenda'
            });
        }
    });
}

// Function untuk download surat tugas
function downloadSuratTugas(type, id) {
    window.location.href = `/kegiatan/download-surat-tugas/${type}/${id}`;
}

// Handle edit agenda
$(document).on('click', '.edit-btn', function() {
    let id = $(this).data('id');
    let type = $(this).data('type');
    
    $.get(`/agenda/${id}`, function(response) {
        if (response.status === 'success') {
            let data = response.data;
            Swal.fire({
                title: 'Edit Agenda',
                html: `
                    <form id="formEditAgenda">
                        <input type="hidden" name="id" value="${data.agenda_id}">
                        <div class="form-group">
                            <label>Nama Agenda</label>
                            <input type="text" name="nama_agenda" class="form-control" value="${data.nama_agenda}" required>
                        </div>
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="date" name="tanggal_agenda" class="form-control" value="${data.tanggal_agenda}" required>
                        </div>
                        <div class="form-group">
                            <label>Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" required>${data.deskripsi}</textarea>
                        </div>
                        <div class="form-group">
                            <label>File Dokumen</label>
                            <input type="file" name="file_surat_agenda" class="form-control">
                            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah file</small>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    let formData = new FormData($('#formEditAgenda')[0]);
                    return $.ajax({
                        url: `/agenda/${id}`,
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'X-HTTP-Method-Override': 'PUT'
                        }
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Berhasil!', 'Agenda berhasil diperbarui', 'success');
                    if (type === 'jurusan') {
                        tableAgendaJurusan.ajax.reload();
                    } else {
                        tableAgendaProdi.ajax.reload();
                    }
                }
            });
        }
    });
});

// Handle delete agenda
$(document).on('click', '.delete-btn', function() {
    let id = $(this).data('id');
    let type = $(this).data('type');
    
    Swal.fire({
        title: 'Hapus Agenda?',
        text: "Data yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `/agenda/${id}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire('Berhasil!', 'Agenda berhasil dihapus', 'success');
                        if (type === 'jurusan') {
                            tableAgendaJurusan.ajax.reload();
                        } else {
                            tableAgendaProdi.ajax.reload();
                        }
                    }
                },
                error: function(xhr) {
                    Swal.fire('Gagal!', 'Terjadi kesalahan saat menghapus agenda', 'error');
                }
            });
        }
    });
});
});
</script>
@endpush