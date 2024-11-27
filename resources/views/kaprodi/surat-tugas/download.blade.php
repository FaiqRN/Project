@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <!-- Search Box -->
    <div class="mb-4">
        <div class="input-group" style="max-width: 300px;">
            <input type="text" class="form-control" id="searchInput" placeholder="Cari Surat Tugas">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Daftar Surat -->
    <div class="dokumen-list">
        @forelse($suratTugas as $surat)
            <div class="card mb-3">
                <div class="card-body bg-light">
                    <h5 class="text-dark">{{ $surat->judul_surat }}</h5>
                    <div class="mb-2">
                        <i class="fas fa-file-alt text-muted"></i>
                        <small class="text-muted ml-2">{{ $surat->nomer_surat }}</small>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-calendar-alt text-muted"></i>
                        <small class="text-muted ml-2">{{ \Carbon\Carbon::parse($surat->tanggal_surat)->format('d/m/Y') }}</small>
                    </div>
                    <div class="d-flex">
                        <a href="{{ route('kaprodi.surat-tugas.download-file', $surat->surat_id) }}" 
                           class="btn btn-primary flex-grow-1 mr-2 text-center"
                           title="Download Surat Tugas">
                            <i class="fas fa-download mr-2"></i> Unduh Dokumen
                        </a>
                        <button type="button"
                            class="btn btn-light"
                            style="width: 100px;"
                            onclick="previewDokumen('{{ asset('storage/' . $surat->file_surat) }}')"
                            title="Lihat Surat">
                            <i class="fas fa-eye mr-1"></i> Lihat
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="alert alert-info">
                Belum ada surat tugas yang tersedia.
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Preview -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file-pdf mr-2"></i>Preview Dokumen</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <iframe id="pdfViewer" width="100%" height="500px" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
.card {
    border-radius: 8px;
    margin-bottom: 15px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
.card-body {
    background-color: #f8f9fa !important;
    padding: 20px;
}
.btn-primary {
    background-color: #007bff;
    border: none;
}
.btn-primary:hover {
    background-color: #0056b3;
}
.btn-light {
    background-color: #e9ecef;
    border: none;
}
.btn-light:hover {
    background-color: #dae0e5;
}
.btn {
    padding: 8px 15px;
    border-radius: 4px;
}
h5 {
    margin-bottom: 15px;
    font-weight: 600;
}
.fas {
    width: 16px;
}
</style>
@endpush

@push('js')
<script>
$(document).ready(function() {
    // Pencarian
    $("#searchInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".dokumen-list .card").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
});

// Preview dokumen
function previewDokumen(url) {
    window.open(url, '_blank');
    $.get(`/surat-tugas/${id}`, function(response) {
        if(response.status == 200) {
            $('#pdfViewer').attr('src', response.file_url);
            $('#previewModal').modal('showkaprodi');
        } else {
            Swal.fire('Error', 'Dokumen tidak dapat ditampilkan', 'error');
        }
    });
}
</script>
@endpush