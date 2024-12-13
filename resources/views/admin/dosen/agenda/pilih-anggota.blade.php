@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <!-- Info Kegiatan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filter Data</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Filter Kegiatan Jurusan -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kegiatan Jurusan:</label>
                                <select class="form-control select2" id="filterKegiatanJurusan">
                                    <option value="">Semua Kegiatan Jurusan</option>
                                    @foreach($kegiatanJurusan as $kegiatan)
                                        <option value="{{ $kegiatan->kegiatan_jurusan_id }}">
                                            {{ $kegiatan->nama_kegiatan_jurusan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <!-- Filter Kegiatan Prodi -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Kegiatan Program Studi:</label>
                                <select class="form-control select2" id="filterKegiatanProdi">
                                    <option value="">Semua Kegiatan Prodi</option>
                                    @foreach($kegiatanProdi as $kegiatan)
                                        <option value="{{ $kegiatan->kegiatan_program_studi_id }}">
                                            {{ $kegiatan->nama_kegiatan_program_studi }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Filter Status Anggota -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status Anggota:</label>
                                <select class="form-control" id="filterStatusAnggota">
                                    <option value="">Semua Status</option>
                                    <option value="assigned">Sudah Dipilih</option>
                                    <option value="unassigned">Belum Dipilih</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="button" class="btn btn-primary mr-2" id="btnFilter">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                            <button type="button" class="btn btn-default" id="btnResetFilter">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Anggota -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Anggota Agenda</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary" onclick="showModal()">
                    <i class="fas fa-plus"></i> Tambah Anggota
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelAnggota" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Agenda</th>
                            <th>Nama Kegiatan</th>
                            <th>Nama Anggota</th>
                            <th>NIDN</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="modalForm">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Form Anggota Agenda</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formAnggota" onsubmit="return saveData(event)">
                @csrf
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Agenda <span class="text-danger">*</span></label>
                        <select name="agenda_id" id="agenda_id" class="form-control select2" style="width: 100%;" required>
                            <option value="">-- Pilih Agenda --</option>
                            @foreach($agendas as $agenda)
                                <option value="{{ $agenda->agenda_id }}">{{ $agenda->nama_agenda }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Anggota <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-control select2" style="width: 100%;" required>
                            <option value="">-- Pilih Anggota --</option>
                            @foreach($dosens as $dosen)
                                <option value="{{ $dosen->user_id }}">{{ $dosen->nama_lengkap }} ({{ $dosen->nidn }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />
<style>
    .select2-container--bootstrap4 .select2-selection--single {
        height: calc(2.25rem + 2px) !important;
    }
    .badge {
        font-size: 90% !important;
        padding: 0.4em 0.6em !important;
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Inisialisasi DataTable
    let table = $('#tabelAnggota').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.dosen.agenda.pilih-anggota.data') }}",
            data: function(d) {
                d.kegiatan_jurusan = $('#filterKegiatanJurusan').val();
                d.kegiatan_prodi = $('#filterKegiatanProdi').val();
                d.status_anggota = $('#filterStatusAnggota').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nama_agenda', name: 'nama_agenda'},
            {data: 'nama_kegiatan', name: 'nama_kegiatan'},
            {data: 'nama_lengkap', name: 'nama_lengkap'},
            {data: 'nidn', name: 'nidn'},
            {data: 'status_anggota', name: 'status_anggota', orderable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Handle Filter
    $('#btnFilter').click(function() {
        table.ajax.reload();
    });

    // Handle Reset Filter
    $('#btnResetFilter').click(function() {
        $('#filterKegiatanJurusan').val('').trigger('change');
        $('#filterKegiatanProdi').val('').trigger('change');
        $('#filterStatusAnggota').val('').trigger('change');
        table.ajax.reload();
    });

    // Reset form saat modal ditutup
    $('#modalForm').on('hidden.bs.modal', function() {
        $('#formAnggota')[0].reset();
        $('#id').val('');
        $('.select2').val('').trigger('change');
    });
});

function showModal() {
    $('#modalForm').modal('show');
}

function saveData(e) {
    e.preventDefault();
    let id = $('#id').val();
    let url = id ? 
        "{{ route('admin.dosen.agenda.pilih-anggota.update', ':id') }}".replace(':id', id) : 
        "{{ route('admin.dosen.agenda.pilih-anggota.store') }}";
    let method = id ? 'PUT' : 'POST';

    $.ajax({
        url: url,
        method: method,
        data: $('#formAnggota').serialize(),
        success: function(response) {
            if(response.success) {
                $('#modalForm').modal('hide');
                $('#tabelAnggota').DataTable().ajax.reload();
                Swal.fire('Sukses', response.message, 'success');
            }
        },
        error: function(xhr) {
            let message = xhr.responseJSON?.message || 'Terjadi kesalahan';
            Swal.fire('Error', message, 'error');
        }
    });
    return false;
}

function editData(id) {
    $.get("{{ route('admin.dosen.agenda.pilih-anggota.edit', ':id') }}".replace(':id', id), function(data) {
        $('#id').val(id);
        $('#agenda_id').val(data.data.agenda_id).trigger('change');
        $('#user_id').val(data.data.user_id).trigger('change');
        $('#modalForm').modal('show');
    });
}

function deleteData(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anggota akan dihapus dari agenda ini!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "{{ route('admin.dosen.agenda.pilih-anggota.delete', ':id') }}".replace(':id', id),
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.success) {
                        $('#tabelAnggota').DataTable().ajax.reload();
                        Swal.fire('Sukses', response.message, 'success');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data', 'error');
                }
            });
        }
    });
}
</script>


@endpush
