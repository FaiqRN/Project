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
                            <th width="10%">Status</th>
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
            <form id="formTambahKegiatan" enctype="multipart/form-data">
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
                                <input type="text" class="form-control" value="Menunggu" readonly>
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
    .small-box {
        border-radius: 0.5rem;
    }
    .table td, .table th {
        vertical-align: middle;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(document).ready(function() {
    // Inisialisasi DataTable
    let table = $('#tabelKegiatan').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '{{ route("dosen.kegiatan-non-jti.list") }}',
            dataSrc: 'data'
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
                    let mulai = moment(data.tanggal_mulai).format('DD/MM/YYYY');
                    let selesai = moment(data.tanggal_selesai).format('DD/MM/YYYY');
                    return `${mulai} - ${selesai}`;
                }
            },
            { 
                data: 'status_persetujuan',
                render: function(data) {
                    let className = `status-badge status-${data.toLowerCase()}`;
                    return `<span class="${className}">${data}</span>`;
                }
            },
            { data: 'status' },
            {
                data: null,
    render: function(data) {
        return `
            <button class="btn btn-sm btn-info" onclick="detailKegiatan(${data.id})">
                <i class="fas fa-eye"></i>
            </button>
            <button class="btn btn-sm btn-primary" onclick="downloadSurat(${data.id})">
                <i class="fas fa-download"></i>
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

    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName);
    });

    // Fungsi untuk update statistik
    function updateStatistik() {
        let total = 0;
        let menunggu = 0;
        let disetujui = 0;
        let ditolak = 0;

        table.rows().data().each(function(data) {
            total++;
            switch(data.status_persetujuan.toLowerCase()) {
                case 'pending':
                    menunggu++;
                    break;
                case 'disetujui':
                    disetujui++;
                    break;
                case 'ditolak':
                    ditolak++;
                    break;
            }
        });

        $('#totalKegiatan').text(total);
        $('#totalMenunggu').text(menunggu);
        $('#totalDisetujui').text(disetujui);
        $('#totalDitolak').text(ditolak);
    }

    $('#modalTambahKegiatan').on('hidden.bs.modal', function() {
        $('#formTambahKegiatan')[0].reset();
        $('.custom-file-label').html('Pilih file...');
    });

    // Form Submit Handler
    $('#formTambahKegiatan').on('submit', function(e) {
        e.preventDefault();
        
        // Validasi jenis kegiatan
        let jenisKegiatan = $('select[name="jenis_kegiatan"]').val();
        if (!jenisKegiatan) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Silahkan pilih jenis kegiatan'
            });
            return false;
        }

        let formData = new FormData(this);
        
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
                $('#modalTambahKegiatan').modal('hide');
                table.ajax.reload();
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: response.message
                });
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });

    // Filter Handler
    $('#btnFilter').click(function() {
        let status = $('#filterStatus').val();
        let tanggal = $('#filterTanggal').val();
        let search = $('#searchKegiatan').val();

        table.ajax.reload();
        updateStatistik();
    });

    // Initial statistik update
    table.on('draw', updateStatistik);
});

function detailKegiatan(id) {
    // Implementasi fungsi detail
    window.location.href = `/dosen/kegiatan-non-jti/${id}`;
}

function downloadSurat(id) {
    window.location.href = `/dosen/kegiatan-non-jti/${id}/download-surat`;
}
</script>
@endpush