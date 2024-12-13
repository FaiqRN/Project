@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <!-- Info Kegiatan -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Kegiatan</h3>
                </div>
                <div class="card-body">
                    @if($kegiatanJurusan)
                        <div class="alert alert-info">
                            <h5><i class="icon fas fa-info"></i> Kegiatan Jurusan</h5>
                            <p style="margin-top: 10px;"><strong>Nama Kegiatan:</strong> {{ $kegiatanJurusan->nama_kegiatan_jurusan }} <br>
                            <strong>Periode:</strong> {{ date('d/m/Y', strtotime($kegiatanJurusan->tanggal_mulai)) }} - 
                                                      {{ date('d/m/Y', strtotime($kegiatanJurusan->tanggal_selesai)) }} 
                                                      <strong>|</strong>
                            <strong style="margin-left: 5px;">Surat Tugas:</strong> {{ $kegiatanJurusan->surat->judul_surat ?? '-' }}
                        </div>
                    @endif

                    @if($kegiatanProdi)
                        <div class="alert alert-success">
                            <h5><i class="icon fas fa-info"></i> Kegiatan Program Studi</h5>
                            <p style="margin-top: 10px;"><strong>Nama Kegiatan:</strong> {{ $kegiatanProdi->nama_kegiatan_program_studi }} <br>
                            <strong>Periode:</strong> {{ date('d/m/Y', strtotime($kegiatanProdi->tanggal_mulai)) }} - 
                                                      {{ date('d/m/Y', strtotime($kegiatanProdi->tanggal_selesai)) }}
                                                      <strong>|</strong>
                            <strong style="margin-left: 5px;">Surat Tugas:</strong> {{ $kegiatanProdi->surat->judul_surat ?? '-' }}</p>
                        </div>
                    @endif

                    @if(!$kegiatanJurusan && !$kegiatanProdi)
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Perhatian</h5>
                            <p>Anda belum ditugaskan pada kegiatan apapun.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Data Anggota -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Anggota Agenda</h3>
        </div>
        <div class="card-body">
            @if($kegiatanJurusan || $kegiatanProdi)
                <div class="mb-3">
                    <button type="button" class="btn btn-primary" onclick="showModal()">
                        <i class="fas fa-plus"></i> Tambah User
                    </button>
                </div>

                <div class="table-responsive">
                    <table id="tabelAnggota" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Judul Agenda</th>
                                <th>Nama Lengkap</th>
                                <th>NIDN</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    Silahkan tunggu penugasan dari admin untuk dapat mengelola anggota agenda.
                </div>
            @endif
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
                        <label>Daftar Anggota:</label>
                        <div id="anggotaContainer">
                            <!-- Baris anggota akan ditambahkan di sini -->
                            <div class="anggota-row mb-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">1.</span>
                                    </div>
                                    <select name="user_id[]" class="form-control select2-anggota" required>
                                        <option value="">-- Pilih Anggota --</option>
                                        @foreach($dosens as $dosen)
                                            <option value="{{ $dosen->user_id }}">{{ $dosen->nama_lengkap }} ({{ $dosen->nidn }})</option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-danger" onclick="removeAnggotaRow(this)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <small class="form-text text-muted">Minimal pilih anggota sebanyak 2 user</small>
                        <button type="button" class="btn btn-success btn-sm mt-2" onclick="addAnggotaRow()">
                            <i class="fas fa-plus"></i> Tambah Anggota
                        </button>
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
    .btn-group .btn + .btn {
    margin-left: 8px;
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Inisialisasi Select2 untuk agenda
    $('#agenda_id').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Inisialisasi Select2 untuk anggota pertama
    initializeSelect2ForRow($('.select2-anggota').first());

    // Inisialisasi DataTable
    let table = $('#tabelAnggota').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('pic.pilih.data') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'nama_agenda', name: 'nama_agenda'},
            {data: 'nama_lengkap', name: 'nama_lengkap'},
            {data: 'nidn', name: 'nidn'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ]
    });

    // Reset form saat modal ditutup
        $('#modalForm').on('hidden.bs.modal', function () {
        resetForm();
        $('#agenda_id').prop('disabled', false);
    });
});

