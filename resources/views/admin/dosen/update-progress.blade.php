@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Update Progress Agenda</h3>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table id="agenda-table" class="table table-bordered table-striped">
                    <thead>
                        <tr class="text-center">
                            <th width="5%">No</th>
                            <th width="20%">Nama Agenda</th>
                            <th width="25%">Kegiatan</th>
                            <th width="10%">Tanggal</th>
                            <th width="15%">Progress</th>
                            <th width="10%">Status</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agendas as $agenda)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td>{{ $agenda->nama_agenda }}</td>
                            <td>
                                @if($agenda->kegiatanJurusan)
                                    {{ $agenda->kegiatanJurusan->nama_kegiatan_jurusan }}
                                @elseif($agenda->kegiatanProgramStudi)
                                    {{ $agenda->kegiatanProgramStudi->nama_kegiatan_program_studi }}
                                @endif
                            </td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d-m-Y') }}</td>
                            <td>
                                <div class="d-flex flex-column align-items-center">
                                    <div class="progress w-100" style="height: 20px; border-radius: 10px;">
                                        <div class="progress-bar {{ $agenda->progress['percentage'] == 100 ? 'bg-success' : 'bg-primary' }}" 
                                             role="progressbar" 
                                             style="width: {{ $agenda->progress['percentage'] }}%; border-radius: 10px;"
                                             aria-valuenow="{{ $agenda->progress['percentage'] }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ number_format($agenda->progress['percentage'], 1) }}%
                                        </div>
                                    </div>
                                    <small class="mt-1 text-muted">
                                        {{ $agenda->progress['uploaded_users'] }}/{{ $agenda->progress['total_users'] }} user
                                    </small>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusClass = $agenda->display_status === 'selesai' ? 'success' : 
                                                ($agenda->display_status === 'tahap penyelesaian' ? 'warning' : 'info');
                                    $statusText = ucfirst($agenda->display_status);
                                @endphp
                                <span class="badge badge-{{ $statusClass }} p-2" style="font-size: 0.9rem; min-width: 100px;">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info btn-sm show-detail mx-1" 
                                            data-id="{{ $agenda->agenda_id }}"
                                            data-agenda="{{ $agenda->nama_agenda }}"
                                            data-kegiatan="{{ $agenda->kegiatanJurusan ? $agenda->kegiatanJurusan->nama_kegiatan_jurusan : 
                                                            $agenda->kegiatanProgramStudi->nama_kegiatan_program_studi }}"
                                            data-tanggal="{{ $agenda->tanggal_agenda }}">
                                        <i class="fas fa-eye"></i> Detail & Hapus
                                    </button>
                                    <a href="{{ route('admin.dosen.update-progress.download', $agenda->agenda_id) }}" 
                                       class="btn btn-primary btn-sm mx-1">
                                        <i class="fas fa-download"></i> Unduh
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detail Dokumentasi</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-subtitle mb-3 text-muted">Informasi Agenda</h6>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="200">Nama Agenda</td>
                                        <td width="20">:</td>
                                        <td><span id="detail-nama-agenda"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Kegiatan</td>
                                        <td>:</td>
                                        <td><span id="detail-kegiatan"></span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-3 text-muted">Detail Progress</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 5%">No</th>
                                        <th style="width: 25%">Nama Dosen</th>
                                        <th style="width: 40%">Nama File</th>
                                        <th style="width: 15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="dokumentasi-list">
                                    <!-- Data will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    var table = $('#agenda-table').DataTable({
        responsive: true,
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        order: [[0, 'asc']], // Urutkan berdasarkan kolom No secara ascending
        pageLength: 25,
        columnDefs: [
            { "orderable": true, "targets": 0 } // Pastikan kolom No bisa diurutkan
        ]
    });

    // Handle show detail
    $('.show-detail').click(function() {
        const id = $(this).data('id');
        const url = "{{ route('admin.dosen.update-progress.detail', ':id') }}".replace(':id', id);
        
        // Update header info
        $('#detail-nama-agenda').text($(this).data('agenda'));
        $('#detail-kegiatan').text($(this).data('kegiatan'));
        
        // Load detail data
        $.ajax({
            url: url,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                $('#dokumentasi-list').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
                $('#detailModal').modal('show');
            },
            success: function(response) {
                if (response.status === 'success') {
                    let html = '';
                    if (response.data.user_submissions.length === 0) {
                        html = '<tr><td colspan="4" class="text-center">Tidak ada data</td></tr>';
                    } else {
                        response.data.user_submissions.forEach((item, index) => {
                            html += `
                                <tr>
                                    <td class="text-center">${index + 1}</td>
                                    <td>${item.nama_dosen}</td>
                                    <td>${item.has_submitted ? item.dokumentasi.nama_file : '<span class="text-muted">Belum upload</span>'}</td>
                                    <td class="text-center">
                                        ${item.has_submitted ? 
                                            `<button class="btn btn-danger btn-sm" onclick="deleteDoc(${item.dokumentasi.id}, '${item.nama_dosen}')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>` : '-'}
                                    </td>
                                </tr>`;
                        });
                    }
                    $('#dokumentasi-list').html(html);
                } else {
                    $('#dokumentasi-list').html(`
                        <tr>
                            <td colspan="4" class="text-center text-danger">
                                ${response.message || 'Gagal memuat data'}
                            </td>
                        </tr>`);
                }
            },
            error: function(xhr) {
                $('#dokumentasi-list').html(`
                    <tr>
                        <td colspan="4" class="text-center text-danger">
                            Terjadi kesalahan saat memuat data
                        </td>
                    </tr>`);
            }
        });
    });
});

function deleteDoc(id, nama) {
    const url = "{{ route('admin.dosen.update-progress.delete', ':id') }}".replace(':id', id);
    
    Swal.fire({
        title: 'Konfirmasi Hapus',
        html: `Apakah Anda yakin ingin menghapus dokumentasi dari <b>${nama}</b>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: url,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Dokumentasi berhasil dihapus'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message || 'Gagal menghapus dokumentasi', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', 'Gagal menghapus dokumentasi', 'error');
                }
            });
        }
    });
}
</script>
@endpush


@push('css')
<style>
.modal-xl { max-width: 90%; }
.progress { height: 20px; }
.badge { padding: 8px 12px; }
.btn-sm { margin: 0 2px; }
.table td { vertical-align: middle; }
</style>
@endpush