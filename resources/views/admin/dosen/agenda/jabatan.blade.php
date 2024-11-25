@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Pilih Jabatan</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Jabatan
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="tabelJabatan" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Hak Akses</th>
                            <th>NIDN</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan Fungsional</th>
                            <th>Pilih Jabatan</th>
                            <th>Nama Kegiatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($jabatan as $key => $jab)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $jab->level->level_nama }}</td>
                            <td>{{ $jab->user->nidn }}</td>
                            <td>{{ $jab->user->nama_lengkap }}</td>
                            <td>{{ $jab->user->jabatan_fungsional }}</td>
                            <td>{{ str_replace('_', ' ', ucwords($jab->jabatan)) }}</td>
                            <td>
                                @if($jab->kegiatanLuarInstitusi)
                                    {{ $jab->kegiatanLuarInstitusi->nama_kegiatan_luar_institusi }}
                                @elseif($jab->kegiatanInstitusi)
                                    {{ $jab->kegiatanInstitusi->nama_kegiatan_institusi }}
                                @elseif($jab->kegiatanJurusan)
                                    {{ $jab->kegiatanJurusan->nama_kegiatan_jurusan }}
                                @elseif($jab->kegiatanProgramStudi)
                                    {{ $jab->kegiatanProgramStudi->nama_kegiatan_program_studi }}
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-warning btn-sm" onclick="editJabatan({{ $jab->jabatan_id }})">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteJabatan({{ $jab->jabatan_id }})">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jabatan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formTambah">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Dosen <span class="text-danger">*</span></label>
                        <select name="user_id" class="form-control select2" required>
                            <option value="">Pilih Dosen</option>
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}">
                                    {{ $user->nama_lengkap }} ({{ $user->nidn }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Level <span class="text-danger">*</span></label>
                        <select name="level_id" class="form-control select2" required>
                            <option value="">Pilih Level</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->level_id }}">{{ $level->level_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jabatan <span class="text-danger">*</span></label>
                        <select name="jabatan" class="form-control" required>
                            <option value="">Pilih Jabatan</option>
                            <option value="ketua_pelaksana">Ketua Pelaksana</option>
                            <option value="sekertaris">Sekertaris</option>
                            <option value="bendahara">Bendahara</option>
                            <option value="anggota">Anggota</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kegiatan <span class="text-danger">*</span></label>
                        <select name="jenis_kegiatan" class="form-control" required>
                            <option value="">Pilih Jenis Kegiatan</option>
                            <option value="luar">Kegiatan Luar Institusi</option>
                            <option value="institusi">Kegiatan Institusi</option>
                            <option value="jurusan">Kegiatan Jurusan</option>
                            <option value="prodi">Kegiatan Program Studi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kegiatan <span class="text-danger">*</span></label>
                        <select name="kegiatan_id" class="form-control" required>
                            <option value="">Pilih Kegiatan</option>
                        </select>
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

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jabatan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formEdit">
                @csrf
                @method('PUT')
                <input type="hidden" name="jabatan_id" id="edit_jabatan_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Dosen <span class="text-danger">*</span></label>
                        <select name="user_id" id="edit_user_id" class="form-control select2" required>
                            <option value="">Pilih Dosen</option>
                            @foreach($users as $user)
                                <option value="{{ $user->user_id }}">
                                    {{ $user->nama_lengkap }} ({{ $user->nidn }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Level <span class="text-danger">*</span></label>
                        <select name="level_id" id="edit_level_id" class="form-control select2" required>
                            <option value="">Pilih Level</option>
                            @foreach($levels as $level)
                                <option value="{{ $level->level_id }}">{{ $level->level_nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jabatan <span class="text-danger">*</span></label>
                        <select name="jabatan" id="edit_jabatan" class="form-control" required>
                            <option value="">Pilih Jabatan</option>
                            <option value="ketua_pelaksana">Ketua Pelaksana</option>
                            <option value="sekertaris">Sekertaris</option>
                            <option value="bendahara">Bendahara</option>
                            <option value="anggota">Anggota</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kegiatan <span class="text-danger">*</span></label>
                        <select name="jenis_kegiatan" id="edit_jenis_kegiatan" class="form-control" required>
                            <option value="">Pilih Jenis Kegiatan</option>
                            <option value="luar">Kegiatan Luar Institusi</option>
                            <option value="institusi">Kegiatan Institusi</option>
                            <option value="jurusan">Kegiatan Jurusan</option>
                            <option value="prodi">Kegiatan Program Studi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kegiatan <span class="text-danger">*</span></label>
                        <select name="kegiatan_id" id="edit_kegiatan_id" class="form-control" required>
                            <option value="">Pilih Kegiatan</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
<style>
    .btn-group {
        display: flex;
        gap: 5px;
    }
    
    .table td {
        white-space: nowrap;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    $('#tabelJabatan').DataTable({
        "order": [],
        "columnDefs": [
            { "orderable": false, "targets": [0, 1, 7] } 
        ],
        responsive: true,
        language: {
            url: "{{ asset('adminlte/plugins/datatables/language/Indonesian.json') }}"
        }

    });

    // Inisialisasi Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Handler untuk auto-populate level
    $('#formTambah select[name="user_id"], #formEdit select[name="user_id"]').on('change', function() {
        const userId = $(this).val();
        const levelSelect = $(this).closest('form').find('select[name="level_id"]');
        
        if (userId) {
            $.ajax({
                url: `/admin/dosen/agenda/jabatan/get-user-level/${userId}`,
                method: 'GET',
                beforeSend: function() {
                    levelSelect.prop('disabled', true);
                },
                success: function(response) {
                    if (response.status === 'success') {
                        levelSelect.val(response.data.level_id).trigger('change');
                    }
                },
                error: function(error) {
                    alert('Gagal mengambil data level user');
                    levelSelect.prop('disabled', false);
                }
            });
        } else {
            levelSelect.val('').trigger('change');
            levelSelect.prop('disabled', false);
        }
    });

    // Handler untuk perubahan jenis kegiatan
    function loadKegiatan(jenisKegiatanSelect, targetSelect) {
        const jenisKegiatan = jenisKegiatanSelect.val();
        
        if (jenisKegiatan) {
            $.ajax({
                url: '{{ route("admin.dosen.agenda.jabatan.getKegiatan") }}',
                method: 'GET',
                data: { jenis_kegiatan: jenisKegiatan },
                beforeSend: function() {
                    targetSelect.prop('disabled', true);
                },
                success: function(response) {
                    targetSelect.empty().append('<option value="">Pilih Kegiatan</option>');
                    if (response.status === 'success') {
                        response.data.forEach(function(item) {
                            targetSelect.append(`<option value="${item.id}">${item.nama}</option>`);
                        });
                    }
                },
                error: function(error) {
                    alert('Gagal mengambil data kegiatan');
                },
                complete: function() {
                    targetSelect.prop('disabled', false);
                }
            });
        } else {
            targetSelect.empty().append('<option value="">Pilih Kegiatan</option>');
        }
    }

    // Bind handler ke form tambah dan edit
    $('#formTambah select[name="jenis_kegiatan"]').on('change', function() {
        loadKegiatan($(this), $('#formTambah select[name="kegiatan_id"]'));
    });

    $('#formEdit select[name="jenis_kegiatan"]').on('change', function() {
        loadKegiatan($(this), $('#formEdit select[name="kegiatan_id"]'));
    });

    // Handler submit form tambah
    $('#formTambah').on('submit', function(e) {
        e.preventDefault();
        
        // Enable semua field untuk submit
        $(this).find('select').prop('disabled', false);
        
        $.ajax({
            url: '{{ route("admin.dosen.agenda.jabatan.store") }}',
            method: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                $('#formTambah button[type="submit"]').prop('disabled', true);
            },
            success: function(response) {
                if(response.status === 'success') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(error) {
                Swal.fire('Error!', error.responseJSON?.message || 'Terjadi kesalahan pada server', 'error');
            },
            complete: function() {
                $('#formTambah button[type="submit"]').prop('disabled', false);
            }
        });
    });

    // Handler tombol edit
    $('.edit-btn').click(function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: `/admin/dosen/agenda/jabatan/${id}/edit`,
            method: 'GET',
            beforeSend: function() {
                $('#formEdit').trigger('reset');
                $('#formEdit select').prop('disabled', true);
            },
            success: function(response) {
                if (response.status === 'success') {
                    const data = response.data;
                    
                    $('#edit_jabatan_id').val(data.jabatan_id);
                    $('#edit_user_id').val(data.user_id).trigger('change');
                    $('#edit_level_id').val(data.level_id).trigger('change');
                    $('#edit_jabatan').val(data.jabatan);
                    
                    // Tentukan jenis kegiatan dan ID kegiatan
                    let jenisKegiatan = '';
                    let kegiatanId = '';
                    
                    if (data.kegiatan_luar_institusi_id) {
                        jenisKegiatan = 'luar';
                        kegiatanId = data.kegiatan_luar_institusi_id;
                    } else if (data.kegiatan_institusi_id) {
                        jenisKegiatan = 'institusi';
                        kegiatanId = data.kegiatan_institusi_id;
                    } else if (data.kegiatan_jurusan_id) {
                        jenisKegiatan = 'jurusan';
                        kegiatanId = data.kegiatan_jurusan_id;
                    } else if (data.kegiatan_program_studi_id) {
                        jenisKegiatan = 'prodi';
                        kegiatanId = data.kegiatan_program_studi_id;
                    }
                    
                    $('#edit_jenis_kegiatan').val(jenisKegiatan).trigger('change');
                    
                    setTimeout(() => {
                        $('#edit_kegiatan_id').val(kegiatanId);
                    }, 1000);
                    
                    $('#formEdit select').prop('disabled', false);
                    $('#edit_level_id').prop('disabled', true);
                    
                    $('#modalEdit').modal('show');
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(error) {
                Swal.fire('Error!', 'Gagal mengambil data jabatan', 'error');
            }
        });
    });

    // Handler submit form edit
    $('#formEdit').on('submit', function(e) {
        e.preventDefault();
        
        const id = $('#edit_jabatan_id').val();
        
        // Enable semua field untuk submit
        $(this).find('select').prop('disabled', false);
        
        $.ajax({
            url: `/admin/dosen/agenda/jabatan/${id}`,
            method: 'PUT',
            data: $(this).serialize(),
            beforeSend: function() {
                $('#formEdit button[type="submit"]').prop('disabled', true);
            },
            success: function(response) {
                if(response.status === 'success') {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error!', response.message, 'error');
                }
            },
            error: function(error) {
                Swal.fire('Error!', error.responseJSON?.message || 'Terjadi kesalahan pada server', 'error');
            },
            complete: function() {
                $('#formEdit button[type="submit"]').prop('disabled', false);
            }
        });
    });

    // Handler tombol hapus
    $('.delete-btn').click(function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/dosen/agenda/jabatan/${id}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if(response.status === 'success') {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(error) {
                        Swal.fire('Error!', 'Gagal menghapus data', 'error');
                    }
                });
            }
        });
    });

    // Reset form saat modal ditutup
    $('#modalTambah').on('hidden.bs.modal', function() {
        $('#formTambah').trigger('reset');
        $('#formTambah select').val('').trigger('change');
        $('#formTambah select').prop('disabled', false);
    });

    $('#modalEdit').on('hidden.bs.modal', function() {
        $('#formEdit').trigger('reset');
        $('#formEdit select').val('').trigger('change');
        $('#formEdit select').prop('disabled', false);
    });

    // Sweet Alert untuk notifikasi dari session
    @if(session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: '{{ session("success") }}',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Error!',
            text: '{{ session("error") }}',
            icon: 'error'
        });
    @endif
});
</script>
@endpush