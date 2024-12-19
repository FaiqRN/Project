@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Kegiatan Non-JTI</h3>
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
                    <div class="btn-group">
                        <button class="btn btn-primary" id="btnFilter">
                            <i class="fas fa-filter mr-1"></i> Filter
                        </button>
                        <button class="btn btn-secondary" id="btnResetFilter">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabel Kegiatan -->
            <div class="table-responsive">
                <table id="tabelKegiatan" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Dosen</th>
                            <th>Nama Kegiatan</th>
                            <th>Penyelenggara</th>
                            <th>Lokasi</th>
                            <th>Tanggal</th>
                            <th width="12%">Status</th>
                            <th>Keterangan</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Persetujuan -->
<div class="modal fade" id="modalPersetujuan" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Persetujuan Kegiatan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPersetujuan">
                <div class="modal-body">
                    <input type="hidden" id="kegiatanId">
                    <input type="hidden" id="jenisKegiatan">
                    
                    <div class="form-group">
                        <label>Status Persetujuan <span class="text-danger">*</span></label>
                        <select class="form-control" id="statusPersetujuan" name="status" required>
                            <option value="">Pilih Status</option>
                            <option value="disetujui">Disetujui</option>
                            <option value="ditolak">Ditolak</option>
                        </select>
                        <small class="text-muted">Pilih status persetujuan untuk kegiatan ini</small>
                    </div>
                    
                    <div class="form-group" id="keteranganGroup" style="display:none;">
                        <label>Keterangan Penolakan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                            placeholder="Masukkan alasan penolakan..."
                            maxlength="255"></textarea>
                        <small class="text-danger">* Wajib diisi jika status ditolak</small>
                        <div class="mt-1">
                            <small class="text-muted char-count">0/255 karakter</small>
                        </div>
                    </div>

                    <div class="alert alert-warning" id="peringatanPenolakan" style="display:none;">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <strong>Perhatian!</strong> Jika status ditolak, file surat akan dihapus secara otomatis.
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
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Kegiatan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th width="30%">Jenis Kegiatan</th>
                                <td id="detailJenisKegiatan"></td>
                            </tr>
                            <tr>
                                <th>Nama Kegiatan</th>
                                <td id="detailKegiatan"></td>
                            </tr>
                            <tr>
                                <th>Penyelenggara</th>
                                <td id="detailPenyelenggara"></td>
                            </tr>
                            <tr>
                                <th>Lokasi Kegiatan</th>
                                <td id="detailLokasi"></td>
                            </tr>
                            <tr>
                                <th>Deskripsi Kegiatan</th>
                                <td id="detailDeskripsi"></td>
                            </tr>
                            <tr>
                                <th>Tanggal Mulai</th>
                                <td id="detailTanggalMulai"></td>
                            </tr>
                            <tr>
                                <th>Tanggal Selesai</th>
                                <td id="detailTanggalSelesai"></td>
                            </tr>
                            <tr>
                                <th>Judul Surat</th>
                                <td id="detailJudulSurat"></td>
                            </tr>
                            <tr>
                                <th>Nomor Surat</th>
                                <td id="detailNomorSurat"></td>
                            </tr>
                            <tr>
                                <th>Tanggal Surat</th>
                                <td id="detailTanggalSurat"></td>
                            </tr>
                            <tr>
                                <th>File Surat</th>
                                <td id="detailFileSurat"></td>
                            </tr>
                        </tbody>
                    </table>
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
<link rel="stylesheet" href="{{ asset('adminlte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.css') }}">
<style>
    .table td, .table th {
        vertical-align: middle;
    }
    .btn-group .btn {
        margin: 0 2px;
    }
    .action-buttons {
        white-space: nowrap;
    }
    .badge-status {
        min-width: 100px;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Inisialisasi DataTable
    let table = $('#tabelKegiatan').DataTable({
        processing: true,
        serverSide: false,
        dom: '<"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        ajax: {
            url: '{{ route("admin.dosen.kegiatan-non-jti.list") }}',
            type: 'GET',
            data: function(d) {
                d.status = $('#filterStatus').val();
                d.tanggal = $('#filterTanggal').val();
                d.search = $('#searchKegiatan').val();
            }
        },
        columns: [
            { 
                data: null,
                render: function (data, type, row, meta) {
                    return meta.row + 1;
                }
            },
            { 
                data: 'user_name',
                render: function(data) {
                    return data || '-';
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
                        case 'pending': badgeClass = 'warning'; break;
                        case 'disetujui': badgeClass = 'success'; break;
                        case 'ditolak': badgeClass = 'danger'; break;
                        default: badgeClass = 'secondary';
                    }
                    return `<span class="badge badge-${badgeClass} badge-status">${data}</span>`;
                }
            },
            { 
                data: 'keterangan',
                render: function(data) {
                    return data || '-';
                }
            },
            {
                data: null,
                className: 'action-buttons',
                orderable: false,
                render: function(data) {
                    let buttons = `<div class="btn-group">`;
                    
                    // Tombol Detail
                    buttons += `
                        <button class="btn btn-sm btn-info" onclick="showDetail('${data.id}', '${data.jenis_kegiatan}')" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>`;

                    // Tombol Persetujuan (hanya untuk status pending)
                    if (data.status_persetujuan.toLowerCase() === 'pending') {
                        buttons += `
                            <button class="btn btn-sm btn-success" onclick="showPersetujuan('${data.id}', '${data.jenis_kegiatan}')" title="Persetujuan">
                                <i class="fas fa-check"></i>
                            </button>`;
                    }

                    // Tombol Download (jika status disetujui)
                    if (data.status_persetujuan.toLowerCase() === 'disetujui') {
                        buttons += `
                            <button class="btn btn-sm btn-primary" onclick="downloadSurat('${data.id}', '${data.jenis_kegiatan}')" title="Unduh Surat">
                                <i class="fas fa-download"></i>
                            </button>`;
                    }

                    // Tombol Hapus
                    buttons += `
                        <button class="btn btn-sm btn-danger" onclick="confirmDelete('${data.id}', '${data.jenis_kegiatan}')" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>`;

                    buttons += `</div>`;
                    return buttons;
                }
            }
        ],
        order: [[0, 'asc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json'
        }
    });

    // Handler untuk filter
    $('#btnFilter').on('click', function() {
        table.ajax.reload();
    });

    // Handler untuk reset filter
    $('#btnResetFilter').on('click', function() {
        $('#filterStatus').val('');
        $('#filterTanggal').val('');
        $('#searchKegiatan').val('');
        table.ajax.reload();
    });

    // Handler untuk search
    $('#searchKegiatan').on('keyup', function(e) {
        if(e.key === 'Enter') {
            table.ajax.reload();
        }
    });

    // Event handler untuk perubahan status persetujuan
    $('#statusPersetujuan').on('change', function() {
        const status = $(this).val();
        if(status === 'ditolak') {
            $('#keteranganGroup').slideDown();
            $('#peringatanPenolakan').slideDown();
            $('#keterangan').prop('required', true);
        } else {
            $('#keteranganGroup').slideUp();
            $('#peringatanPenolakan').slideUp();
            $('#keterangan').prop('required', false);
        }
    });

    // Handler untuk menghitung karakter keterangan
    $('#keterangan').on('input', function() {
        const maxLength = 255;
        const currentLength = $(this).val().length;
        $('.char-count').text(`${currentLength}/${maxLength} karakter`);
    });

    // Handler submit form persetujuan
    $('#formPersetujuan').on('submit', function(e) {
        e.preventDefault();
        
        const kegiatanId = $('#kegiatanId').val();
        const jenisKegiatan = $('#jenisKegiatan').val();
        const status = $('#statusPersetujuan').val();
        const keterangan = $('#keterangan').val();

        // Validasi
        if (!status) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Status persetujuan harus dipilih!'
            });
            return false;
        }

        if (status === 'ditolak' && !keterangan.trim()) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Keterangan penolakan harus diisi!'
            });
            return false;
        }

        // Konfirmasi sebelum submit
        let confirmTitle = status === 'disetujui' ? 'Konfirmasi Persetujuan' : 'Konfirmasi Penolakan';
        let confirmText = status === 'disetujui' 
            ? 'Apakah Anda yakin akan menyetujui kegiatan ini?' 
            : 'Apakah Anda yakin akan menolak kegiatan ini? File surat akan dihapus secara otomatis.';

        Swal.fire({
            title: confirmTitle,
            text: confirmText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, lanjutkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitPersetujuan(kegiatanId, jenisKegiatan, status, keterangan);
            }
        });
    });
});

