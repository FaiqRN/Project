@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar User</h3>
                <button type="button" class="btn btn-primary btn-sm" onclick="showAddModal()">
                    <i class="fas fa-plus"></i> Tambah User
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="alert-container"></div>
            <table id="users-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>NIDN</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Jabatan Fungsional</th>
                        <th>Program Studi</th>
                        <th>Posisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Tambah User Baru</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="userForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" value="POST">
                <input type="hidden" name="user_id" id="user_id">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Pilih Posisi <span class="text-danger">*</span></label>
                                <select name="level_id" id="level_id" class="form-control">
                                    <option value="">Pilih Posisi Jabatan</option>
                                    <option value="2">Kaprodi</option>
                                    <option value="3">Dosen</option>
                                    <option value="4">PIC</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>NIDN <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nidn" id="nidn">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="username" id="username">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Password <span class="text-danger password-required">*</span></label>
                                <input type="password" class="form-control" name="password" id="password">
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama_lengkap" id="nama_lengkap">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gelar Depan</label>
                                <input type="text" class="form-control" name="gelar_depan" id="gelar_depan">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gelar Belakang</label>
                                <input type="text" class="form-control" name="gelar_belakang" id="gelar_belakang">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Program Studi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="program_studi" id="program_studi">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jabatan Fungsional <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="jabatan_fungsional" id="jabatan_fungsional">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Foto</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="foto" name="foto">
                                <label class="custom-file-label" for="foto">Pilih file</label>
                            </div>
                        </div>
                        <div id="preview-container" class="mt-2"></div>
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
@endsection

