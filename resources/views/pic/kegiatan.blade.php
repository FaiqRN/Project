@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Kegiatan</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-3" id="kegiatanTab" role="tablist">
                @if($kegiatanJurusan)
                <li class="nav-item">
                    <a class="nav-link active" id="jurusan-tab" data-toggle="tab" href="#jurusan" role="tab">
                        Kegiatan Jurusan
                    </a>
                </li>
                @endif
                @if($kegiatanProdi)
                <li class="nav-item">
                    <a class="nav-link {{ !$kegiatanJurusan ? 'active' : '' }}" id="prodi-tab" data-toggle="tab" href="#prodi" role="tab">
                        Kegiatan Program Studi
                    </a>
                </li>
                @endif
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="kegiatanTabContent">
                <!-- Kegiatan Jurusan -->
                @if($kegiatanJurusan)
                <div class="tab-pane fade show active" id="jurusan" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Kegiatan Jurusan</h3>
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

                                <!-- Download Surat -->
                                <div class="col-12 mb-4">
                                    @if($kegiatanJurusan->surat)
                                        <a href="{{ route('surat-tugas.download-file', $kegiatanJurusan->surat->surat_id) }}" 
                                           class="btn btn-primary btn-block">
                                            <i class="fas fa-download mr-2"></i>Download Surat Tugas ({{ $kegiatanJurusan->surat->nomer_surat }})
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-secondary btn-block" disabled>
                                            <i class="fas fa-exclamation-circle mr-2"></i>Surat Tugas Belum Tersedia
                                        </button>
                                    @endif
                                </div>

                                <!-- Form Agenda Jurusan -->
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Tambah Agenda</h3>
                                        </div>
                                        <div class="card-body">
                                            <form id="formAgendaJurusan" class="form-horizontal">
                                                @csrf
                                                <input type="hidden" name="kegiatan_type" value="jurusan">
                                                <input type="hidden" name="kegiatan_id" value="{{ $kegiatanJurusan->kegiatan_jurusan_id }}">
                                                
                                                <div class="form-group">
                                                    <label>Judul Agenda</label>
                                                    <input type="text" class="form-control" name="nama_agenda" required>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Deskripsi</label>
                                                    <textarea class="form-control" name="deskripsi" rows="3" required></textarea>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Tanggal Agenda</label>
                                                    <input type="date" class="form-control" name="tanggal_agenda" 
                                                           min="{{ $kegiatanJurusan->tanggal_mulai }}" 
                                                           max="{{ $kegiatanJurusan->tanggal_selesai }}" required>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Dokumen Pendukung</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" name="file_surat_agenda" id="fileAgendaJurusan">
                                                        <label class="custom-file-label" for="fileAgendaJurusan">Pilih file</label>
                                                    </div>
                                                    <small class="form-text text-muted">Format: PDF, DOC, DOCX (Maks. 2MB)</small>
                                                </div>

                                                <div class="form-group">
                                                    <button type="button" class="btn btn-success btn-block mb-2" id="btnTambahAgendaJurusan">
                                                        <i class="fas fa-plus mr-2"></i>Tambah ke Daftar
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-block" id="btnUnggahAgendaJurusan">
                                                        <i class="fas fa-upload mr-2"></i>Unggah ke Database
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabel Agenda Jurusan -->
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Daftar Agenda</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle mr-2"></i>Agenda yang belum diunggah akan hilang saat halaman di-refresh
                                            </div>
                                            <div class="table-responsive">
                                                <table id="tabelAgendaJurusan" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">No</th>
                                                            <th width="20%">Judul</th>
                                                            <th width="15%">Tanggal</th>
                                                            <th>Deskripsi</th>
                                                            <th width="15%">Dokumen</th>
                                                            <th width="10%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if($kegiatanProdi)
                <div class="tab-pane fade {{ !$kegiatanJurusan ? 'show active' : '' }}" id="prodi" role="tabpanel">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Data Kegiatan Program Studi</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nama Kegiatan:</label>
                                        <p class="form-control">{{ $kegiatanProdi->nama_kegiatan_program_studi }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Mulai:</label>
                                        <p class="form-control">{{ date('d-m-Y', strtotime($kegiatanProdi->tanggal_mulai)) }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Tanggal Selesai:</label>
                                        <p class="form-control">{{ date('d-m-Y', strtotime($kegiatanProdi->tanggal_selesai)) }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Penyelenggara:</label>
                                        <p class="form-control">{{ $kegiatanProdi->penyelenggara }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Lokasi:</label>
                                        <p class="form-control">{{ $kegiatanProdi->lokasi_kegiatan }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Deskripsi:</label>
                                        <p class="form-control">{{ $kegiatanProdi->deskripsi_kegiatan }}</p>
                                    </div>
                                </div>
                
                                <!-- Download Surat -->
                                <div class="col-12 mb-4">
                                    @if($kegiatanProdi->surat)
                                        <a href="{{ route('surat-tugas.download-file', $kegiatanProdi->surat->surat_id) }}" 
                                           class="btn btn-primary btn-block">
                                            <i class="fas fa-download mr-2"></i>Download Surat Tugas ({{ $kegiatanProdi->surat->nomer_surat }})
                                        </a>
                                    @else
                                        <button type="button" class="btn btn-secondary btn-block" disabled>
                                            <i class="fas fa-exclamation-circle mr-2"></i>Surat Tugas Belum Tersedia
                                        </button>
                                    @endif
                                </div>
                
                                <!-- Form Agenda Prodi -->
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Tambah Agenda</h3>
                                        </div>
                                        <div class="card-body">
                                            <form id="formAgendaProdi" class="form-horizontal">
                                                @csrf
                                                <input type="hidden" name="kegiatan_type" value="prodi">
                                                <input type="hidden" name="kegiatan_id" value="{{ $kegiatanProdi->kegiatan_prodi_id }}">
                                                
                                                <div class="form-group">
                                                    <label>Judul Agenda</label>
                                                    <input type="text" class="form-control" name="nama_agenda" required>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Deskripsi</label>
                                                    <textarea class="form-control" name="deskripsi" rows="3" required></textarea>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Tanggal Agenda</label>
                                                    <input type="date" class="form-control" name="tanggal_agenda" 
                                                           min="{{ $kegiatanProdi->tanggal_mulai }}" 
                                                           max="{{ $kegiatanProdi->tanggal_selesai }}" required>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label>Dokumen Pendukung</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" name="file_surat_agenda" id="fileAgendaProdi">
                                                        <label class="custom-file-label" for="fileAgendaProdi">Pilih file</label>
                                                    </div>
                                                    <small class="form-text text-muted">Format: PDF, DOC, DOCX (Maks. 2MB)</small>
                                                </div>
                
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-success btn-block mb-2" id="btnTambahAgendaProdi">
                                                        <i class="fas fa-plus mr-2"></i>Tambah ke Daftar
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-block" id="btnUnggahAgendaProdi">
                                                        <i class="fas fa-upload mr-2"></i>Unggah ke Database
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                
                                <!-- Tabel Agenda Prodi -->
                                <div class="col-md-8">
                                    <div class="card">
                                        <div class="card-header">
                                            <h3 class="card-title">Daftar Agenda</h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle mr-2"></i>Agenda yang belum diunggah akan hilang saat halaman di-refresh
                                            </div>
                                            <div class="table-responsive">
                                                <table id="tabelAgendaProdi" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th width="5%">No</th>
                                                            <th width="20%">Judul</th>
                                                            <th width="15%">Tanggal</th>
                                                            <th>Deskripsi</th>
                                                            <th width="15%">Dokumen</th>
                                                            <th width="10%">Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
</div>

<!-- Modal Edit Agenda -->
<div class="modal fade" id="modalEditAgenda" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Agenda</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditAgenda">
                @csrf
                @method('PUT')
                <input type="hidden" name="agenda_id" id="edit_agenda_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul Agenda</label>
                        <input type="text" class="form-control" name="nama_agenda" id="edit_nama_agenda" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal</label>
                        <input type="date" class="form-control" name="tanggal_agenda" id="edit_tanggal_agenda" required>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="edit_deskripsi" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>File Baru (Opsional)</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" name="file_surat_agenda" id="edit_file_agenda">
                            <label class="custom-file-label" for="edit_file_agenda">Pilih file</label>
                        </div>
                        <small class="form-text text-muted">Format: PDF, DOC, DOCX (Maks. 2MB)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
@endpush

@push('scripts')
<!-- DataTables & Plugins -->
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<!-- SweetAlert2 -->
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<!-- bs-custom-file-input -->
<script src="{{ asset('adminlte/plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>

<script>
$(function () {
    // Initialize Bootstrap custom file input
    bsCustomFileInput.init();
    
    // CSRF token setup for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Temporary storage arrays for both Jurusan and Prodi agendas
    let tempAgendaJurusan = [];
    let tempAgendaProdi = [];
    
    // Initialize DataTable for Jurusan Agenda
    let tableAgendaJurusan = $('#tabelAgendaJurusan').DataTable({
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
            { 
                data: 'file_surat_agenda',
                render: function(data) {
                    return data ? `<a href="/storage/${data}" class="btn btn-info btn-sm" target="_blank">
                                    <i class="fas fa-download"></i> Download
                                </a>` : '-';
                }
            },
            {
                data: 'action',
                render: function(data, type, row) {
                    return `
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                    data-id="${row.id}" data-type="jurusan">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                    data-id="${row.id}" data-type="jurusan">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // Initialize DataTable for Prodi Agenda
    let tableAgendaProdi = $('#tabelAgendaProdi').DataTable({
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
            { 
                data: 'file_surat_agenda',
                render: function(data) {
                    return data ? `<a href="/storage/${data}" class="btn btn-info btn-sm" target="_blank">
                                    <i class="fas fa-download"></i> Download
                                </a>` : '-';
                }
            },
            {
                data: 'action',
                render: function(data, type, row) {
                    return `
                        <div class="btn-group">
                            <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                    data-id="${row.id}" data-type="prodi">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                    data-id="${row.id}" data-type="prodi">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // Handle "Tambah ke Daftar" button for both Jurusan and Prodi
    $('#btnTambahAgendaJurusan').click(function() {
        handleTambahAgenda('jurusan');
    });

    $('#btnTambahAgendaProdi').click(function() {
        handleTambahAgenda('prodi');
    });

    function handleTambahAgenda(type) {
        const form = type === 'jurusan' ? $('#formAgendaJurusan') : $('#formAgendaProdi');
        
        // Form validation
        if (!form[0].checkValidity()) {
            form[0].reportValidity();
            return;
        }

        // Validasi tanggal
        const tanggalMulai = new Date(form.find('input[name="tanggal_mulai"]').val());
        const tanggalSelesai = new Date(form.find('input[name="tanggal_selesai"]').val());
        const tanggalAgenda = new Date(form.find('input[name="tanggal_agenda"]').val());

        if (tanggalAgenda < tanggalMulai || tanggalAgenda > tanggalSelesai) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Tanggal agenda harus berada dalam rentang tanggal kegiatan'
            });
            return;
        }

        // Create agenda data object
        const agendaData = {
            nama_agenda: form.find('input[name="nama_agenda"]').val(),
            tanggal_agenda: form.find('input[name="tanggal_agenda"]').val(),
            deskripsi: form.find('textarea[name="deskripsi"]').val(),
            file: form.find('input[type="file"]')[0].files[0],
            temp_id: Date.now()
        };

        // Add to temporary array
        if (type === 'jurusan') {
            tempAgendaJurusan.push(agendaData);
            refreshTempTable('jurusan');
        } else {
            tempAgendaProdi.push(agendaData);
            refreshTempTable('prodi');
        }

        // Reset form
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

    // Refresh temporary table function
    function refreshTempTable(type) {
        const table = type === 'jurusan' ? $('#tabelAgendaJurusan tbody') : $('#tabelAgendaProdi tbody');
        const tempData = type === 'jurusan' ? tempAgendaJurusan : tempAgendaProdi;
        
        table.empty();
        
        tempData.forEach((item, index) => {
            table.append(`
                <tr data-temp-id="${item.temp_id}">
                    <td>${index + 1}</td>
                    <td>${item.nama_agenda}</td>
                    <td>${item.tanggal_agenda}</td>
                    <td>${item.deskripsi}</td>
                    <td>${item.file ? item.file.name : '-'}</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm btn-remove-temp" 
                                data-id="${item.temp_id}" data-type="${type}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    // Handle temporary agenda deletion
    $(document).on('click', '.btn-remove-temp', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        
        if (type === 'jurusan') {
            tempAgendaJurusan = tempAgendaJurusan.filter(item => item.temp_id !== id);
            refreshTempTable('jurusan');
        } else {
            tempAgendaProdi = tempAgendaProdi.filter(item => item.temp_id !== id);
            refreshTempTable('prodi');
        }
    });

    // Handle "Unggah ke Database" button for both Jurusan and Prodi
    $('#btnUnggahAgendaJurusan').click(function() {
        handleUnggahAgenda('jurusan');
    });

    $('#btnUnggahAgendaProdi').click(function() {
        handleUnggahAgenda('prodi');
    });

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

        // Create FormData
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

        // Send to server
        $.ajax({
            url: '/dosen/kegiatan/agenda/store',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
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
                    text: 'Agenda berhasil diunggah ke database'
                });
                
                // Reset data
                if (type === 'jurusan') {
                    tempAgendaJurusan = [];
                    tableAgendaJurusan.ajax.reload();
                } else {
                    tempAgendaProdi = [];
                    tableAgendaProdi.ajax.reload();
                }
                
                refreshTempTable(type);
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat mengunggah agenda'
                });
            }
        });
    }

    // Handle edit button click
    $(document).on('click', '.btn-edit', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        
        $.get(`/dosen/kegiatan/agenda/${type}/show/${id}`, function(data) {
            $('#edit_agenda_id').val(data.id);
            $('#edit_nama_agenda').val(data.nama_agenda);
            $('#edit_tanggal_agenda').val(data.tanggal_agenda);
            $('#edit_deskripsi').val(data.deskripsi);
            $('#edit_type').val(type);
            
            $('#modalEditAgenda').modal('show');
        });
    });

    // Handle edit form submission
    $('#formEditAgenda').submit(function(e) {
        e.preventDefault();
        
        const id = $('#edit_agenda_id').val();
        const type = $('#edit_type').val();
        const formData = new FormData(this);

        $.ajax({
            url: `/dosen/kegiatan/agenda/${type}/update/${id}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#modalEditAgenda').modal('hide');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Agenda berhasil diperbarui'
                });

                if (type === 'jurusan') {
                    tableAgendaJurusan.ajax.reload();
                } else {
                    tableAgendaProdi.ajax.reload();
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat memperbarui agenda'
                });
            }
        });
    });

    // Handle delete button click
    $(document).on('click', '.btn-delete', function() {
        const id = $(this).data('id');
        const type = $(this).data('type');
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus agenda ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/dosen/kegiatan/agenda/${type}/delete/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Agenda berhasil dihapus'
                        });
                        
                        if (type === 'jurusan') {
                            tableAgendaJurusan.ajax.reload();
                        } else {
                            tableAgendaProdi.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus agenda'
                        });
                    }
                });
            }
        });
    });

    // File input change handler
    $('.custom-file-input').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

