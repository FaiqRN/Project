@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manajemen Agenda</h3>
            <div class="float-right">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahAgenda">
                    <i class="fas fa-plus"></i> Tambah Agenda
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="tabelAgenda">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kegiatan</th>
                            <th>Nama Agenda</th>
                            <th>Tanggal</th>
                            <th>Deskripsi</th>
                            <th>Dokumen</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Agenda -->
<div class="modal fade" id="modalTambahAgenda" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Agenda</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formTambahAgenda" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Tipe Kegiatan <span class="text-danger">*</span></label>
                        <select class="form-control" name="kegiatan_type" id="kegiatan_type" required>
                            <option value="">Pilih Tipe Kegiatan</option>
                            <option value="jurusan">Kegiatan Jurusan</option>
                            <option value="prodi">Kegiatan Program Studi</option>
                            <option value="institusi">Kegiatan Institusi</option>
                            <option value="luar_institusi">Kegiatan Luar Institusi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kegiatan <span class="text-danger">*</span></label>
                        <select class="form-control" name="kegiatan_id" id="kegiatan_id" required disabled>
                            <option value="">Pilih Kegiatan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nama Agenda <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_agenda" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Agenda <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal_agenda" id="tanggal_agenda" required disabled>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>File Agenda (PDF/DOC/DOCX) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" name="file_surat_agenda" accept=".pdf,.doc,.docx" >
                        <small class="text-muted">Maksimal 5MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Agenda -->
<div class="modal fade" id="modalEditAgenda" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Agenda</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditAgenda" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="agenda_id" id="edit_agenda_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Agenda <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_agenda" id="edit_nama_agenda" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Agenda <span class="text-danger">*</span></label>
                         <input type="date" class="form-control" name="tanggal_agenda" id="edit_tanggal_agenda">
                    </div>
                    <div class="form-group">
                        <label>Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi" id="edit_deskripsi" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>File Agenda (PDF/DOC/DOCX)</label>
                        <input type="file" class="form-control" name="file_surat_agenda" accept=".pdf,.doc,.docx">
                        <small class="text-muted">Kosongkan jika tidak ingin mengubah file. Maksimal 5MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detail Agenda -->
<div class="modal fade" id="modalDetailAgenda" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Agenda</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th>Nama Kegiatan</th>
                                <td id="detail_nama_kegiatan"></td>
                            </tr>
                            <tr>
                                <th>Nama Agenda</th>
                                <td id="detail_nama_agenda"></td>
                            </tr>
                            <tr>
                                <th>Tanggal</th>
                                <td id="detail_tanggal_agenda"></td>
                            </tr>
                            <tr>
                                <th>PIC</th>
                                <td id="detail_pic"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table">
                            <tr>
                                <th>Deskripsi</th>
                                <td id="detail_deskripsi"></td>
                            </tr>
                            <tr>
                                <th>Dokumen</th>
                                <td id="detail_dokumen"></td>
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
<style>
    .table td {
        vertical-align: middle !important;
    }
    .btn-group {
        display: flex;
        gap: 5px;
    }
    .table th {
        text-align: center;
        vertical-align: middle;
    }
    .dataTables_wrapper .dataTables_processing {
        background: rgba(255,255,255,0.9);
        border: 1px solid #ddd;
        border-radius: 3px;
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    var table = $('#tabelAgenda').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('admin.dosen.agenda.get-data') }}",
    columns: [
        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
        { data: 'nama_kegiatan', name: 'nama_kegiatan' },
        { data: 'nama_agenda', name: 'nama_agenda' },
        { data: 'tanggal_agenda', name: 'tanggal_agenda' },
        { data: 'deskripsi', name: 'deskripsi',orderable: false  },
        { data: 'dokumen', name: 'dokumen', orderable: false, searchable: false },
        { data: 'aksi', name: 'aksi', orderable: false, searchable: false, render: function(data, type, row) {                
            return `
                <div class="btn-group" role="group" style="gap: 5px;">
                    <button type="button" class="btn btn-warning btn-sm edit-agenda" 
                        data-id="${row.agenda_id}"
                        data-nama="${row.nama_agenda}"
                        data-tanggal_mulai="${row.tanggal_mulai}" 
                        data-tanggal_selesai="${row.tanggal_selesai}"
                        data-deskripsi="${row.deskripsi}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button type="button" class="btn btn-danger btn-sm delete-agenda" 
                        data-id="${row.agenda_id}">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>`;
            } 
        }
    ],
    columnDefs: [
        { className: "align-middle", targets: "_all" }
    ]
});