function showModal() {
    resetForm();
    $('#formAnggota').removeAttr('data-mode').removeAttr('data-id');
    $('#agenda_id').prop('disabled', false);
    $('#modalForm').modal('show');
    addAnggotaRow(); // Tambah satu baris untuk mode tambah
}

function saveData(e) {
    e.preventDefault();
    
    let mode = $('#formAnggota').attr('data-mode');
    let id = $('#formAnggota').attr('data-id');
    
    let formData = {
        _token: $('meta[name="csrf-token"]').attr('content'),
        agenda_id: $('#agenda_id').val(),
        user_id: $('select[name="user_id[]"]').first().val() // Ambil hanya anggota pertama saat edit
    };

    // Tentukan URL dan method berdasarkan mode
    let url = mode === 'edit' 
        ? "{{ route('pic.pilih.update', ':id') }}".replace(':id', id)
        : "{{ route('pic.pilih.store') }}";
    let method = mode === 'edit' ? 'PUT' : 'POST';

    // Jika mode store (bukan edit), kirim semua user_ids
    if (mode !== 'edit') {
        formData.user_ids = $('select[name="user_id[]"]').map(function() {
            return $(this).val();
        }).get();
        delete formData.user_id;
    }

    $.ajax({
        url: url,
        method: method,
        data: formData,
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
    $.get("{{ route('pic.pilih.edit', ':id') }}".replace(':id', id), function(data) {
        if(data.success) {
            resetForm(); // Reset form terlebih dahulu
            
            // Set agenda_id dan disable dropdown agenda
            $('#agenda_id').val(data.data.agenda_id).trigger('change').prop('disabled', true);
            
            // Set user_id pada baris pertama
            $('.select2-anggota').first().val(data.data.user_id).trigger('change');
            
            $('#modalForm').modal('show');
            
            // Ubah URL form untuk update
            $('#formAnggota').attr('data-mode', 'edit');
            $('#formAnggota').attr('data-id', id);
        } else {
            Swal.fire('Error', data.message, 'error');
        }
    });
}

function deleteData(id) {
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
                url: "{{ route('pic.pilih.delete', ':id') }}".replace(':id', id),
                type: 'DELETE', // Menggunakan type alih-alih method
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

function initializeSelect2ForRow($element) {
    $element.select2({
        theme: 'bootstrap4',
        width: '100%',
        dropdownParent: $('#modalForm')
    });
}

function addAnggotaRow() {
    let rowCount = $('.anggota-row').length + 1;
    let newRow = `
        <div class="anggota-row mb-2">
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">${rowCount}.</span>
                </div>
                <select name="user_id[]" class="form-control select2-anggota" required>
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($dosens as $dosen)
                        <option value="{{ $dosen->user_id }}">{{ $dosen->nama_lengkap }} ({{ $dosen->nidn }})</option>
                    @endforeach
                </select>
                <div class="input-group-append">
                    <button type="button" class="btn btn-danger" onclick="removeAnggotaRow(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    $('#anggotaContainer').append(newRow);
    initializeSelect2ForRow($('#anggotaContainer .anggota-row:last-child .select2-anggota'));
    updateRowNumbers();
}

function removeAnggotaRow(button) {
    $(button).closest('.anggota-row').remove();
    updateRowNumbers();
}

function updateRowNumbers() {
    $('.anggota-row').each(function(index) {
        $(this).find('.input-group-text').text((index + 1) + '.');
    });
}

function resetForm() {
    $('#formAnggota')[0].reset();
    $('#formAnggota').removeAttr('data-mode').removeAttr('data-id');
    $('#agenda_id').prop('disabled', false);
    
    // Reset container anggota
    $('#anggotaContainer').html('');
    
    // Jika bukan mode edit, tambahkan baris kosong
    if (!$('#formAnggota').attr('data-mode')) {
        addAnggotaRow();
    }
    
    // Reset semua select2
    $('#agenda_id').val('').trigger('change');
}
</script>
@endpush