// Global error handler for AJAX requests
$(document).ajaxError(function(event, jqXHR, settings, error) {
        console.error('Ajax Error:', error);
        if (!jqXHR.responseJSON) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan pada server'
            });
            return;
        }

        const message = jqXHR.responseJSON.message;
        const errors = jqXHR.responseJSON.errors;

        if (errors) {
            let errorMessage = '<ul>';
            Object.keys(errors).forEach(key => {
                errorMessage += `<li>${errors[key][0]}</li>`;
            });
            errorMessage += '</ul>';

            Swal.fire({
                icon: 'error',
                title: 'Validasi Error',
                html: errorMessage
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: message || 'Terjadi kesalahan pada server'
            });
        }
    });

    // Date validation function for both Jurusan and Prodi
    function validateTanggalAgenda(tanggalAgenda, tanggalMulai, tanggalSelesai) {
        const agendaDate = new Date(tanggalAgenda);
        const startDate = new Date(tanggalMulai);
        const endDate = new Date(tanggalSelesai);

        if (agendaDate < startDate || agendaDate > endDate) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Tanggal agenda harus berada dalam rentang tanggal kegiatan'
            });
            return false;
        }
        return true;
    }

    // File validation function for both Jurusan and Prodi
    function validateFile(file) {
        if (!file) return true;

        const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        const maxSize = 2 * 1024 * 1024; // 2MB

        if (!allowedTypes.includes(file.type)) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'File harus berupa PDF, DOC, atau DOCX'
            });
            return false;
        }

        if (file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ukuran file maksimal 2MB'
            });
            return false;
        }

        return true;
    }

    // Function to format date for display
    function formatDate(date) {
        if (!date) return '';
        const d = new Date(date);
        const month = '' + (d.getMonth() + 1);
        const day = '' + d.getDate();
        const year = d.getFullYear();

        return [day.padStart(2, '0'), month.padStart(2, '0'), year].join('-');
    }

    // Function to reset form
    function resetForm(type) {
        const form = type === 'jurusan' ? $('#formAgendaJurusan') : $('#formAgendaProdi');
        form[0].reset();
        form.find('.custom-file-label').text('Pilih file');
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').remove();
    }

    // Function to show loading state
    function showLoading(message = 'Mohon tunggu...') {
        Swal.fire({
            title: 'Loading',
            text: message,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    }

    // Function to hide loading state
    function hideLoading() {
        Swal.close();
    }

    // Initialize date pickers if needed
    if ($.fn.datepicker) {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true
        });
    }

    // Initialize rich text editor if needed
    if (typeof CKEDITOR !== 'undefined') {
        CKEDITOR.replace('deskripsi');
    }

    // Handle tab changes to refresh tables
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        const target = $(e.target).attr('href');
        if (target === '#jurusan') {
            tableAgendaJurusan.ajax.reload();
        } else if (target === '#prodi') {
            tableAgendaProdi.ajax.reload();
        }
    });
});
</script>
@endpush