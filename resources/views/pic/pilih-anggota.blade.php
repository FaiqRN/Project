@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Manajemen Anggota</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Agenda & Anggota</h3>
                </div>
                <div class="card-body">
                    <!-- Form Section -->
                    <div class="row">
                        <div class="col-12">
                            <form id="formAnggota" class="bg-gray-light p-3 rounded mb-4">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="nidn" class="form-control mb-2" placeholder="NIDN" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="nama_anggota" class="form-control mb-2" placeholder="Nama Anggota" required>
                                </div>
                                <div class="form-group">
                                    <input type="text" name="nama_agenda" class="form-control mb-2" placeholder="Nama Agenda" required>
                                </div>
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-save mr-2"></i>Simpan
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Table Section -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="tabelAnggota" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">No</th>
                                            <th>NIDN</th>
                                            <th>Nama Anggota</th>
                                            <th>Tanggal</th>
                                            <th width="15%">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Anggota</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>NIDN</label>
                        <input type="text" class="form-control" name="nidn" id="edit_nidn" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Anggota</label>
                        <input type="text" class="form-control" name="nama_anggota" id="edit_nama_anggota" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Agenda</label>
                        <input type="text" class="form-control" name="nama_agenda" id="edit_nama_agenda" required>
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
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
<style>
.bg-gray-light {
    background-color: #f8f9fa;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>

<script>
    $(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        var table = $('#tabelAnggota').DataTable({
            responsive: true,
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pic.pilih-anggota.data') }}",
                type: 'GET'
            },
            columns: [
                {
                    data: null,
                    orderable: false,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                { data: 'nidn' },
                { data: 'nama_anggota' },
                { 
                    data: 'tanggal_agenda',
                    render: function(data) {
                        return moment(data).format('DD-MM-YYYY');
                    }
                },
                {
                    data: 'agenda_id',
                    orderable: false,
                    render: function(data) {
                        return `
                            <button type="button" class="btn btn-sm btn-primary edit-btn" data-id="${data}">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-btn" data-id="${data}">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        `;
                    }
                }
            ],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    
        // Handle form submission untuk tambah data
        $('#formAnggota').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('pic.pilih-anggota.store') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.status === 'success') {
                        Swal.fire('Berhasil!', response.message, 'success');
                        $('#formAnggota')[0].reset();
                        table.ajax.reload();
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON.message, 'error');
                }
            });
        });
    
        // Handle edit button
        $(document).on('click', '.edit-btn', function() {
            var id = $(this).data('id');
            $('#edit_id').val(id);
            
            $.ajax({
                url: "{{ route('pic.pilih-anggota.edit', '') }}/" + id,
                method: 'GET',
                success: function(response) {
                    $('#edit_nidn').val(response.nidn);
                    $('#edit_nama_anggota').val(response.nama_anggota);
                    $('#edit_nama_agenda').val(response.nama_agenda);
                    $('#editModal').modal('show');
                }
            });
        });
    
        // Handle edit form submission
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            var id = $('#edit_id').val();
            
            $.ajax({
                url: "{{ route('pic.pilih-anggota.update', '') }}/" + id,
                method: 'PUT',
                data: $(this).serialize(),
                success: function(response) {
                    if(response.status === 'success') {
                        $('#editModal').modal('hide');
                        Swal.fire('Berhasil!', response.message, 'success');
                        table.ajax.reload();
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error!', xhr.responseJSON.message, 'error');
                }
            });
        });
    
        // Handle delete button
        $(document).on('click', '.delete-btn', function() {
            var id = $(this).data('id');
            
            Swal.fire({
                title: 'Hapus Anggota?',
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
                        url: "{{ route('pic.pilih-anggota.delete', '') }}/" + id,
                        method: 'DELETE',
                        success: function(response) {
                            if(response.status === 'success') {
                                Swal.fire('Terhapus!', response.message, 'success');
                                table.ajax.reload();
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', xhr.responseJSON.message, 'error');
                        }
                    });
                }
            });
        });
    });
    </script>
@endpush