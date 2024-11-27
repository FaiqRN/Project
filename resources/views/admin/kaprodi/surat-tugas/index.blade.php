@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Surat Tugas</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus"></i> Tambah Surat Tugas
                </button>
            </div>
        </div>
        <div class="card-body">
            <table id="tableSuratTugas" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th width="5%" class="text-center no-sort">No</th>
                        <th>Nomor Surat</th>
                        <th class="no-sort">Judul Surat</th>
                        <th>Tanggal</th>
                        <th width="20%" class="text-center no-sort">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suratTugas as $index => $surat)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $surat->nomer_surat }}</td>
                        <td>{{ $surat->judul_surat }}</td>
                        <td>{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <button class="btn btn-info btn-sm" onclick="showDetail({{ $surat->surat_id }})">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="editSurat({{ $surat->surat_id }})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteSurat({{ $surat->surat_id }})">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Surat Tugas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formTambah" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nomor Surat</label>
                        <input type="text" class="form-control" name="nomer_surat" required>
                    </div>
                    <div class="form-group">
                        <label>Judul Surat</label>
                        <input type="text" class="form-control" name="judul_surat" required>
                    </div>
                    <div class="form-group">
                        <label>File Surat (PDF)</label>
                        <input type="file" class="form-control" name="file_surat" accept=".pdf" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Surat</label>
                        <input type="date" class="form-control" name="tanggal_surat" required>
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

<!-- Modal Detail -->
<div class="modal fade" id="modalDetail" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Surat Tugas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Nomor Surat</th>
                            <td><span id="detail_nomer_surat"></span></td>
                        </tr>
                        <tr>
                            <th>Judul</th>
                            <td><span id="detail_judul_surat"></span></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td><span id="detail_tanggal_surat"></span></td>
                        </tr>
                        <tr>
                            <th>Dokumen</th>
                            <td>
                                <a href="#" id="detail_file_surat" class="btn btn-primary btn-sm" target="_blank">
                                    <i class="fas fa-download"></i> Download Dokumen
                                </a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Surat Tugas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEdit" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="surat_id" id="edit-id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nomor Surat</label>
                        <input type="text" class="form-control" name="nomer_surat" id="edit-nomer" required>
                    </div>
                    <div class="form-group">
                        <label>Judul Surat</label>
                        <input type="text" class="form-control" name="judul_surat" id="edit-judul" required>
                    </div>
                    <div class="form-group">
                        <label>File Surat (PDF)</label>
                        <input type="file" class="form-control" name="file_surat" accept=".pdf">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengubah file</small>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Surat</label>
                        <input type="date" class="form-control" name="tanggal_surat" id="edit-tanggal" required>
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

@push('js')
<script>
$(document).ready(function() {
    $('#tableSuratTugas').DataTable({
        "order": [], 
        "columnDefs": [
            { "orderable": false, "targets": [0, 4] } 
        ],
        "pageLength": 10,
        "language": {
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
            "infoEmpty": "Tidak ada data yang tersedia",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "search": "Search:",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": ">",
                "previous": "<"
            }
        },
        "searching": true,
        "search": {
            "smart": true,
            "regex": false,
            "caseInsensitive": true
        },
      
        "columns": [
            { "searchable": false }, 
            { "searchable": true },  
            { "searchable": true },  
            { "searchable": false }, 
            { "searchable": false }  
        ],

        "initComplete": function () {
            var api = this.api();
            
            $('.dataTables_filter input')
                .off()
                .on('input', function() {
                    api.search(this.value).draw();
                });
                
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var searchTerm = $('.dataTables_filter input').val().toLowerCase();
                
                if (searchTerm === '') {
                    return true;
                }

                var nomorSurat = data[1].toLowerCase();
                var judulSurat = data[2].toLowerCase();
                
                return nomorSurat.includes(searchTerm) || judulSurat.includes(searchTerm);
            });
        }
    });
});

