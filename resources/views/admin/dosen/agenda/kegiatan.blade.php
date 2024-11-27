@extends('layouts.template')
@section('content')
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
                <table id="tableKegiatanJurusan" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kegiatan</th>
                            <th>Penanggung Jawab</th>
                            <th>Surat Tugas</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div id="kegiatanProdi" class="tab-pane fade">
                <table id="tableKegiatanProdi" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Kegiatan</th>
                            <th>Penanggung Jawab</th>
                            <th>Surat Tugas</th>
                            <th>Periode</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Kegiatan Jurusan -->
<div class="modal fade" id="modalKegiatanJurusan" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Kegiatan Jurusan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formKegiatanJurusan">
                <div class="modal-body">
                    <input type="hidden" name="kegiatan_jurusan_id" id="kegiatan_jurusan_id">
                    <div class="form-group">
                        <label>Surat Tugas <span class="text-danger">*</span></label>
                        <select class="form-control" name="surat_id" id="surat_id_jurusan" required>
                            <option value="">Pilih Surat Tugas</option>
                            @foreach($surat as $s)
                                <option value="{{ $s->surat_id }}">{{ $s->nomer_surat }} - {{ $s->judul_surat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Penanggung Jawab <span class="text-danger">*</span></label>
                        <select class="form-control" name="user_id" id="user_id_jurusan" required>
                            <option value="">Pilih Penanggung Jawab</option>
                            @foreach($pic as $p)
                                <option value="{{ $p->user_id }}">{{ $p->nama_lengkap }}</option>
                            @endforeach
                        </select>
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
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Kegiatan Prodi -->
<div class="modal fade" id="modalKegiatanProdi" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Form Kegiatan Program Studi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formKegiatanProdi">
                <div class="modal-body">
                    <input type="hidden" name="kegiatan_program_studi_id" id="kegiatan_program_studi_id">
                    <div class="form-group">
                        <label>Surat Tugas <span class="text-danger">*</span></label>
                        <select class="form-control" name="surat_id" id="surat_id_prodi" required>
                            <option value="">Pilih Surat Tugas</option>
                            @foreach($surat as $s)
                                <option value="{{ $s->surat_id }}">{{ $s->nomer_surat }} - {{ $s->judul_surat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Penanggung Jawab <span class="text-danger">*</span></label>
                        <select class="form-control" name="user_id" id="user_id_prodi" required>
                            <option value="">Pilih Penanggung Jawab</option>
                            @foreach($pic as $p)
                                <option value="{{ $p->user_id }}">{{ $p->nama_lengkap }}</option>
                            @endforeach
                        </select>
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

<!-- Modal Detail Kegiatan -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Kegiatan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('adminlte/plugins/select2/js/select2.full.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Inisialisasi Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Inisialisasi DataTable Kegiatan Jurusan
    let tableKegiatanJurusan = $('#tableKegiatanJurusan').DataTable({
        processing: true,
        serverSide: true,
        ajax: "/admin/kegiatan/jurusan/get-data",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_kegiatan_jurusan', name: 'nama_kegiatan_jurusan' },
            { data: 'penanggung_jawab', name: 'penanggung_jawab' },
            { data: 'surat_tugas', name: 'surat_tugas' },
            { data: 'periode', name: 'periode' },
            { 
                data: 'status_kegiatan', 
                name: 'status_kegiatan',
                render: function(data) {
                    return data === 'berlangsung' 
                        ? '<span class="badge badge-success">Berlangsung</span>'
                        : '<span class="badge badge-secondary">Berakhir</span>';
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });

    // Inisialisasi DataTable Kegiatan Prodi
    let tableKegiatanProdi = $('#tableKegiatanProdi').DataTable({
        processing: true,
        serverSide: true,
        ajax: "/admin/kegiatan/prodi/get-data",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_kegiatan_program_studi', name: 'nama_kegiatan_program_studi' },
            { data: 'penanggung_jawab', name: 'penanggung_jawab' },
            { data: 'surat_tugas', name: 'surat_tugas' },
            { data: 'periode', name: 'periode' },
            { 
                data: 'status_kegiatan', 
                name: 'status_kegiatan',
                render: function(data) {
                    return data === 'berlangsung' 
                        ? '<span class="badge badge-success">Berlangsung</span>'
                        : '<span class="badge badge-secondary">Berakhir</span>';
                }
            },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });

    // Handle Tambah Kegiatan Jurusan
    $('#tambahKegiatanJurusan').click(function() {
        $('#formKegiatanJurusan')[0].reset();
        $('#kegiatan_jurusan_id').val('');
        $('#modalKegiatanJurusan .modal-title').text('Tambah Kegiatan Jurusan');
        $('#modalKegiatanJurusan').modal('show');
    });

    // Handle Tambah Kegiatan Prodi
    $('#tambahKegiatanProdi').click(function() {
        $('#formKegiatanProdi')[0].reset();
        $('#kegiatan_program_studi_id').val('');
        $('#modalKegiatanProdi .modal-title').text('Tambah Kegiatan Program Studi');
        $('#modalKegiatanProdi').modal('show');
    });

    // Handle Submit Kegiatan Jurusan
    $('#formKegiatanJurusan').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let url = $('#kegiatan_jurusan_id').val() 
            ? '/admin/kegiatan/jurusan/update/' + $('#kegiatan_jurusan_id').val()
            : '/admin/kegiatan/jurusan/store';
        let method = $('#kegiatan_jurusan_id').val() ? 'POST' : 'POST';

        if (method === 'POST') {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#modalKegiatanJurusan').modal('hide');
                tableKegiatanJurusan.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                });
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = '';
                $.each(errors, function(key, value) {
                    errorMessage += value[0] + '\n';
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errorMessage
                });
            }
        });
    });

    // Handle Submit Kegiatan Prodi
    $('#formKegiatanProdi').submit(function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        let url = $('#kegiatan_program_studi_id').val()
            ? '/admin/kegiatan/prodi/update/' + $('#kegiatan_program_studi_id').val()
            : '/admin/kegiatan/prodi/store';
        let method = $('#kegiatan_program_studi_id').val() ? 'POST' : 'POST';

        if (method === 'POST') {
            formData.append('_method', 'PUT');
        }

        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#modalKegiatanProdi').modal('hide');
                tableKegiatanProdi.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                });
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = '';
                $.each(errors, function(key, value) {
                    errorMessage += value[0] + '\n';
                });
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errorMessage
                });
            }
        });
    });

    // Handle Detail Kegiatan
    $(document).on('click', '.detail-btn', function() {
        let id = $(this).data('id');
        let type = $(this).closest('.tab-pane').attr('id');
        let url = type === 'kegiatanJurusan' 
            ? '/admin/kegiatan/jurusan/' + id
            : '/admin/kegiatan/prodi/' + id;

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                $('#detail_nama_kegiatan').text(response.data.nama_kegiatan_jurusan || response.data.nama_kegiatan_program_studi);
                $('#detail_penanggung_jawab').text(response.data.user.nama_lengkap);
                $('#detail_surat_tugas').text(response.data.surat.nomer_surat);
                $('#detail_deskripsi').text(response.data.deskripsi_kegiatan);
                $('#detail_lokasi').text(response.data.lokasi_kegiatan);
                $('#detail_periode').text(response.data.tanggal_mulai + ' s/d ' + response.data.tanggal_selesai);
                $('#detail_status').html(
                    response.data.status_kegiatan === 'berlangsung'
                        ? '<span class="badge badge-success">Berlangsung</span>'
                        : '<span class="badge badge-secondary">Berakhir</span>'
                );
                $('#detail_penyelenggara').text(response.data.penyelenggara);
                $('#modalDetail').modal('show');
            }
        });
    });

    // Handle Edit Kegiatan
    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        let type = $(this).closest('.tab-pane').attr('id');
        let url = type === 'kegiatanJurusan'
            ? '/admin/kegiatan/jurusan/' + id
            : '/admin/kegiatan/prodi/' + id;
        let modal = type === 'kegiatanJurusan'
            ? '#modalKegiatanJurusan'
            : '#modalKegiatanProdi';

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                let data = response.data;
                if (type === 'kegiatanJurusan') {
                    $('#kegiatan_jurusan_id').val(data.kegiatan_jurusan_id);
                    $('#surat_id_jurusan').val(data.surat_id);
                    $('#user_id_jurusan').val(data.user_id);
                    $('[name="nama_kegiatan_jurusan"]').val(data.nama_kegiatan_jurusan);
                } else {
                    $('#kegiatan_program_studi_id').val(data.kegiatan_program_studi_id);
                    $('#surat_id_prodi').val(data.surat_id);
                    $('#user_id_prodi').val(data.user_id);
                    $('[name="nama_kegiatan_program_studi"]').val(data.nama_kegiatan_program_studi);
                }
                
                $('[name="deskripsi_kegiatan"]').val(data.deskripsi_kegiatan);
                $('[name="lokasi_kegiatan"]').val(data.lokasi_kegiatan);
                $('[name="tanggal_mulai"]').val(data.tanggal_mulai);
                $('[name="tanggal_selesai"]').val(data.tanggal_selesai);
                $('[name="penyelenggara"]').val(data.penyelenggara);

                $(modal + ' .modal-title').text('Edit Kegiatan');
                $(modal).modal('show');
            }
        });
    });

    // Handle Delete Kegiatan
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        let type = $(this).closest('.tab-pane').attr('id');
        let url = type === 'kegiatanJurusan'
            ? '/admin/kegiatan/jurusan/delete/' + id
            : '/admin/kegiatan/prodi/delete/' + id;

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
                    url: url,
                    method: 'DELETE',
                    success: function(response) {
                        if (type === 'kegiatanJurusan') {
                            tableKegiatanJurusan.ajax.reload();
                        } else {
                            tableKegiatanProdi.ajax.reload();
                        }
                        Swal.fire(
                            'Terhapus!',
                            response.message,
                            'success'
                        );
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Gagal!',
                            xhr.responseJSON.message,
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endpush