// Di dalam script, bagian handle perubahan tipe kegiatan
$('#kegiatan_type').change(function() {
    var type = $(this).val();
    var kegiatanSelect = $('#kegiatan_id');
    var tanggalInput = $('#tanggal_agenda');
    
    kegiatanSelect.prop('disabled', true);
    tanggalInput.prop('disabled', true);
    kegiatanSelect.html('<option value="">Pilih Kegiatan</option>');
    
    if (type) {
        $.ajax({
            url: "{{ route('admin.dosen.agenda.get-kegiatan') }}",
            type: 'GET',
            data: { type: type },
            success: function(response) {
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(item) {
                        var nama;
                        switch(type) {
                            case 'jurusan':
                                nama = item.nama_kegiatan_jurusan;
                                break;
                            case 'prodi':
                                nama = item.nama_kegiatan_program_studi;
                                break;
                            case 'institusi':
                                nama = item.nama_kegiatan_institusi;
                                break;
                            case 'luar_institusi':
                                nama = item.nama_kegiatan_luar_institusi;
                                break;
                        }
                        kegiatanSelect.append(
                            $('<option></option>')
                                .val(item.id)
                                .text(nama)
                                .data('tanggal_mulai', item.tanggal_mulai)
                                .data('tanggal_selesai', item.tanggal_selesai)
                        );
                    });
                    kegiatanSelect.prop('disabled', false);
                }
            }
        });
    }
});

    $('#kegiatan_id').change(function() {
        var selectedOption = $(this).find('option:selected');
        var tanggalInput = $('#tanggal_agenda');
        
        if (selectedOption.val()) {
            var tanggalMulai = selectedOption.data('tanggal_mulai');
            var tanggalSelesai = selectedOption.data('tanggal_selesai');
            
            tanggalInput.attr('min', tanggalMulai);
            tanggalInput.attr('max', tanggalSelesai);
            tanggalInput.prop('disabled', false);
        } else {
            tanggalInput.prop('disabled', true);
        }
    });

    // Handle submit form tambah
    $('#formTambahAgenda').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: "{{ route('admin.dosen.agenda.store') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Berhasil!', response.message, 'success');
                    $('#modalTambahAgenda').modal('hide');
                    table.ajax.reload();
                    $('#formTambahAgenda')[0].reset();
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    var errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value[0] + '\n';
                    });
                    Swal.fire('Error!', errorMessage, 'error');
                }
            }
        });
    });

 // Handle klik tombol edit
    $(document).on('click', '.edit-agenda', function() {
        var id = $(this).data('id');
        var tanggalMulai = $(this).data('tanggal_mulai');
        var tanggalSelesai = $(this).data('tanggal_selesai');
        
        $('#edit_agenda_id').val(id);
        $('#edit_nama_agenda').val($(this).data('nama'));
        $('#edit_tanggal_agenda').val($(this).data('tanggal'));
        $('#edit_deskripsi').val($(this).data('deskripsi'));
        
        // Set batasan tanggal
        $('#edit_tanggal_agenda').attr('min', tanggalMulai);
        $('#edit_tanggal_agenda').attr('max', tanggalSelesai);
        
        $('#modalEditAgenda').modal('show');
    });

    // Validasi perubahan tanggal
    $('#edit_tanggal_agenda').on('change', function() {
        var selectedDate = new Date($(this).val());
        if (!selectedDate) {
            return;
        }
        var minDate = new Date($(this).attr('min'));
        var maxDate = new Date($(this).attr('max'));
        
        if (selectedDate < minDate || selectedDate > maxDate) {
            Swal.fire({
                title: 'Error!',
                text: 'Tanggal harus berada dalam rentang tanggal kegiatan (' + 
                    formatDate($(this).attr('min')) + ' sampai ' + 
                    formatDate($(this).attr('max')) + ')',
                icon: 'error'
            });
            $(this).val('');
        }
    });

    // Fungsi format tanggal untuk pesan error
    function formatDate(dateString) {
        var date = new Date(dateString);
        var day = String(date.getDate()).padStart(2, '0');
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var year = date.getFullYear();
        return `${day}-${month}-${year}`;
    }

    // Handle submit form edit
    $('#formEditAgenda').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var id = $('#edit_agenda_id').val();

        $.ajax({
            url: "{{ route('admin.dosen.agenda.update', '') }}/" + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-HTTP-Method-Override': 'PUT'
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire('Berhasil!', response.message, 'success');
                    $('#modalEditAgenda').modal('hide');
                    $('#formEditAgenda')[0].reset();
                    table.ajax.reload();
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                if (errors) {
                    var errorMessage = '';
                    $.each(errors, function(key, value) {
                        errorMessage += value[0] + '\n';
                    });
                    Swal.fire('Error!', errorMessage, 'error');
                }
            }
        });
    });

    // Handle klik tombol detail
    $(document).on('click', '.detail-agenda', function() {
        var row = $(this).closest('tr');
        var data = table.row(row).data();
        
        $('#detail_nama_kegiatan').text(data.nama_kegiatan);
        $('#detail_nama_agenda').text(data.nama_agenda);
        $('#detail_tanggal_agenda').text(data.tanggal_agenda);
        $('#detail_pic').text(data.pic);
        $('#detail_deskripsi').text(data.deskripsi);
        $('#detail_dokumen').html(data.dokumen);
        
        $('#modalDetailAgenda').modal('show');
    });

    // Handle klik tombol hapus
    $(document).on('click', '.delete-agenda', function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data agenda akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('admin.dosen.agenda.delete', '') }}/" + id,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Berhasil!', response.message, 'success');
                            table.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data', 'error');
                    }
                });
            }
        });
    });

    // Preview dokumen PDF
    $(document).on('click', '.preview-dokumen', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var ext = url.split('.').pop().toLowerCase();
        
        if (ext === 'pdf') {
            // Buka PDF di tab baru
            window.open(url, '_blank');
        } else {
            // Untuk file doc/docx, langsung download
            window.location.href = url;
        }
    });

    // Reset form saat modal ditutup
    $('#modalTambahAgenda').on('hidden.bs.modal', function () {
        $('#formTambahAgenda')[0].reset();
        $('#kegiatan_id').prop('disabled', true).html('<option value="">Pilih Kegiatan</option>');
    });

    // Format tanggal untuk tampilan
    function formatDate(date) {
        var d = new Date(date),
            month = '' + (d.getMonth() + 1),
            day = '' + d.getDate(),
            year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        return [day, month, year].join('-');
    }
});
</script>
@endpush