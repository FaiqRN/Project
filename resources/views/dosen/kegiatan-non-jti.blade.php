@extends('layouts.template')


@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Kegiatan Non-JTI</h3>
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambahKegiatan">
                    <i class="fas fa-plus mr-2"></i>Tambah Kegiatan
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Filter dan Pencarian -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select class="form-control" id="filterStatus">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="ditolak">Ditolak</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="filterTanggal">
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchKegiatan" placeholder="Cari kegiatan...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="button" id="btnSearch">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-secondary btn-block" id="btnFilter">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                </div>
            </div>


            <!-- Tabel Kegiatan -->
            <div class="table-responsive">
                <table id="tabelKegiatan" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Kegiatan</th>
                            <th>Penyelenggara</th>
                            <th>Lokasi</th>
                            <th>Tanggal</th>
                            <th width="12%">Status Persetujuan</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>


<!-- Modal Tambah Kegiatan -->
<div class="modal fade" id="modalTambahKegiatan" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Tambah Kegiatan Non-JTI</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formTambahKegiatan" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Kegiatan<span class="text-danger">*</span></label>
                                <select class="form-control" name="jenis_kegiatan" required>
                                    <option value="">Pilih Jenis Kegiatan</option>
                                    <option value="institusi">Kegiatan Institusi</option>
                                    <option value="luar_institusi">Kegiatan Luar Institusi</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status Persetujuan</label>
                                <input type="text" class="form-control" value="pending" readonly>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Nama Kegiatan<span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="nama_kegiatan" required>
                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Penyelenggara<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="penyelenggara" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lokasi Kegiatan<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="lokasi_kegiatan" required>
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <label>Deskripsi Kegiatan<span class="text-danger">*</span></label>
                        <textarea class="form-control" name="deskripsi_kegiatan" rows="3" required></textarea>
                    </div>


                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Mulai<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_mulai" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tanggal Selesai<span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="tanggal_selesai" required>
                            </div>
                        </div>
                    </div>


                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="mb-0">Informasi Surat</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Judul Surat<span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="judul_surat" required>
                            </div>
               
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Nomor Surat<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="nomer_surat" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Tanggal Surat<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="tanggal_surat" required>
                                    </div>
                                </div>
                            </div>
               
                            <div class="form-group">
                                <label>File Surat (PDF)<span class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="file_surat" id="file_surat" accept=".pdf" required>
                                    <label class="custom-file-label" for="file_surat">Pilih file...</label>
                                </div>
                                <small class="form-text text-muted">Format PDF, maksimal 2MB</small>
                            </div>
                        </div>
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
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog" aria-labelledby="modalDetailLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalDetailLabel">Detail Kegiatan</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Informasi Utama -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 font-weight-bold">Informasi Kegiatan</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="text-muted mb-1">Nama Kegiatan</label>
                                <p id="detailNamaKegiatan" class="font-weight-bold"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted mb-1">Tanggal Pelaksanaan</label>
                                <p id="detailTanggal" class="font-weight-bold"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted mb-1">Jenis Kegiatan</label>
                                <p id="detailJenisKegiatan" class="font-weight-bold"></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="text-muted mb-1">Penyelenggara</label>
                                <p id="detailPenyelenggara" class="font-weight-bold"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted mb-1">Status Persetujuan</label>
                                <p>
                                    <span id="detailStatusPersetujuan" class="badge badge-pill px-3 py-2"></span>
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label class="text-muted mb-1">Lokasi</label>
                                <p id="detailLokasi" class="font-weight-bold"></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <label class="text-muted mb-1">Deskripsi Kegiatan</label>
                                <p id="detailDeskripsi" class="font-weight-bold"></p>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Informasi Surat -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 font-weight-bold">Informasi Surat</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="text-muted mb-1">Judul Surat</label>
                                <p id="detailJudulSurat" class="font-weight-bold"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted mb-1">Nomor Surat</label>
                                <p id="detailNomorSurat" class="font-weight-bold"></p>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted mb-1">Tanggal Surat</label>
                                <p id="detailTanggalSurat" class="font-weight-bold"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('css')
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<style>
    .status-badge {
        padding: 0.5rem;
        border-radius: 4px;
        font-size: 0.875rem;
        font-weight: 600;
        text-align: center;
    }
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }
    .status-disetujui {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .status-ditolak {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .table td, .table th {
        vertical-align: middle;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    .custom-file-label {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .modal-body {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }


    .modal-header {
        border-radius: 0;
    }
   
    .card {
        border: none;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
   
    .card-header {
        border-bottom: 1px solid #eee;
    }
   
    .text-muted {
        font-size: 0.85rem;
    }
   
    .badge {
        font-size: 0.85rem;
        font-weight: 500;
    }
   
    .badge-warning {
        background-color: #ffeeba;
        color: #856404;
    }
   
    .badge-success {
        background-color: #d4edda;
        color: #155724;
    }
   
    .badge-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
   
    .badge-info {
        background-color: #d1ecf1;
        color: #0c5460;
    }
   
    .modal-lg {
        max-width: 800px;
    }
   
    .modal-body {
        padding: 1.5rem;
    }
   
    .font-weight-bold {
        color: #2d3748;
    }
</style>
@endpush


@push('js')
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    let table = $('#tabelKegiatan').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
        url: '{{ route("dosen.kegiatan-non-jti.list") }}',
        type: 'GET',
        dataSrc: function(response) {
            return response.data || [];
        }
    },
    columns: [
        {
            data: null,
            render: function (data, type, row, meta) {
                return meta.row + 1;
            }
        },
        { data: 'nama' },
        { data: 'penyelenggara' },
        { data: 'lokasi' },
        {
            data: null,
            render: function(data) {
                const mulai = moment(data.tanggal_mulai).format('DD/MM/YYYY');
                const selesai = moment(data.tanggal_selesai).format('DD/MM/YYYY');
                return `${mulai} - ${selesai}`;
            }
        },
        {
            data: 'status_persetujuan',
            render: function(data) {
                let badgeClass;
                switch(data.toLowerCase()) {
                    case 'pending':
                        badgeClass = 'warning';
                        break;
                    case 'disetujui':
                        badgeClass = 'success';
                        break;
                    case 'ditolak':
                        badgeClass = 'danger';
                        break;
                    default:
                        badgeClass = 'secondary';
                }
                return `<span class="badge badge-${badgeClass}">${data}</span>`;
            }
        },
        {
            data: null,
            render: function(data) {
                return `
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info" onclick="detailKegiatan(${data.id})" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>
                `;
            }
        }
    ],
    order: [[0, 'asc']],
    language: {
        url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
    }
});


    // File input handler
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });


    // Reset form saat modal ditutup
    $('#modalTambahKegiatan').on('hidden.bs.modal', function() {
        $('#formTambahKegiatan')[0].reset();
        $('.custom-file-label').html('Pilih file...');
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
    });


    // Form Submit Handler
    $('#formTambahKegiatan').on('submit', function(e) {
        e.preventDefault();
       
        // Remove previous error states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
       
        let formData = new FormData(this);
       
        // Show loading state
        Swal.fire({
            title: 'Menyimpan Data',
            text: 'Mohon tunggu...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });


        $.ajax({
            url: '{{ route("dosen.kegiatan-non-jti.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    $('#modalTambahKegiatan').modal('hide');
                    table.ajax.reload();
                });
            },
            error: function(xhr) {
                console.error('Error:', xhr);
               
                if (xhr.status === 422) {
                    // Validation errors
                    let errors = xhr.responseJSON.errors;
                    Object.keys(errors).forEach(function(key) {
                        let input = $(`[name="${key}"]`);
                        input.addClass('is-invalid');
                        input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                    });
                }
               
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data'
                });
            }
        });
    });


    // Filter Handler
    $('#btnFilter').on('click', function() {
        let status = $('#filterStatus').val();
        let tanggal = $('#filterTanggal').val();
        let search = $('#searchKegiatan').val();


        table.ajax.reload();
    });


    // Search Handler
    $('#searchKegiatan').on('keyup', function(e) {
        if(e.key === 'Enter') {
            table.search(this.value).draw();
        }
    });
});


