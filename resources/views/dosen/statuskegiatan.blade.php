@extends('layouts.template')


@section('content')
<div class="container-fluid">
    <div class="row" id="kegiatan-container">


    </div>
</div>
@endsection


@push('css')
<style>
    .progress {
        height: 25px;
    }
    .progress-card {
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        background-color: white;
    }
    .badge-jurusan {
        background-color: #03346E;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 12px;
    }
    .badge-prodi {
        background-color: #28a745;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        font-size: 12px;
    }
    .progress-bar {
        background-color: #03346E;
        font-size: 14px;
        line-height: 25px;
    }
    .text-primary {
        color: #03346E !important;
    }
    .progress-card h5 {
        font-weight: bold;
    }
    .progress-info {
        font-size: 14px;
        margin-bottom: 8px;
    }
</style>
@endpush


@push('js')
<script>
$(document).ready(function() {
    function loadProgressKegiatan() {
        $.ajax({
            url: '{{ route("dosen.statuskegiatan.get-progress") }}',
            method: 'GET',
            success: function(response) {
                const container = $('#kegiatan-container');
                container.empty();
               
                if (response.length === 0) {
                    container.append(`
                        <div class="col-12">
                            <div class="alert alert-info">
                                Belum ada data kegiatan yang tersedia.
                            </div>
                        </div>
                    `);
                    return;
                }
               
                response.forEach(function(kegiatan) {
                    const badgeClass = kegiatan.jenis_kegiatan === 'Kegiatan Jurusan' ?
                                     'badge-jurusan' : 'badge-prodi';
                   
                    const card = `
                        <div class="col-md-6">
                            <div class="progress-card p-4">
                                <h5 class="text-primary mb-3">${kegiatan.nama_kegiatan}</h5>
                                <p class="progress-info">Jumlah Agenda: ${kegiatan.jumlah_agenda}</p>
                                <p class="progress-info">Progress Agenda: ${kegiatan.agenda_selesai}/${kegiatan.jumlah_agenda}</p>
                               
                                <div class="progress mb-3">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: ${kegiatan.progress}%"
                                         aria-valuenow="${kegiatan.progress}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        ${kegiatan.progress}%
                                    </div>
                                </div>
                               
                                <span class="badge ${badgeClass}">${kegiatan.jenis_kegiatan}</span>
                            </div>
                        </div>
                    `;
                    container.append(card);
                });
            },
            error: function(xhr, status, error) {
                console.error('Error loading progress:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal memuat data progress kegiatan'
                });
            }
        });
    }


    loadProgressKegiatan();


    setInterval(loadProgressKegiatan, 30000);
});
</script>
@endpush