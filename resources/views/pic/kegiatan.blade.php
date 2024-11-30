@extends('layouts.template')

@section('content')
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Kegiatan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-blue-600">Home</a></li>
                        <li class="breadcrumb-item text-gray-600">Agenda</li>
                        <li class="breadcrumb-item active text-gray-800">Kegiatan</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Kegiatan Jurusan Card -->
            @if($kegiatanJurusan)
            <div class="card bg-white shadow-sm">
                <div class="card-header bg-gray-100">
                    <h3 class="card-title font-weight-bold">Kegiatan Jurusan</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-gray-700 font-weight-bold">Nama Kegiatan:</label>
                                <p class="form-control bg-gray-50">{{ $kegiatanJurusan->nama_kegiatan_jurusan }}</p>
                            </div>
                            <div class="form-group">
                                <label class="text-gray-700 font-weight-bold">Tanggal Mulai:</label>
                                <p class="form-control bg-gray-50">{{ date('d-m-Y', strtotime($kegiatanJurusan->tanggal_mulai)) }}</p>
                            </div>
                            <div class="form-group">
                                <label class="text-gray-700 font-weight-bold">Tanggal Selesai:</label>
                                <p class="form-control bg-gray-50">{{ date('d-m-Y', strtotime($kegiatanJurusan->tanggal_selesai)) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-gray-700 font-weight-bold">Penyelenggara:</label>
                                <p class="form-control bg-gray-50">{{ $kegiatanJurusan->penyelenggara }}</p>
                            </div>
                            <div class="form-group">
                                <label class="text-gray-700 font-weight-bold">Lokasi:</label>
                                <p class="form-control bg-gray-50">{{ $kegiatanJurusan->lokasi_kegiatan }}</p>
                            </div>
                            <div class="form-group">
                                <label class="text-gray-700 font-weight-bold">Deskripsi:</label>
                                <p class="form-control bg-gray-50" style="min-height: 100px;">{{ $kegiatanJurusan->deskripsi_kegiatan }}</p>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="button" class="btn btn-primary btn-lg btn-block" onclick="downloadSuratTugas('jurusan', {{ $kegiatanJurusan->kegiatan_jurusan_id }})">
                                <i class="fas fa-download mr-2"></i>Download Surat Tugas
                            </button>
                        </div>
                    </div>

                    <!-- Agenda Section -->
                    <div class="mt-5">
                        <h4 class="font-weight-bold mb-3">Tambah Agenda</h4>
                        <form id="formAgendaJurusan" class="bg-gray-100 p-4 rounded shadow-sm">
                            <input type="hidden" name="kegiatan_type" value="jurusan">
                            <input type="hidden" name="kegiatan_id" value="{{ $kegiatanJurusan->kegiatan_jurusan_id }}">
                            <div class="form-group">
                                <input type="text" name="nama_agenda" class="form-control form-control-lg mb-3" placeholder="Judul Agenda" required>
                                <textarea name="deskripsi" class="form-control mb-3" rows="4" placeholder="Deskripsi" required></textarea>
                                <input type="date" name="tanggal_agenda" class="form-control form-control-lg mb-3" required>
                                <div class="custom-file mb-3">
                                    <input type="file" class="custom-file-input" name="file_surat_agenda" id="file_agenda_jurusan">
                                    <label class="custom-file-label" for="file_agenda_jurusan">Pilih file</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-plus mr-2"></i>Tambah Agenda
                            </button>
                        </form>
                    </div>

                    <!-- Tabel Agenda -->
                    <div class="mt-5">
                        <h4 class="font-weight-bold mb-3">Daftar Agenda</h4>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover bg-white" id="tableAgendaJurusan">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="align-middle">No</th>
                                        <th class="align-middle" style="width: 25%">Nama Agenda</th>
                                        <th class="align-middle" style="width: 15%">Tanggal</th>
                                        <th class="align-middle">Deskripsi</th>
                                        <th class="align-middle" style="width: 15%">Dokumen</th>
                                        <th class="align-middle" style="width: 15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data akan diisi melalui AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Kegiatan Prodi Card - Similar styling as above -->
            @if($kegiatanProdi)
            <div class="card bg-white shadow-sm mt-4">
                <!-- Similar structure with same styling as Kegiatan Jurusan -->
            </div>
            @endif

            @if(!$kegiatanJurusan && !$kegiatanProdi)
            <div class="card bg-white shadow-sm">
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5 class="alert-heading font-weight-bold"><i class="fas fa-info-circle mr-2"></i>Informasi</h5>
                        <p class="mb-0">Anda tidak memiliki kegiatan yang sedang berlangsung saat ini.</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('css')
<!-- DataTables -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css') }}">
<!-- SweetAlert2 -->
<link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css') }}">
<!-- Custom CSS -->
<style>
    .form-control {
        font-size: 1rem;
    }
    .form-control-lg {
        font-size: 1.1rem;
    }
    .card {
        border-radius: 0.5rem;
    }
    .card-body {
        padding: 2rem;
    }
    .table th, .table td {
        padding: 1rem;
        vertical-align: middle;
    }
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1.1rem;
    }
    .bg-gray-50 {
        background-color: #F9FAFB;
    }
    .bg-gray-100 {
        background-color: #F3F4F6;
    }
    .text-gray-600 {
        color: #4B5563;
    }
    .text-gray-700 {
        color: #374151;
    }
    .text-gray-800 {
        color: #1F2937;
    }
    .shadow-sm {
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@push('scripts')
<!-- Your existing scripts remain unchanged -->
@endpush