function refreshTable() {
    window.location.reload();
}

    // Handle form tambah 
    $('#formTambah').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    
    $.ajax({
        url: "{{ route('admin.kaprodi.surat-tugas.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if(response.status == 200) {
                $('#modalTambah').modal('hide');
                Swal.fire('Sukses!', response.message, 'success')
                    .then(() => refreshTable());
            }
        },
        error: function(response) {
            let errors = response.responseJSON.errors;
            let errorMessage = '';
            $.each(errors, function(key, value) {
                errorMessage += value[0] + '\n';
            });
            Swal.fire('Error!', errorMessage, 'error');
        }
    });
});

function showDetail(id) {
    $.ajax({
        url: `{{ url('admin/kaprodi/surat-tugas') }}/${id}`,
        type: 'GET',
        success: function(response) {
            if(response.status == 200) {
                // Mengisi data ke modal detail dengan selector yang benar
                $('#detail_nomer_surat').text(response.data.nomer_surat);
                $('#detail_judul_surat').text(response.data.judul_surat);
                
                // Format tanggal ke format Indonesia
                const tanggal = new Date(response.data.tanggal_surat);
                const options = { day: 'numeric', month: 'long', year: 'numeric' };
                $('#detail_tanggal_surat').text(tanggal.toLocaleDateString('id-ID', options));
                
                // Set URL file surat
                $('#detail_file_surat').attr('href', response.file_url);
                
                // Tampilkan modal
                $('#modalDetail').modal('show');
            } else {
                Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error!', 'Terjadi kesalahan saat mengambil data', 'error');
        }
    });
}

// Tambahkan handler untuk tombol download
$(document).on('click', '#detail_file_surat', function(e) {
    const fileUrl = $(this).attr('href');
    if (!fileUrl || fileUrl === '#') {
        e.preventDefault();
        Swal.fire('Error!', 'File tidak tersedia', 'error');
    }
});


function editSurat(id) {
    $.ajax({
        url: `{{ url('admin/kaprodi/surat-tugas') }}/${id}`,
        type: 'GET',
        success: function(response) {
            if(response.status == 200) {
                $('#edit-id').val(response.data.surat_id);
                $('#edit-nomer').val(response.data.nomer_surat);
                $('#edit-judul').val(response.data.judul_surat);
                $('#edit-tanggal').val(response.data.tanggal_surat);
                $('#modalEdit').modal('show');
            } else {
                Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error!', 'Terjadi kesalahan saat mengambil data', 'error');
        }
    });
}

function deleteSurat(id) {
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
                url: "{{ url('admin/kaprodi/surat-tugas') }}/" + id,
                type: "DELETE",
                success: function(response) {
                    if(response.status == 200) {
                        Swal.fire('Sukses!', response.message, 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                    }
                },
                error: function(response) {
                    Swal.fire('Error!', 'Terjadi kesalahan', 'error');
                }
            });
        }
    });
}

// Handle form edit 
$('#formEdit').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    let id = $('#edit-id').val();
    
    formData.append('_method', 'PUT'); // Menambahkan method PUT
    
    $.ajax({
        url: `{{ url('admin/kaprodi/surat-tugas') }}/${id}`,
        type: 'POST',
        data: formData,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        contentType: false,
        processData: false,
        success: function(response) {
            if(response.status == 200) {
                $('#modalEdit').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire('Error!', response.message || 'Terjadi kesalahan', 'error');
            }
        },
        error: function(xhr) {
            if(xhr.status === 422) { // Validation error
                let errors = xhr.responseJSON.errors;
                let errorMessage = '';
                $.each(errors, function(key, value) {
                    errorMessage += value[0] + '\n';
                });
                Swal.fire('Error!', errorMessage, 'error');
            } else {
                Swal.fire('Error!', 'Terjadi kesalahan pada server', 'error');
            }
        }
    });
});
</script>
@endpush
@endsection