@push('css')
<style>
    .modal-body {
        max-height: calc(100vh - 210px);
        overflow-y: auto;
    }
    #preview-container img {
        max-width: 100%;
        height: auto;
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

    // Inisialisasi DataTable
    var table = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: "{{ route('admin.users.index') }}",
        order: [],
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {
                data: 'foto', 
                name: 'foto', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return `<img src="${data || '{{ asset("img/default-user.png") }}'}" 
                            alt="Foto" class="img-thumbnail" style="max-width: 50px;">`;
                }
            },
            {data: 'nidn', name: 'nidn'},
            {data: 'nama_lengkap', name: 'nama_lengkap'},
            {data: 'username', name: 'username'},
            {data: 'jabatan_fungsional', name: 'jabatan_fungsional'},
            {data: 'program_studi', name: 'program_studi'},
            {data: 'level.level_nama', name: 'level.level_nama',orderable: false},
            {
                data: 'user_id', 
                name: 'action', 
                orderable: false, 
                searchable: false,
                render: function(data, type, row) {
                    return `
                            <button type="button" class="btn btn-warning btn-sm" onclick="showEditModal('${data}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('${data}')">
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

    // Handle form submission
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var url = $(this).attr('action');
        
        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.status == 'success') {
                    $('#userModal').modal('hide');
                    table.ajax.reload();
                    Swal.fire('Sukses!', response.message, 'success');
                }
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key).addClass('is-invalid');
                        $('#' + key).after('<div class="invalid-feedback">' + value[0] + '</div>');
                    });
                } else {
                    Swal.fire('Error!', 'Terjadi kesalahan pada server', 'error');
                }
            }
        });
    });

    // Reset form dan error messages saat modal ditutup
    $('#userModal').on('hidden.bs.modal', function() {
        $('#userForm').trigger('reset');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        $('#preview-container').empty();
    });

    // Preview foto
    $('#foto').change(function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-container').html('<img src="' + e.target.result + '" class="img-thumbnail" style="max-height: 200px">');
            }
            reader.readAsDataURL(file);
        }
    });
});

// Function untuk modal tambah
function showAddModal() {
    $('#userModal').find('.modal-title').text('Tambah User Baru');
    $('#userForm').trigger('reset');
    $('#userForm')[0].reset();
    $('#userForm').find('input[name="_method"]').val('POST');
    $('#userForm').attr('action', "{{ route('admin.users.store') }}");
    $('.password-field').show();
    $('#userModal').modal('show');
}

// Function untuk modal edit
function showEditModal(id) {
    // Reset form dan hapus pesan error sebelumnya
    $('#userForm')[0].reset();
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    // Set judul modal dan method
    $('#userModalLabel').text('Edit User');
    $('input[name="_method"]').val('PUT');
    $('#user_id').val(id);
    
    // Set action URL
    $('#userForm').attr('action', `/admin/users/${id}`);
    
    // Sembunyikan asterisk password karena opsional saat edit
    $('.password-required').hide();
    $('#password').removeAttr('required');
    
    // Tampilkan loading
    Swal.fire({
        title: 'Loading...',
        text: 'Mengambil data user',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Ambil data user
    $.ajax({
        url: `/admin/users/${id}`,
        type: 'GET',
        success: function(response) {
            Swal.close();
            
            if(response.status === 'success') {
                let user = response.data;
                
                // Set nilai untuk semua field
                $('#level_id').val(user.level_id);
                $('#username').val(user.username);
                $('#nama_lengkap').val(user.nama_lengkap);
                $('#nidn').val(user.nidn);
                $('#gelar_depan').val(user.gelar_depan);
                $('#gelar_belakang').val(user.gelar_belakang);
                $('#jabatan_fungsional').val(user.jabatan_fungsional);
                $('#program_studi').val(user.program_studi);
                
                // Tampilkan foto jika ada
                if(user.foto) {
                    $('#preview-container').html(`
                        <img src="${user.foto}" class="img-thumbnail" style="max-height: 200px">
                    `);
                }
                
                // Tampilkan modal
                $('#userModal').modal('show');
            }
        },
        error: function(xhr) {
            Swal.close();
            Swal.fire(
                'Error!',
                xhr.responseJSON?.message || 'Gagal mengambil data user',
                'error'
            );
        }
    });
}

// Tambahkan ini untuk handle form submission
$('#userForm').on('submit', function(e) {
    e.preventDefault();
    
    let formData = new FormData(this);
    let url = $(this).attr('action');
    
    // Reset error states
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').remove();
    
    // Tampilkan loading
    Swal.fire({
        title: 'Loading...',
        text: 'Menyimpan data user',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            Swal.close();
            
            if(response.status === 'success') {
                $('#userModal').modal('hide');
                $('#users-table').DataTable().ajax.reload();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        },
        error: function(xhr) {
            Swal.close();
            
            if(xhr.status === 422) {
                // Validation errors
                let errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    $(`#${key}`)
                        .addClass('is-invalid')
                        .after(`<div class="invalid-feedback">${value[0]}</div>`);
                });
            } else {
                Swal.fire(
                    'Error!',
                    xhr.responseJSON?.message || 'Terjadi kesalahan pada server',
                    'error'
                );
            }
        }
    });
});

// Function untuk menghapus data
function confirmDelete(id) {
    if (!id) {
        console.error('ID tidak valid:', id);
        Swal.fire('Error!', 'ID user tidak valid', 'error');
        return;
    }

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
            // Tampilkan loading
            Swal.fire({
                title: 'Mohon tunggu',
                text: 'Sedang menghapus data...',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Lakukan request delete
            $.ajax({
                url: "{{ url('admin/users') }}/" + id,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    console.log('Success Response:', response);
                    
                    if(response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        // Reload tabel
                        $('#users-table').DataTable().ajax.reload();
                    } else {
                        Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error Details:', {
                        xhr: xhr,
                        status: status,
                        error: error
                    });
                    
                    let errorMessage = 'Terjadi kesalahan saat menghapus data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            });
        }
    });
}

// Tambahkan fungsi untuk debugging
function handleDeleteError(error) {
    console.error('Delete Error:', error);
    let errorMessage = 'Terjadi kesalahan saat menghapus data';
    
    if (error.responseJSON && error.responseJSON.message) {
        errorMessage = error.responseJSON.message;
    }
    
    Swal.fire('Error!', errorMessage, 'error');
}
</script>
@endpush