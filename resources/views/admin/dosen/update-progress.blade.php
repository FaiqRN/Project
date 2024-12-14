@extends('layouts.template')

@section('content')
@php
function getStatusBadgeClass($status) {
    return match($status) {
        'selesai' => 'success',
        'tahap penyelesaian' => 'warning',
        'berlangsung' => 'info',
        default => 'secondary'
    };
}
@endphp

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Update Progress Agenda</h3>
        </div>
        
        <div class="card-body">
            <table id="agenda-table" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Agenda</th>
                        <th>Kegiatan</th>
                        <th>Tanggal</th>
                        <th>Progress</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agendas as $index => $agenda)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $agenda->nama_agenda }}</td>
                        <td>
                            @if($agenda->kegiatanJurusan)
                                {{ $agenda->kegiatanJurusan->nama_kegiatan_jurusan }}
                            @elseif($agenda->kegiatanProgramStudi)
                                {{ $agenda->kegiatanProgramStudi->nama_kegiatan_program_studi }}
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d-m-Y') }}</td>
                        <td>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: {{ $agenda->progress['percentage'] }}%">
                                    {{ number_format($agenda->progress['percentage'], 1) }}%
                                </div>
                            </div>
                            <small>{{ $agenda->progress['uploaded_users'] }}/{{ $agenda->progress['total_users'] }} user</small>
                        </td>
                        <td>
                            <span class="badge badge-{{ getStatusBadgeClass($agenda->display_status) }}">
                                {{ ucfirst($agenda->display_status) }}
                            </span>
                        </td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm show-detail" 
                                    data-id="{{ $agenda->agenda_id }}" 
                                    title="Lihat Detail">
                                <i class="fas fa-eye"></i> Detail & Hapus
                            </button>
                            <a href="{{ route('admin.dosen.update-progress.download', $agenda->agenda_id) }}" 
                               class="btn btn-primary btn-sm"
                               title="Download Dokumen">
                                <i class="fas fa-download"></i> Unduh
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Progress Agenda</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Informasi Agenda -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold mb-3">Informasi Agenda</h6>
                        <table class="table table-sm">
                            <tr>
                                <td width="30%">Nama Agenda</td>
                                <td width="5%">:</td>
                                <td id="detail-nama-agenda"></td>
                            </tr>
                            <tr>
                                <td>Kegiatan</td>
                                <td>:</td>
                                <td id="detail-kegiatan"></td>
                            </tr>
                            <tr>
                                <td>Tanggal</td>
                                <td>:</td>
                                <td id="detail-tanggal"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold mb-3">Progress</h6>
                        <table class="table table-sm">
                            <tr>
                                <td width="30%">Total User</td>
                                <td width="5%">:</td>
                                <td id="detail-total-user"></td>
                            </tr>
                            <tr>
                                <td>Sudah Upload</td>
                                <td>:</td>
                                <td id="detail-uploaded"></td>
                            </tr>
                            <tr>
                                <td>Status</td>
                                <td>:</td>
                                <td id="detail-status"></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Tabel Dokumentasi -->
                <div class="dokumentasi-section">
                    <h6 class="font-weight-bold mb-3">Daftar Dokumentasi</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama User</th>
                                    <th>Status</th>
                                    <th>Tanggal Upload</th>
                                    <th>Nama File</th>
                                    <th>Deskripsi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="dokumentasi-list"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
.progress { height: 20px; margin-bottom: 5px; }
.progress-bar { background-color: #28a745; }
.badge { padding: 8px 12px; }
.badge-success { background-color: #28a745; color: white; }
.badge-warning { background-color: #ffc107; color: black; }
.badge-info { background-color: #17a2b8; color: white; }
.badge-secondary { background-color: #6c757d; color: white; }
.btn-sm { margin: 0 2px; }
</style>
@endpush

@push('js')
<script>
    $(document).ready(function() {
        // DataTable initialization
        $('#agenda-table').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    
        // Event handler untuk tombol detail
        $(document).on('click', '.show-detail', function() {
            const id = $(this).data('id');
            
            $.ajax({
                url: `/admin/dosen/update-progress/${id}/detail`,
                method: 'GET',
                beforeSend: function() {
                    Swal.fire({
                        title: 'Memuat...',
                        html: 'Mohon tunggu sebentar',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    Swal.close();
                    console.log('Response:', response); // Debug log
                    
                    if (response.status === 'success' && response.data) {
                        const agenda = response.data.agenda;
                        const submissions = response.data.user_submissions;
                        
                        // Update informasi agenda
                        $('#detail-nama-agenda').text(agenda.nama_agenda || '-');
                        $('#detail-kegiatan').text(
                            agenda.kegiatan_jurusan ? 
                            agenda.kegiatan_jurusan.nama_kegiatan_jurusan : 
                            (agenda.kegiatan_program_studi ? 
                                agenda.kegiatan_program_studi.nama_kegiatan_program_studi : 
                                '-')
                        );
                        $('#detail-tanggal').text(moment(agenda.tanggal_agenda).format('DD-MM-YYYY'));
                        
                        // Update progress info
                        const uploadedCount = submissions.filter(s => s.has_submitted).length;
                        $('#detail-total-user').text(submissions.length);
                        $('#detail-uploaded').text(uploadedCount);
                        
                        // Generate tabel dokumentasi
                        let tableContent = '';
                        submissions.forEach((submission, index) => {
                            tableContent += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${submission.user_name}</td>
                                    <td>
                                        <span class="badge badge-${submission.has_submitted ? 'success' : 'warning'}">
                                            ${submission.has_submitted ? 'Sudah Upload' : 'Belum Upload'}
                                        </span>
                                    </td>
                                    <td>${submission.has_submitted ? moment(submission.submission_date).format('DD-MM-YYYY') : '-'}</td>
                                    <td>${submission.has_submitted ? submission.dokumentasi.nama : '-'}</td>
                                    <td>${submission.has_submitted ? submission.dokumentasi.deskripsi : '-'}</td>
                                    <td>
                                        ${submission.has_submitted ? 
                                            `<button class="btn btn-danger btn-sm delete-doc" 
                                                    onclick="deleteDoc(${submission.dokumentasi.id})">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>` : 
                                            '-'}
                                    </td>
                                </tr>
                            `;
                        });
                        
                        $('#dokumentasi-list').html(tableContent);
                        $('#detailModal').modal('show');
                    } else {
                        Swal.fire('Error', 'Data tidak ditemukan', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error); // Debug log
                    Swal.fire('Error', 'Gagal memuat detail: ' + error, 'error');
                }
            });
        });
    });
    
    // Function untuk delete dokumentasi
    function deleteDoc(id) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Anda yakin ingin menghapus dokumentasi ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/dosen/update-progress/${id}/delete`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: 'Dokumentasi berhasil dihapus',
                                showConfirmButton: false,
                                timer: 1500
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