// Function untuk handle detail kegiatan
function detailKegiatan(id) {
    currentKegiatanId = id; // Simpan ID untuk keperluan download
    $.ajax({
        url: `/dosen/kegiatan-non-jti/${id}/detail`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const data = response.data;
                $('#detailNamaKegiatan').text(data.nama_kegiatan);
                $('#detailJenisKegiatan').text(data.jenis_kegiatan);
                $('#detailPenyelenggara').text(data.penyelenggara);
                $('#detailLokasi').text(data.lokasi_kegiatan);
                $('#detailTanggal').text(`${formatDate(data.tanggal_mulai)} - ${formatDate(data.tanggal_selesai)}`);
                $('#detailDeskripsi').text(data.deskripsi_kegiatan);
               
                // Atur warna status persetujuan
                const statusPersetujuan = $('#detailStatusPersetujuan');
                statusPersetujuan.text(data.status_persetujuan);
                switch(data.status_persetujuan.toLowerCase()) {
                    case 'pending':
                        statusPersetujuan.removeClass().addClass('badge badge-warning');
                        break;
                    case 'disetujui':
                        statusPersetujuan.removeClass().addClass('badge badge-success');
                        break;
                    case 'ditolak':
                        statusPersetujuan.removeClass().addClass('badge badge-danger');
                        break;
                }
               
                // Informasi surat
                $('#detailJudulSurat').text(data.surat.judul_surat);
                $('#detailNomorSurat').text(data.surat.nomer_surat);
                $('#detailTanggalSurat').text(formatDate(data.surat.tanggal_surat));
               
                $('#modalDetail').modal('show');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', 'Terjadi kesalahan saat memuat detail kegiatan', 'error');
        }
    });
}

// Fungsi untuk memformat tanggal
function formatDate(dateString) {
    const options = { day: '2-digit', month: '2-digit', year: 'numeric' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
}

let currentKegiatanId = null; // Untuk menyimpan ID kegiatan yang sedang ditampilkan

</script>
@endpush