@php
    $activemenu = $activemenu ?? '';
@endphp
<div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Dashboard -->
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ ($activemenu == 'dashboard')? 'active' : '' }}">
                    <i class="nav-icon fa fa-home"></i>
                    <p>Home</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ ($activemenu == 'profile')? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-cog"></i>
                    <p>Profile</p>
                </a>
            </li>
            
            <li class="nav-item">
                <a href="{{ route('admin.dosen.agenda.persetujuan-poin') }}" class="nav-link {{ ($activemenu == 'persetujuan-poin')? 'active' : '' }}">
                    <i class="fa fa-fw fa-star"></i>
                    <p>Persetujuan Poin</p>
                </a>
            </li>

            <!-- Menu Dosen -->
            <li class="nav-item {{ in_array($activemenu, ['profile', 'agenda', 'progress', 'kegiatan-non-jti']) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ in_array($activemenu, ['profile', 'agenda', 'progress', 'kegiatan-non-jti']) ? 'active' : '' }}">
                    <i class="nav-icon fas fa-chalkboard-teacher"></i>
                    <p>
                        Dosen
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <!-- Agenda -->
                    <li class="nav-item">
                        <a href="#" class="nav-link {{ ($activemenu == 'agenda')? 'active' : '' }}">
                            <i class="fa fa-calendar nav-icon"></i>
                            <p>
                                Kegiatan & Agenda
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.dosen.agenda.jabatan') }}" class="nav-link">
                                            <i class="fa fa-fw fa-circle"></i>
                                    <p>Pilih Jabatan</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('admin.dosen.agenda.kegiatan') }}" class="nav-link">
                                            <i class="fa fa-fw fa-circle"></i>
                                    <p>Kegiatan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.dosen.agenda.pilih-anggota') }}" class="nav-link">
                                            <i class="fa fa-fw fa-circle"></i>
                                    <p>Pilih Anggota</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.dosen.agenda.unggah-dokumen') }}" class="nav-link">
                                            <i class="fa fa-fw fa-circle"></i>
                                    <p>Unggah Dokumen</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Progress Kegiatan -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dosen.progress-kegiatan') }}" class="nav-link {{ ($activemenu == 'progress')? 'active' : '' }}">
                            <i class="fa fa-tasks nav-icon"></i>
                            <p>Progress Kegiatan</p>
                        </a>
                    </li>

                    <!-- Update Progress Kegiatan -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dosen.update-progress') }}" class="nav-link {{ ($activemenu == 'update-progress')? 'active' : '' }}">
                            <i class="fa fa-fw fa-hourglass-half"></i>
                            <p>Update Progress</p>
                        </a>
                    </li>

                    <!-- Kegiatan Non-JTI -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dosen.kegiatan-non-jti') }}" class="nav-link {{ ($activemenu == 'kegiatan-non-jti')? 'active' : '' }}">
                            <i class="fa fa-fw fa-university"></i>
                            <p>Kegiatan Non-JTI</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Menu Kaprodi -->
            <li class="nav-item {{ in_array($activemenu, ['surat-tugas']) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ in_array($activemenu, ['surat-tugas']) ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-tie"></i>
                    <p>
                        Kaprodi
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <!-- Surat Tugas -->
                    <li class="nav-item">
                        <a href="{{ route('admin.kaprodi.surat-tugas') }}" class="nav-link {{ ($activemenu == 'surat-tugas')? 'active' : '' }}">
                            <i class="fa fa-fw fa-file"></i>
                            <p>Surat Tugas</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div>

@push('css')
<style>
    .nav-sidebar .nav-treeview .nav-treeview {
        padding-left: 1rem;
    }
    .nav-sidebar .nav-treeview .nav-treeview .nav-item {
        font-size: 0.95em;
    }
    .nav-sidebar .menu-open > .nav-link i.right {
        transform: rotate(90deg);
    }
    
    /* Memastikan ukuran ikon konsisten */
    .nav-icon {
        width: 1.6em !important;
        text-align: center;
    }
    
    /* Memberikan sedikit jarak antara ikon dan teks */
    .nav-sidebar .nav-link p {
        margin-left: 0.5rem;
    }
</style>
@endpush