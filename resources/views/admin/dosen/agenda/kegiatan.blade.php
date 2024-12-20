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
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#kegiatanInstitusi">Kegiatan Institusi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#kegiatanLuarInstitusi">Kegiatan Luar Institusi</a>
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
                <div id="kegiatanInstitusi" class="tab-pane fade">
                    <div class="table-responsive">
                        <table id="tableKegiatanInstitusi" class="table table-bordered table-striped w-100">
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
                <div id="kegiatanLuarInstitusi" class="tab-pane fade">
                    <div class="table-responsive">
                        <table id="tableKegiatanLuarInstitusi" class="table table-bordered table-striped w-100">
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

<!-- Modal Institusi -->
<div class="modal fade" id="modalKegiatanInstitusi" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Form Kegiatan Institusi</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formKegiatanInstitusi" novalidate>
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="kegiatan_institusi_id" id="kegiatan_institusi_id">
                    <input type="hidden" name="surat_id" id="hidden_surat_id_institusi">
                    <input type="hidden" name="user_id" id="hidden_user_id_institusi">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Surat Tugas</label>
                                <select class="form-control select2" name="surat_id" id="surat_id_institusi" disabled>
                                    <option value="">Pilih Surat Tugas</option>
                                    @foreach($surat as $s)
                                        <option value="{{ $s->surat_id }}">{{ $s->nomer_surat }} - {{ $s->judul_surat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Penanggung Jawab</label>
                                <select class="form-control select2" name="user_id" id="user_id_institusi" disabled>
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
                        <input type="text" class="form-control" name="nama_kegiatan_institusi" maxlength="200" required>
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

<!-- Modal Luar Institusi -->
<div class="modal fade" id="modalKegiatanLuarInstitusi" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Form Kegiatan Luar Institusi</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formKegiatanLuarInstitusi" novalidate>
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="kegiatan_luar_institusi_id" id="kegiatan_luar_institusi_id">
                    <input type="hidden" name="surat_id" id="hidden_surat_id_luar_institusi">
                    <input type="hidden" name="user_id" id="hidden_user_id_luar_institusi">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Surat Tugas</label>
                                <select class="form-control select2" name="surat_id" id="surat_id_luar_institusi" disabled>
                                    <option value="">Pilih Surat Tugas</option>
                                    @foreach($surat as $s)
                                        <option value="{{ $s->surat_id }}">{{ $s->nomer_surat }} - {{ $s->judul_surat }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Penanggung Jawab</label>
                                <select class="form-control select2" name="user_id" id="user_id_luar_institusi" disabled>
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
                        <input type="text" class="form-control" name="nama_kegiatan_luar_institusi" maxlength="200" required>
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

    $('#surat_id_institusi, #user_id_institusi').select2({
        theme: 'bootstrap4',
        width: '100%',
        dropdownParent: $('#modalKegiatanInstitusi')
    });

    $('#surat_id_luar_institusi, #user_id_luar_institusi').select2({
        theme: 'bootstrap4',
        width: '100%',
        dropdownParent: $('#modalKegiatanLuarInstitusi')
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
            { data: 'action', orderable: false, searchable: false }
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
            { data: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });

    let tableKegiatanInstitusi = $('#tableKegiatanInstitusi').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.dosen.agenda.institusi.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_kegiatan_institusi', name: 'nama_kegiatan_institusi' },
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
            { data: 'action', orderable: false, searchable: false }
        ],
        order: [[1, 'asc']]
    });

    let tableKegiatanLuarInstitusi = $('#tableKegiatanLuarInstitusi').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('admin.dosen.agenda.luar-institusi.data') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_kegiatan_luar_institusi', name: 'nama_kegiatan_luar_institusi' },
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
            { data: 'action', orderable: false, searchable: false }
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

    // Detail Button Handler
    $(document).on('click', '.detail-btn', function() {
        let id = $(this).data('id');
        let tableId = $(this).closest('table').attr('id');
        let type;
        
        switch(tableId) {
            case 'tableKegiatanJurusan':
                type = 'jurusan';
                break;
            case 'tableKegiatanProdi':
                type = 'prodi';
                break;
            case 'tableKegiatanInstitusi':
                type = 'institusi';
                break;
            case 'tableKegiatanLuarInstitusi':
                type = 'luar-institusi';
                break;
        }
        
        $.ajax({
            url: `/admin/dosen/agenda/${type}/${id}`,
            type: 'GET',
            success: function(response) {
                let data = response.data;
                let namaKegiatan;
                
                switch(type) {
                    case 'jurusan':
                        namaKegiatan = data.nama_kegiatan_jurusan;
                        break;
                    case 'prodi':
                        namaKegiatan = data.nama_kegiatan_program_studi;
                        break;
                    case 'institusi':
                        namaKegiatan = data.nama_kegiatan_institusi;
                        break;
                    case 'luar-institusi':
                        namaKegiatan = data.nama_kegiatan_luar_institusi;
                        break;
                }
                
                $('#detail_nama_kegiatan').text(namaKegiatan);
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
            },
            error: function(xhr) {
                Swal.fire('Gagal!', 'Gagal mengambil detail kegiatan', 'error');
            }
        });
    });

    // Edit Button Handler
    $(document).on('click', '.edit-btn', function() {
        let id = $(this).data('id');
        let tableId = $(this).closest('table').attr('id');
        let type;
        
        switch(tableId) {
            case 'tableKegiatanJurusan':
                type = 'jurusan';
                break;
            case 'tableKegiatanProdi':
                type = 'prodi';
                break;
            case 'tableKegiatanInstitusi':
                type = 'institusi';
                break;
            case 'tableKegiatanLuarInstitusi':
                type = 'luar-institusi';
                break;
        }
        
        $.ajax({
            url: `/admin/dosen/agenda/${type}/${id}`,
            type: 'GET',
            success: function(response) {
                let data = response.data;
                
                switch(type) {
                    case 'jurusan':
                        setFormJurusan(data);
                        break;
                    case 'prodi':
                        setFormProdi(data);
                        break;
                    case 'institusi':
                        setFormInstitusi(data);
                        break;
                    case 'luar-institusi':
                        setFormLuarInstitusi(data);
                        break;
                }
            },
            error: function(xhr) {
                Swal.fire('Gagal!', 'Gagal mengambil data kegiatan', 'error');
            }
        });
    });

    function setFormJurusan(data) {
        $('#kegiatan_jurusan_id').val(data.kegiatan_jurusan_id);
        $('#surat_id_jurusan').val(data.surat_id).trigger('change');
        $('#user_id_jurusan').val(data.user_id).trigger('change');
        $('[name="nama_kegiatan_jurusan"]').val(data.nama_kegiatan_jurusan);
        $('#formKegiatanJurusan [name="deskripsi_kegiatan"]').val(data.deskripsi_kegiatan);
        $('#formKegiatanJurusan [name="lokasi_kegiatan"]').val(data.lokasi_kegiatan);
        $('#formKegiatanJurusan [name="tanggal_mulai"]').val(data.tanggal_mulai);
        $('#formKegiatanJurusan [name="tanggal_selesai"]').val(data.tanggal_selesai);
        $('#formKegiatanJurusan [name="penyelenggara"]').val(data.penyelenggara);
        $('#modalKegiatanJurusan').modal('show');
    }

    function setFormProdi(data) {
        $('#kegiatan_program_studi_id').val(data.kegiatan_program_studi_id);
        $('#surat_id_prodi').val(data.surat_id).trigger('change');
        $('#user_id_prodi').val(data.user_id).trigger('change');
        $('[name="nama_kegiatan_program_studi"]').val(data.nama_kegiatan_program_studi);
        $('#formKegiatanProdi [name="deskripsi_kegiatan"]').val(data.deskripsi_kegiatan);
        $('#formKegiatanProdi [name="lokasi_kegiatan"]').val(data.lokasi_kegiatan);
        $('#formKegiatanProdi [name="tanggal_mulai"]').val(data.tanggal_mulai);
        $('#formKegiatanProdi [name="tanggal_selesai"]').val(data.tanggal_selesai);
        $('#formKegiatanProdi [name="penyelenggara"]').val(data.penyelenggara);
        $('#modalKegiatanProdi').modal('show');
    }

    function setFormInstitusi(data) {
        $('#kegiatan_institusi_id').val(data.kegiatan_institusi_id);
        $('#surat_id_institusi').val(data.surat_id).trigger('change');
        $('#user_id_institusi').val(data.user_id).trigger('change');
        $('#formKegiatanInstitusi input[name="surat_id"]').val(data.surat_id);
        $('#formKegiatanInstitusi input[name="user_id"]').val(data.user_id);
        $('[name="nama_kegiatan_institusi"]').val(data.nama_kegiatan_institusi);
        $('#formKegiatanInstitusi [name="deskripsi_kegiatan"]').val(data.deskripsi_kegiatan);
        $('#formKegiatanInstitusi [name="lokasi_kegiatan"]').val(data.lokasi_kegiatan);
        $('#formKegiatanInstitusi [name="tanggal_mulai"]').val(data.tanggal_mulai);
        $('#formKegiatanInstitusi [name="tanggal_selesai"]').val(data.tanggal_selesai);
        $('#formKegiatanInstitusi [name="penyelenggara"]').val(data.penyelenggara);
        $('#modalKegiatanInstitusi').modal('show');
    }

    function setFormLuarInstitusi(data) {
       $('#kegiatan_luar_institusi_id').val(data.kegiatan_luar_institusi_id);
       $('#surat_id_luar_institusi').val(data.surat_id).trigger('change');
       $('#user_id_luar_institusi').val(data.user_id).trigger('change');
       $('#formKegiatanLuarInstitusi input[name="surat_id"]').val(data.surat_id);
       $('#formKegiatanLuarInstitusi input[name="user_id"]').val(data.user_id);
       $('[name="nama_kegiatan_luar_institusi"]').val(data.nama_kegiatan_luar_institusi);
       $('#formKegiatanLuarInstitusi [name="deskripsi_kegiatan"]').val(data.deskripsi_kegiatan);
       $('#formKegiatanLuarInstitusi [name="lokasi_kegiatan"]').val(data.lokasi_kegiatan);
       $('#formKegiatanLuarInstitusi [name="tanggal_mulai"]').val(data.tanggal_mulai);
       $('#formKegiatanLuarInstitusi [name="tanggal_selesai"]').val(data.tanggal_selesai);
       $('#formKegiatanLuarInstitusi [name="penyelenggara"]').val(data.penyelenggara);
       $('#modalKegiatanLuarInstitusi').modal('show');
   }

   // Form Submit Handler
   function handleFormSubmit(form, type) {
       let formData = new FormData(form);
       let id;
       let url;

       switch(type) {
           case 'jurusan':
               id = $('#kegiatan_jurusan_id').val();
               break;
           case 'prodi':
               id = $('#kegiatan_program_studi_id').val();
               break;
           case 'institusi':
               id = $('#kegiatan_institusi_id').val();
               break;
           case 'luar-institusi':
               id = $('#kegiatan_luar_institusi_id').val();
               break;
       }

       if (id) {
           url = `/admin/dosen/agenda/${type}/update/${id}`;
           formData.append('_method', 'PUT');
       } else {
           url = `/admin/dosen/agenda/${type}/store`;
       }

       $.ajax({
           url: url,
           method: 'POST',
           data: formData,
           processData: false,
           contentType: false,
           success: function(response) {
               let modalId = `#modalKegiatan${type.charAt(0).toUpperCase() + type.slice(1)}`;
               $(modalId).modal('hide');
               
               switch(type) {
                   case 'jurusan':
                       tableKegiatanJurusan.ajax.reload(null, false);
                       break;
                   case 'prodi':
                       tableKegiatanProdi.ajax.reload(null, false);
                       break;
                   case 'institusi':
                       tableKegiatanInstitusi.ajax.reload(null, false);
                       break;
                   case 'luar-institusi':
                       tableKegiatanLuarInstitusi.ajax.reload(null, false);
                       break;
               }
               
               Swal.fire('Berhasil', response.message, 'success');
           },
           error: function(xhr) {
               let errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan';
               Swal.fire('Gagal', errorMessage, 'error');
           }
       });
   }

   // Form Submit Events
   $('#formKegiatanJurusan').on('submit', function(e) {
       e.preventDefault();
       handleFormSubmit(this, 'jurusan');
   });

   $('#formKegiatanProdi').on('submit', function(e) {
       e.preventDefault();
       handleFormSubmit(this, 'prodi');
   });

   $('#formKegiatanInstitusi').on('submit', function(e) {
       e.preventDefault();
       handleFormSubmit(this, 'institusi');
   });

   $('#formKegiatanLuarInstitusi').on('submit', function(e) {
       e.preventDefault();
       handleFormSubmit(this, 'luar-institusi');
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
       $('#kegiatan_jurusan_id, #kegiatan_program_studi_id, #kegiatan_institusi_id, #kegiatan_luar_institusi_id').val('');
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

   // Delete Button Handler
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        let tableId = $(this).closest('table').attr('id');
        let type;

        switch(tableId) {
            case 'tableKegiatanJurusan':
                type = 'jurusan';
                break;
            case 'tableKegiatanProdi':
                type = 'prodi';
                break;
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
                $.ajax({
                    url: `/admin/dosen/agenda/${type}/delete/${id}`,
                    type: 'DELETE',
                    success: function(response) {
                        if (type === 'jurusan') {
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
});
</script>
@endpush