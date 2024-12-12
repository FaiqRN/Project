@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Kegiatan untuk Unggah Dokumen Akhir</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="kegiatan-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Nama Kegiatan</th>
                                    <th width="15%">Status</th>
                                    <th width="15%">Dokumen Akhir</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload -->
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Unggah Dokumen Akhir</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="kegiatan_id" id="kegiatan_id">
                    <input type="hidden" name="kegiatan_type" id="kegiatan_type">
                    <div class="form-group">
                        <label for="dokumen_akhir">Pilih File (PDF, Max: 10MB)</label>
                        <input type="file" class="form-control" id="dokumen_akhir" name="dokumen_akhir" accept=".pdf" required>
                        <small class="form-text text-muted">File harus dalam format PDF dengan ukuran maksimal 10MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Unggah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('css')
<link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.css') }}">
<style>
    .table td, .table th {
        vertical-align: middle;
        text-align: center;
    }
    .table th:nth-child(2) {
        text-align: left;
    }
    .table td:nth-child(2) {
        text-align: left;
    }
</style>
@endpush

@push('js')
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
<script>
$(document).ready(function() {
    var table = $('#kegiatan-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('pic.unggah-dokumen.list') }}",
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'nama', name: 'nama'},
            {data: 'status', name: 'status'},
            {data: 'dokumen', name: 'dokumen'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        language: {
            processing: "Memuat data...",
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data per halaman",
            info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            infoEmpty: "Tidak ada data yang ditampilkan",
            infoFiltered: "(difilter dari _MAX_ total data)",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        }
    });

    // Handle upload button click
    $('#kegiatan-table').on('click', '.upload-btn', function() {
        $('#kegiatan_id').val($(this).data('id'));
        $('#kegiatan_type').val($(this).data('type'));
        $('#uploadModal').modal('show');
    });

    // Handle download button click
    $('#kegiatan-table').on('click', '.download-btn', function() {
        var id = $(this).data('id');
        var type = $(this).data('type');
        window.location.href = "{{ url('dosen/dokumen/download') }}/" + id + "/" + type;
    });

    // Handle form submission
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: "{{ route('pic.unggah-dokumen.store') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#uploadModal').modal('hide');
                Swal.fire({
                    title: 'Berhasil!',
                    text: response.message,
                    icon: 'success'
                });
                table.ajax.reload();
            },
            error: function(xhr) {
                Swal.fire({
                    title: 'Error!',
                    text: xhr.responseJSON.message,
                    icon: 'error'
                });
            }
        });
    });

    // Reset form when modal is closed
    $('#uploadModal').on('hidden.bs.modal', function() {
        $('#uploadForm')[0].reset();
    });
});
</script>
@endpush