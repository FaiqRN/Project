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
                        <th>No.</th>
                        <th>Nomor Surat</th>
                        <th>Judul</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($suratTugas as $index => $surat)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $surat->nomer_surat }}</td>
                        <td>{{ $surat->judul_surat }}</td>
                        <td>{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d/m/Y') }}</td>
                        <td>
                            <button class="btn btn-info btn-sm" onclick="showDetail({{ $surat->surat_id }})" data-toggle="tooltip" title="Detail">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                            <button class="btn btn-warning btn-sm" onclick="editSurat({{ $surat->surat_id }})" data-toggle="tooltip" title="Edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" onclick="deleteSurat({{ $surat->surat_id }})" data-toggle="tooltip" title="Hapus">
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
                <table class="table">
                    <tr>
                        <th>Nomor Surat</th>
                        <td id="detail-nomer"></td>
                    </tr>
                    <tr>
                        <th>Judul Surat</th>
                        <td id="detail-judul"></td>
                    </tr>
                    <tr>
                        <th>Tanggal Surat</th>
                        <td id="detail-tanggal"></td>
                    </tr>
                    <tr>
                        <th>File Surat</th>
                        <td>
                            <a id="detail-file" href="#" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-download"></i> Download
                            </a>
                        </td>
                    </tr>
                </table>
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
    $('#tableSuratTugas').DataTable();

    // Handle form tambah submission
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
                    Swal.fire('Sukses!', response.message, 'success')
                        .then(() => location.reload());
                } else {
                    Swal.fire('Error!', 'Terjadi kesalahan', 'error');
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
});

function showDetail(id) {
    $.get("{{ url('admin/kaprodi/surat-tugas') }}/" + id, function(response) {
        $('#detail-nomer').text(response.nomer_surat);
        $('#detail-judul').text(response.judul_surat);
        $('#detail-tanggal').text(moment(response.tanggal_surat).format('DD/MM/YYYY'));
        $('#detail-file').attr('href', "{{ asset('storage') }}/" + response.file_surat);
        $('#modalDetail').modal('show');
    });
}

function editSurat(id) {
    $.get("{{ url('admin/kaprodi/surat-tugas') }}/" + id, function(response) {
        $('#edit-id').val(response.surat_id);
        $('#edit-nomer').val(response.nomer_surat);
        $('#edit-judul').val(response.judul_surat);
        $('#edit-tanggal').val(response.tanggal_surat);
        $('#modalEdit').modal('show');
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

// Handle form edit submission
$('#formEdit').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    let id = $('#edit-id').val();
    
    $.ajax({
        url: "{{ url('admin/kaprodi/surat-tugas') }}/" + id,
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            if(response.status == 200) {
                Swal.fire('Sukses!', response.message, 'success')
                    .then(() => location.reload());
            } else {
                Swal.fire('Error!', 'Terjadi kesalahan', 'error');
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
</script>
@endpush
@endsection