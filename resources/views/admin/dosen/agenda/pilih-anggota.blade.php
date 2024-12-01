@extends('layouts.template')

@section('content')
<div class="container-fluid p-0">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Anggota Agenda</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelAnggota" class="table table-bordered table-striped w-100">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Anggota</th>
                            <th>NIDN</th>
                            <th>Tanggal</th>
                            <th>Nama Agenda</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Edit Data Anggota</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEdit" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="agenda_id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nama Anggota <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_anggota" id="edit_nama_anggota" required>
                    </div>
                    <div class="form-group">
                        <label>NIDN <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nidn" id="edit_nidn" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Agenda <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_agenda" id="edit_nama_agenda" required>
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
@endsection

@push('css')
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
    
    .dataTables_wrapper {
        padding: 0;
        width: 100%;
    }

    .btn-group {
        display: flex;
        gap: 5px;
    }

    .btn {
        margin-right: 5px;
        padding: 5px 10px;
    }
    
    .btn i {
        margin-right: 5px;
    }
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize DataTable
    let table = $('#tabelAnggota').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.dosen.agenda.pilih-anggota.data') }}",
        columns: [
            { 
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false,
                searchable: false
            },
            { data: 'nama_anggota', name: 'nama_anggota' },
            { data: 'nidn', name: 'nidn' },
            { 
                data: 'tanggal_agenda',
                name: 'tanggal_agenda',
                render: function(data) {
                    return moment(data).format('DD-MM-YYYY');
                }
            },
            { data: 'nama_agenda', name: 'nama_agenda' },
            {
                data: 'agenda_id',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-primary edit-btn" data-id="${data}">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger delete-btn" data-id="${data}">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    `;
                }
            }
        ],
        order: [[1, 'asc']],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });

    // Handle Edit Button
    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        $.ajax({
            url: "{{ url('admin/dosen/agenda/pilih-anggota/edit') }}/" + id,
            type: 'GET',
            success: function(response) {
                $('#edit_id').val(response.data.agenda_id);
                $('#edit_nama_anggota').val(response.data.nama_anggota);
                $('#edit_nidn').val(response.data.nidn);
                $('#edit_nama_agenda').val(response.data.nama_agenda);
                $('#modalEdit').modal('show');
            }
        });
    });

    // Handle Edit Form Submit
    $('#formEdit').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit_id').val();
        $.ajax({
            url: "{{ url('admin/dosen/agenda/pilih-anggota/update') }}/" + id,
            method: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                if(response.status === 'success') {
                    $('#modalEdit').modal('hide');
                    Swal.fire('Berhasil!', response.message, 'success');
                    table.ajax.reload();
                }
            },
            error: function(xhr) {
                Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
            }
        });
    });

    // Handle Delete Button
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
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
                    url: "{{ url('admin/dosen/agenda/pilih-anggota/delete') }}/" + id,
                    type: 'DELETE',
                    success: function(response) {
                        if(response.status === 'success') {
                            Swal.fire('Terhapus!', response.message, 'success');
                            table.ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                    }
                });
            }
        });
    });

    // Modal Cleanup
    $('#modalEdit').on('hidden.bs.modal', function() {
        $('#formEdit')[0].reset();
        $('.is-invalid').removeClass('is-invalid');
    });
});
</script>
@endpush