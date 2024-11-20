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
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>Dashboard</p>
                </a>
            </li>

            <!-- Menu Dosen -->
            <li class="nav-item {{ in_array($activemenu, ['profile', 'agenda', 'progress', 'kegiatan-non-jti']) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ in_array($activemenu, ['profile', 'agenda', 'progress', 'kegiatan-non-jti']) ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-tie"></i>
                    <p>
                        Dosen
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <!-- Profile -->
                    <li class="nav-item">
                        <a href="{{ route('admin.users.index') }}" class="nav-link {{ ($activemenu == 'profile')? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Profile</p>
                        </a>
                    </li>

                    <!-- Agenda -->
                    <li class="nav-item">
                        <a href="#" class="nav-link {{ ($activemenu == 'agenda')? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>
                                Agenda
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.dosen.agenda.kegiatan') }}" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Kegiatan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.dosen.agenda.pilih-anggota') }}" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Pilih Anggota</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.dosen.agenda.pembagian-poin') }}" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Pembagian Poin</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.dosen.agenda.unggah-dokumen') }}" class="nav-link">
                                    <i class="far fa-dot-circle nav-icon"></i>
                                    <p>Unggah Dokumen</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <!-- Progress Kegiatan -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dosen.progress-kegiatan') }}" class="nav-link {{ ($activemenu == 'progress')? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Progress Kegiatan</p>
                        </a>
                    </li>

                    <!-- Update Progress Kegiatan -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dosen.update-progress') }}" class="nav-link {{ ($activemenu == 'update-progress')? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Update Progress</p>
                        </a>
                    </li>

                    <!-- Kegiatan Non-JTI -->
                    <li class="nav-item">
                        <a href="{{ route('admin.dosen.kegiatan-non-jti') }}" class="nav-link {{ ($activemenu == 'kegiatan-non-jti')? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Kegiatan Non-JTI</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Menu Kaprodi -->
            <li class="nav-item {{ in_array($activemenu, ['surat-tugas']) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ in_array($activemenu, ['surat-tugas']) ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-graduate"></i>
                    <p>
                        Kaprodi
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <!-- Surat Tugas -->
                    <li class="nav-item">
                        <a href="{{ route('admin.kaprodi.surat-tugas') }}" class="nav-link {{ ($activemenu == 'surat-tugas')? 'active' : '' }}">
                            <i class="far fa-circle nav-icon"></i>
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
</style>
@endpush