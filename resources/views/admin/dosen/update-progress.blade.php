@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Update Progress Agenda</h3>
                </div>
                
                <div class="card-body">
                    @if($agendas->isEmpty())
                        <div class="alert alert-info">
                            Tidak ada agenda yang tersedia.
                        </div>
                    @else
                        <table id="agenda-table" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Nama Agenda</th>
                                    <th width="20%">Kegiatan</th>
                                    <th width="10%">Tanggal</th>
                                    <th width="15%">Progress</th>
                                    <th width="15%">Status</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($agendas as $index => $agenda)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $agenda->nama_agenda }}</td>
                                    <td>
                                        @if($agenda->kegiatanJurusan)
                                            {{ $agenda->kegiatanJurusan->nama_kegiatan_jurusan }}
                                        @elseif($agenda->kegiatanProgramStudi)
                                            {{ $agenda->kegiatanProgramStudi->nama_kegiatan_program_studi }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($agenda->tanggal_agenda)->format('d-m-Y') }}</td>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" 
                                                style="width: {{ $agenda->progress['percentage'] }}%"
                                                aria-valuenow="{{ $agenda->progress['percentage'] }}" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                                {{ $agenda->progress['percentage'] }}%
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ $agenda->progress['uploaded_users'] }}/{{ $agenda->progress['total_users'] }} user
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $agenda->display_status === 'selesai' ? 'success' : ($agenda->display_status === 'tahap penyelesaian' ? 'warning' : 'info') }}">
                                            {{ ucfirst($agenda->display_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-sm btn-detail" 
                                                data-id="{{ $agenda->agenda_id }}">
                                            <i class="fas fa-eye"></i> Detail & Hapus
                                        </button>
                                        <button type="button" class="btn btn-primary btn-sm btn-download" 
                                                data-id="{{ $agenda->agenda_id }}">
                                            <i class="fas fa-download"></i> Unduh
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Modal Detail -->
                        <div class="modal fade" id="detailModal" tabindex="-1">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Detail Agenda</h5>
                                        <button type="button" class="close" data-dismiss="modal">
                                            <span>&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>Informasi Agenda</h6>
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td>Nama Agenda</td>
                                                        <td>:</td>
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
                                                <h6>Progress</h6>
                                                <table class="table table-borderless">
                                                    <tr>
                                                        <td>Total User</td>
                                                        <td>:</td>
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
                                            <div class="submission-list mt-4">
                                                <h6>Daftar Dokumentasi</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Nama User</th>
                                                                <th>Status</th>
                                                                <th>Tanggal Upload</th>
                                                                <th>Nama File</th>
                                                                <th>Deskripsi</th>
                                                                <th>Aksi</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="submission-table-body">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
    
    @push('css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.24/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.0.18/sweetalert2.min.css">
    <style>
    .progress {
        height: 20px;
        margin-bottom: 5px;
    }
    .progress-bar {
        background-color: #28a745;
    }
    .badge {
        padding: 8px 12px;
        font-size: 0.9em;
    }
    .badge-warning {
        background-color: #ffc107;
        color: #000;
    }
    .badge-success {
        background-color: #28a745;
        color: #fff;
    }
    .badge-info {
        background-color: #17a2b8;
        color: #fff;
    }
    .table-borderless td {
        padding: 0.5rem;
    }
    .modal-xl {
        max-width: 1140px;
    }
    .btn {
        margin-right: 5px;
    }
    .btn i {
        margin-right: 3px;
    }
    </style>
    @endpush
    
    @push('js')
    <script src="//cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="//cdn.datatables.net/1.10.24/js/dataTables.bootstrap4.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.0.18/sweetalert2.min.js"></script>
    <script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#agenda-table').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
            }
        });
    
        // Handle detail button click
        $('.btn-detail').click(function() {
        const id = $(this).data('id');
        
        $.ajax({
            // Pastikan URL sesuai dengan route yang didefinisikan
            url: `/admin/dosen/update-progress/${id}/detail`,
            type: 'GET',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function() {
                Swal.fire({
                    title: 'Loading...',
                    text: 'Mengambil data detail agenda',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                Swal.close();
                console.log('Response:', response); // Debug response
                
                if (response.status === 'success') {
                    const agenda = response.data.agenda;
                    const submissions = response.data.user_submissions;
                    
                    // Update modal content
                    $('#detail-nama-agenda').text(agenda.nama_agenda);
                    $('#detail-kegiatan').text(
                        agenda.kegiatan_jurusan ? 
                            agenda.kegiatan_jurusan.nama_kegiatan_jurusan : 
                            (agenda.kegiatan_program_studi ? 
                                agenda.kegiatan_program_studi.nama_kegiatan_program_studi : 
                                '-')
                    );
                    $('#detail-tanggal').text(moment(agenda.tanggal_agenda).format('DD-MM-YYYY'));
                    
                    // Update progress information
                    const uploadedCount = submissions.filter(s => s.has_submitted).length;
                    $('#detail-total-user').text(submissions.length);
                    $('#detail-uploaded').text(uploadedCount);
                    
                    // Generate submissions table content
                    const tableBody = submissions.map(submission => `
                        <tr>
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
                                ${submission.has_submitted ? `
                                    <button class="btn btn-danger btn-sm delete-doc" data-id="${submission.dokumentasi.id}">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                ` : '-'}
                            </td>
                        </tr>
                    `).join('');
                    
                    $('#submission-table-body').html(tableBody);
                    $('#detailModal').modal('show');
                } else {
                    Swal.fire('Error', response.message || 'Terjadi kesalahan', 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                console.error('AJAX Error:', {xhr, status, error}); // Debug error
                Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
            }
        });
    });

    
        // Handle download button click
        $(document).on('click', '.delete-doc', function() {
        const id = $(this).data('id');
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus dokumentasi ini?',
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
                            Swal.fire('Berhasil', 'Dokumentasi berhasil dihapus', 'success')
                            .then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('Error', response.message || 'Gagal menghapus dokumentasi', 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Terjadi kesalahan pada server', 'error');
                    }
                });
            }
        });
    });

    
        // Handle delete document
        $(document).on('click', '.delete-doc', function() {
            const id = $(this).data('id');
            
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus dokumentasi ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteDocument(id);
                }
            });
        });
    
        // Helper Functions
        function updateModalContent(data) {
            const { agenda, user_submissions } = data;
            
            // Update basic info
            $('#detail-nama-agenda').text(agenda.nama_agenda);
            $('#detail-kegiatan').text(agenda.kegiatan_jurusan ? 
                agenda.kegiatan_jurusan.nama_kegiatan_jurusan : 
                (agenda.kegiatan_program_studi ? 
                    agenda.kegiatan_program_studi.nama_kegiatan_program_studi : 
                    '-'));
            $('#detail-tanggal').text(moment(agenda.tanggal_agenda).format('DD-MM-YYYY'));
            
            // Update progress info
            const uploadedCount = user_submissions.filter(s => s.has_submitted).length;
            const totalCount = user_submissions.length;
            
            $('#detail-total-user').text(totalCount);
            $('#detail-uploaded').text(uploadedCount);
            $('#detail-status').html(getStatusBadge(uploadedCount, totalCount));
            
            // Update submissions table
            const tableBody = user_submissions.map(submission => `
                <tr>
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
                        ${submission.has_submitted ? `
                            <button class="btn btn-danger btn-sm delete-doc" data-id="${submission.dokumentasi.id}">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        ` : '-'}
                    </td>
                </tr>
            `).join('');
            
            $('#submission-table-body').html(tableBody);
        }
    
        function deleteDocument(id) {
            $.ajax({
                url: `/admin/dosen/update-progress/${id}/delete`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    Swal.fire({
                        title: 'Loading...',
                        text: 'Menghapus dokumentasi',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#detailModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Dokumentasi berhasil dihapus'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        showError('Gagal menghapus dokumentasi', response.message);
                    }
                },
                error: function(xhr) {
                    handleAjaxError(xhr);
                }
            });
        }
    
        function getStatusBadge(uploadedCount, totalCount) {
            let status, badgeClass;
            
            if (uploadedCount === 0) {
                status = 'Berlangsung';
                badgeClass = 'info';
            } else if (uploadedCount === totalCount) {
                status = 'Selesai';
                badgeClass = 'success';
            } else {
                status = 'Tahap Penyelesaian';
                badgeClass = 'warning';
            }
            
            return `<span class="badge badge-${badgeClass}">${status}</span>`;
        }
    
        function showError(title, message) {
            Swal.fire({
                icon: 'error',
                title: title,
                text: message
            });
        }
    
        function handleAjaxError(xhr) {
            console.error('Ajax Error:', xhr);
            showError('Error', xhr.responseJSON?.message || 'Terjadi kesalahan pada server');
        }
    });
    </script>
    @endpush