// Function untuk submit persetujuan
function submitPersetujuan(kegiatanId, jenisKegiatan, status, keterangan) {
    Swal.fire({
        title: 'Memproses...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: `{{ url('admin/dosen/kegiatan-non-jti') }}/${kegiatanId}/status`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            jenis_kegiatan: jenisKegiatan,
            status: status,
            keterangan: keterangan
        },
        success: function(response) {
            $('#modalPersetujuan').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: response.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                $('#tabelKegiatan').DataTable().ajax.reload();
            });
        },
        error: function(xhr) {
            let errorMessage = 'Terjadi kesalahan saat memproses persetujuan';
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

// Function untuk menampilkan modal persetujuan
function showPersetujuan(id, jenisKegiatan) {
    // Reset form
    $('#formPersetujuan')[0].reset();
    $('#keteranganGroup').hide();
    $('#peringatanPenolakan').hide();
    $('.char-count').text('0/255 karakter');
    
    // Set nilai
    $('#kegiatanId').val(id);
    $('#jenisKegiatan').val(jenisKegiatan);
    
    // Tampilkan modal
    $('#modalPersetujuan').modal('show');
}

// Function untuk menampilkan detail
function showDetail(id, jenisKegiatan) {
    Swal.fire({
        title: 'Memuat...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: `{{ url('admin/dosen/kegiatan-non-jti') }}/${id}/detail`,
        type: 'GET',
        data: { jenis_kegiatan: jenisKegiatan },
        success: function(response) {
            if (response.success) {
                const data = response.data;
                
                // Isi data ke dalam modal detail
                $('#detailJenisKegiatan').text(data.jenis_kegiatan);
                $('#detailKegiatan').text(data.nama);
                $('#detailPenyelenggara').text(data.penyelenggara);
                $('#detailLokasi').text(data.lokasi);
                $('#detailDeskripsi').text(data.deskripsi);
                $('#detailTanggalMulai').text(moment(data.tanggal_mulai).format('DD MMMM YYYY'));
                $('#detailTanggalSelesai').text(moment(data.tanggal_selesai).format('DD MMMM YYYY'));

                if (data.surat) {
                    $('#detailJudulSurat').text(data.surat.judul);
                    $('#detailNomorSurat').text(data.surat.nomor);
                    $('#detailTanggalSurat').text(moment(data.surat.tanggal).format('DD MMMM YYYY'));
                    $('#detailFileSurat').html(`
                        <button class="btn btn-sm btn-primary" onclick="downloadSurat('${id}', '${jenisKegiatan}')">
                            <i class="fas fa-download mr-1"></i> Download Surat
                        </button>
                    `);
                } else {
                    $('#detailJudulSurat').text('-');
                    $('#detailNomorSurat').text('-');
                    $('#detailTanggalSurat').text('-');
                    $('#detailFileSurat').text('Tidak ada file surat');
                }
                
                Swal.close();
                $('#modalDetail').modal('show');
            } else {
                throw new Error(response.message || 'Terjadi kesalahan');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Gagal memuat detail kegiatan';
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

// Function untuk download surat
function downloadSurat(id, jenisKegiatan) {
    Swal.fire({
        title: 'Mengunduh...',
        text: 'Mohon tunggu',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    window.location.href = `{{ url('admin/dosen/kegiatan-non-jti') }}/${id}/download-surat/${jenisKegiatan}`;
    
    setTimeout(() => {
        Swal.close();
    }, 1000);
}

// Function untuk konfirmasi hapus
function confirmDelete(id, jenisKegiatan) {
    Swal.fire({
        title: 'Konfirmasi Hapus',
        text: 'Apakah Anda yakin ingin menghapus kegiatan ini? File surat juga akan dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            deleteKegiatan(id, jenisKegiatan);
        }
    });
}

// Function untuk proses hapus
function deleteKegiatan(id, jenisKegiatan) {
    $.ajax({
        url: `{{ url('admin/dosen/kegiatan-non-jti') }}/${id}`,
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            _method: 'DELETE',
            jenis_kegiatan: jenisKegiatan
        },
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    $('#tabelKegiatan').DataTable().ajax.reload();
                });
            } else {
                throw new Error(response.message || 'Terjadi kesalahan');
            }
        },
        error: function(xhr) {
            let errorMessage = 'Terjadi kesalahan saat menghapus kegiatan';
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
</script>
@endpush