@extends('layouts.template')

@section('content')
<div class="container-fluid p-0">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="card-title">Daftar Kegiatan</h3>
                </div>
                <div class="col-md-6 text-right">
                    <div class="btn-group">
                        <button type="button" class="btn btn-success" id="tambahKegiatanJurusan">
                            <i class="fas fa-plus"></i> Kegiatan Jurusan
                        </button>
                        <button type="button" class="btn btn-primary" id="tambahKegiatanProdi">
                            <i class="fas fa-plus"></i> Kegiatan Prodi
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#kegiatanJurusan">Kegiatan Jurusan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#kegiatanProdi">Kegiatan Program Studi</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content pt-4">
                <div id="kegiatanJurusan" class="tab-pane active">
                    <div class="table-responsive">
                        <table id="tableKegiatanJurusan" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Penanggung Jawab</th>
                                    <th>Surat Tugas</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <div id="kegiatanProdi" class="tab-pane fade">
                    <div class="table-responsive">
                        <table id="tableKegiatanProdi" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Penanggung Jawab</th>
                                    <th>Surat Tugas</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Jurusan -->
<div class="modal fade" id="modalKegiatanJurusan" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Form Kegiatan Jurusan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formKegiatanJurusan" novalidate>
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="kegiatan_jurusan_id" id="kegiatan_jurusan_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Surat Tugas <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="surat_id" id="surat_id_jurusan" required>
                                    <option value="">Pilih Surat Tugas</option>
                                    @foreach($surat as $s)
                                        <option value="{{ $s->surat_id }}">{{ $s->nomer_surat }} - {{ $s->judul_surat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Penanggung Jawab <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="user_id" id="user_id_jurusan" required>
                                    <option value="">Pilih Penanggung Jawab</option>
                                    @foreach($pic as $p)
                                        <option value="{{ $p->user_id }}">{{ $p->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nama Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kegiatan_jurusan" maxlength="200" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Kegiatan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi_kegiatan" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Lokasi Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="lokasi_kegiatan" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_mulai" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_selesai" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Penyelenggara <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="penyelenggara" maxlength="150" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Prodi -->
<div class="modal fade" id="modalKegiatanProdi" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Form Kegiatan Program Studi</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formKegiatanProdi" novalidate>
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="kegiatan_program_studi_id" id="kegiatan_program_studi_id">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Surat Tugas <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="surat_id" id="surat_id_prodi" required>
                                    <option value="">Pilih Surat Tugas</option>
                                    @foreach($surat as $s)
                                        <option value="{{ $s->surat_id }}">{{ $s->nomer_surat }} - {{ $s->judul_surat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Penanggung Jawab <span class="text-danger">*</span></label>
                                <select class="form-control select2" name="user_id" id="user_id_prodi" required>
                                    <option value="">Pilih Penanggung Jawab</option>
                                    @foreach($pic as $p)
                                        <option value="{{ $p->user_id }}">{{ $p->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nama Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kegiatan_program_studi" maxlength="200" required>
                    </div>

                    <div class="form-group">
                        <label>Deskripsi Kegiatan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi_kegiatan" rows="3" required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Lokasi Kegiatan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="lokasi_kegiatan" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Mulai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_mulai" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Selesai <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_selesai" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Penyelenggara <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="penyelenggara" maxlength="150" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detail Kegiatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 200px">Nama Kegiatan</th>
                                <td id="detail_nama_kegiatan"></td>
                            </tr>
                            <tr>
                                <th>Penanggung Jawab</th>
                                <td id="detail_penanggung_jawab"></td>
                            </tr>
                            <tr>
                                <th>Surat Tugas</th>
                                <td id="detail_surat_tugas"></td>
                            </tr>
                            <tr>
                                <th>Deskripsi</th>
                                <td id="detail_deskripsi"></td>
                            </tr>
                            <tr>
                                <th>Lokasi</th>
                                <td id="detail_lokasi"></td>
                            </tr>
                            <tr>
                                <th>Periode</th>
                                <td id="detail_periode"></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td id="detail_status"></td>
                            </tr>
                            <tr>
                                <th>Penyelenggara</th>
                                <td id="detail_penyelenggara"></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<style>
    .container-fluid {
        padding-right: 0;
        padding-left: 0;
    }
    
    .card {
        margin-bottom: 0;
        border-radius: 0;
    }
    
    .table-responsive {
        min-height: 400px;
    }
    
    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
    }
    .nav-tabs .nav-link.active {
        background-color: #03346E;
        color: white !important;
        border: none;
    }
    
    .tab-content {
        padding: 20px;
        background-color: #fff;
    }
    
    .dataTables_wrapper {
        padding: 0;
        width: 100%;
    }

    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white !important;
    }
    
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: black !important;
    }
    
    .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white !important;
    }
    
    .btn {
        margin-right: 5px;
        padding: 5px 10px;
    }
    
    .btn i {
        margin-right: 5px;
    }

    .select2-container {
        z-index: 9999;
    }

    .modal {
        z-index: 1050 !important;
    }

    .select2-dropdown {
        z-index: 1060 !important;
    }

    .select2-container--open {
        z-index: 1060 !important;
    }

    .badge {
        padding: 0.4em 0.8em;
    }

    .btn-group1 {
        display: flex;
        gap: 5px;
    }

    .btn-group1 .btn {
        padding: 5px 10px;
        font-size: 14px;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Select2
    $('#surat_id_jurusan, #user_id_jurusan').select2({
        theme: 'bootstrap4',
        width: '100%',
        dropdownParent: $('#modalKegiatanJurusan')
    });

    $('#surat_id_prodi, #user_id_prodi').select2({
        theme: 'bootstrap4',
        width: '100%',
        dropdownParent: $('#modalKegiatanProdi')
    });

    // Initialize DataTables
    let tableKegiatanJurusan = $('#tableKegiatanJurusan').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.dosen.agenda.jurusan.data') }}", 
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_kegiatan_jurusan', name: 'nama_kegiatan_jurusan' },
            { data: 'penanggung_jawab', name: 'penanggung_jawab' },
            { data: 'surat_tugas', name: 'surat_tugas' },
            { data: 'periode', name: 'periode' },
            { 
                data: 'status_kegiatan',
                render: function(data) {
                    return `<span class="badge badge-${data === 'berlangsung' ? 'success' : 'secondary'}">
                        ${data.charAt(0).toUpperCase() + data.slice(1)}
                    </span>`;
                }
            },
            { 
                data: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-info btn-sm detail-btn" data-id="${row.kegiatan_jurusan_id}">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        <button class="btn btn-warning btn-sm edit-btn" data-id="${row.kegiatan_jurusan_id}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="${row.kegiatan_jurusan_id}">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    `;
                }
            }
        ],
        order: [[1, 'asc']]
    });

    let tableKegiatanProdi = $('#tableKegiatanProdi').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.dosen.agenda.prodi.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_kegiatan_program_studi', name: 'nama_kegiatan_program_studi' },
            { data: 'penanggung_jawab', name: 'penanggung_jawab' },
            { data: 'surat_tugas', name: 'surat_tugas' },
            { data: 'periode', name: 'periode' },
            { 
                data: 'status_kegiatan',
                render: function(data) {
                    return `<span class="badge badge-${data === 'berlangsung' ? 'success' : 'secondary'}">
                        ${data.charAt(0).toUpperCase() + data.slice(1)}
                    </span>`;
                }
            },
            { 
                data: 'action',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `<div class="btn-group1">
                        <button class="btn btn-info btn-sm detail-btn" data-id="${row.kegiatan_program_studi_id}">
                            <i class="fas fa-eye"></i> Detail
                        </button>
                        <button class="btn btn-warning btn-sm edit-btn" data-id="${row.kegiatan_program_studi_id}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-danger btn-sm delete-btn" data-id="${row.kegiatan_program_studi_id}">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>`;
                }
            }
        ],
        order: [[1, 'asc']]
    });

    // Add Button Handlers
    $('#tambahKegiatanJurusan').click(function() {
        $('#formKegiatanJurusan')[0].reset();
        $('#kegiatan_jurusan_id').val('');
        $('#surat_id_jurusan, #user_id_jurusan').val('').trigger('change');
        $('#modalKegiatanJurusan').modal('show');
    });

    $('#tambahKegiatanProdi').click(function() {
        $('#formKegiatanProdi')[0].reset();
        $('#kegiatan_program_studi_id').val('');
        $('#surat_id_prodi, #user_id_prodi').val('').trigger('change');
        $('#modalKegiatanProdi').modal('show');
    });

    // Form Validation and Submit
    function validateForm(formData, isJurusan) {
        let errors = [];
        let prefix = isJurusan ? 'jurusan' : 'prodi';
        
        if (!formData.get('surat_id')) errors.push('Surat Tugas harus dipilih');
        if (!formData.get('user_id')) errors.push('Penanggung Jawab harus dipilih');
        if (!formData.get(`nama_kegiatan_${isJurusan ? 'jurusan' : 'program_studi'}`)) 
            errors.push('Nama Kegiatan harus diisi');
        if (!formData.get('deskripsi_kegiatan')) errors.push('Deskripsi Kegiatan harus diisi');
        if (!formData.get('lokasi_kegiatan')) errors.push('Lokasi Kegiatan harus diisi');
        if (!formData.get('tanggal_mulai')) errors.push('Tanggal Mulai harus diisi');
        if (!formData.get('tanggal_selesai')) errors.push('Tanggal Selesai harus diisi');
        if (!formData.get('penyelenggara')) errors.push('Penyelenggara harus diisi');

        return errors;
    }

    function handleFormSubmit(form, isJurusan) {
        let formData = new FormData(form);
        let errors = validateForm(formData, isJurusan);
        
        if (errors.length > 0) {
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                html: errors.join('<br>')
            });
            return false;
        }

        let id = isJurusan ? $('#kegiatan_jurusan_id').val() : $('#kegiatan_program_studi_id').val();
        let type = isJurusan ? 'jurusan' : 'prodi';
        let url = id 
            ? `/admin/dosen/agenda/${type}/update/${id}`
            : `/admin/dosen/agenda/${type}/store`;

        if (id) {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                let modalId = isJurusan ? '#modalKegiatanJurusan' : '#modalKegiatanProdi';
                $(modalId).modal('hide');
                
                if (isJurusan) {
                    tableKegiatanJurusan.ajax.reload(null, false);
                } else {
                    tableKegiatanProdi.ajax.reload(null, false);
                }
                
                Swal.fire('Berhasil', response.message, 'success');
            },
            error: function(xhr) {
                let errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan';
                Swal.fire('Gagal', errorMessage, 'error');
            }
        });
    }

    $('#formKegiatanJurusan').on('submit', function(e) {
        e.preventDefault();
        handleFormSubmit(this, true);
    });

    $('#formKegiatanProdi').on('submit', function(e) {
        e.preventDefault();
        handleFormSubmit(this, false);
    });

    // Edit Button Handler
    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        let isJurusan = $(this).closest('table').attr('id') === 'tableKegiatanJurusan';
        let type = isJurusan ? 'jurusan' : 'prodi';
        
        $.ajax({
            url: `/admin/dosen/agenda/${type}/${id}`,
            type: 'GET',
            success: function(response) {
                let data = response.data;
                let modalId = isJurusan ? '#modalKegiatanJurusan' : '#modalKegiatanProdi';
                
                if (isJurusan) {
                    $('#kegiatan_jurusan_id').val(data.kegiatan_jurusan_id);
                    $('#surat_id_jurusan').val(data.surat_id).trigger('change');
                    $('#user_id_jurusan').val(data.user_id).trigger('change');
                    $('[name="nama_kegiatan_jurusan"]').val(data.nama_kegiatan_jurusan);
                    $('#formKegiatanJurusan [name="deskripsi_kegiatan"]').val(data.deskripsi_kegiatan);
                    $('#formKegiatanJurusan [name="lokasi_kegiatan"]').val(data.lokasi_kegiatan);
                    $('#formKegiatanJurusan [name="tanggal_mulai"]').val(data.tanggal_mulai);
                    $('#formKegiatanJurusan [name="tanggal_selesai"]').val(data.tanggal_selesai);
                    $('#formKegiatanJurusan [name="penyelenggara"]').val(data.penyelenggara);
                } else {
                    $('#kegiatan_program_studi_id').val(data.kegiatan_program_studi_id);
                    $('#surat_id_prodi').val(data.surat_id).trigger('change');
                    $('#user_id_prodi').val(data.user_id).trigger('change');
                    $('[name="nama_kegiatan_program_studi"]').val(data.nama_kegiatan_program_studi);
                    $('#formKegiatanProdi [name="deskripsi_kegiatan"]').val(data.deskripsi_kegiatan);
                    $('#formKegiatanProdi [name="lokasi_kegiatan"]').val(data.lokasi_kegiatan);
                    $('#formKegiatanProdi [name="tanggal_mulai"]').val(data.tanggal_mulai);
                    $('#formKegiatanProdi [name="tanggal_selesai"]').val(data.tanggal_selesai);
                    $('#formKegiatanProdi [name="penyelenggara"]').val(data.penyelenggara);
                }
                
                $(modalId).modal('show');
            }
        });
    });

    // Detail Button Handler
    $(document).on('click', '.detail-btn', function() {
        let id = $(this).data('id');
        let isJurusan = $(this).closest('table').attr('id') === 'tableKegiatanJurusan';
        let type = isJurusan ? 'jurusan' : 'prodi';
        
        $.ajax({
            url: `/admin/dosen/agenda/${type}/${id}`,
            type: 'GET',
            success: function(response) {
                let data = response.data;
                
                $('#detail_nama_kegiatan').text(isJurusan ? data.nama_kegiatan_jurusan : data.nama_kegiatan_program_studi);
                $('#detail_penanggung_jawab').text(data.user.nama_lengkap);
                $('#detail_surat_tugas').text(data.surat.nomer_surat);
                $('#detail_deskripsi').text(data.deskripsi_kegiatan);
                $('#detail_lokasi').text(data.lokasi_kegiatan);
                $('#detail_periode').text(data.tanggal_mulai + ' s/d ' + data.tanggal_selesai);
                $('#detail_status').html(`<span class="badge badge-${data.status_kegiatan === 'berlangsung' ? 'success' : 'secondary'}">
                    ${data.status_kegiatan.charAt(0).toUpperCase() + data.status_kegiatan.slice(1)}
                </span>`);
                $('#detail_penyelenggara').text(data.penyelenggara);
                
                $('#modalDetail').modal('show');
            }
        });
    });

    // Delete Button Handler
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        let isJurusan = $(this).closest('table').attr('id') === 'tableKegiatanJurusan';
        let type = isJurusan ? 'jurusan' : 'prodi';
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/dosen/agenda/${type}/delete/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (isJurusan) {
                            tableKegiatanJurusan.ajax.reload(null, false);
                        } else {
                            tableKegiatanProdi.ajax.reload(null, false);
                        }
                        Swal.fire('Terhapus!', response.message, 'success');
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    });

    // Date Validation
    $('input[name="tanggal_mulai"], input[name="tanggal_selesai"]').on('change', function() {
        let form = $(this).closest('form');
        let tanggalMulai = new Date(form.find('input[name="tanggal_mulai"]').val());
        let tanggalSelesai = new Date(form.find('input[name="tanggal_selesai"]').val());
        
        if (tanggalSelesai < tanggalMulai) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Tanggal selesai harus setelah tanggal mulai!'
            });
            $(this).val('');
        }
    });

    // Modal Cleanup
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        $(this).find('select').val('').trigger('change');
        $('#kegiatan_jurusan_id, #kegiatan_program_studi_id').val('');
        $('.is-invalid').removeClass('is-invalid');
    });

    // DataTable Language Configuration
    $.extend(true, $.fn.dataTable.defaults, {
        language: {
            processing: 'Sedang memproses...',
            search: 'Pencarian:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ hingga _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data yang dapat ditampilkan',
            infoFiltered: '(difilter dari _MAX_ total data)',
            loadingRecords: 'Memuat data...',
            zeroRecords: 'Tidak ditemukan data yang sesuai',
            emptyTable: 'Tidak ada data yang tersedia',
            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Selanjutnya',
                previous: 'Sebelumnya'
            }
        }
    });

    // Select2 Z-index Fix
    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });
});
</script>
@endpush