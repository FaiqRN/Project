<!-- resources/views/layouts/sidebar-dosen.blade.php -->
@php
    $activemenu = $activemenu ?? '';
@endphp
<div class="sidebar">
    <!-- Sidebar Menu -->
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            <!-- Beranda -->
            <li class="nav-item">
                <a href="{{ url('/') }}" class="nav-link {{ ($activemenu == 'beranda')? 'active' : '' }}">
                    <i class="nav-icon fas fa-home"></i>
                    <p>Beranda</p>
                </a>
            </li>

            <!-- Agenda -->
            <li class="nav-item">
                <a href="#" class="nav-link {{ ($activemenu == 'agenda')? 'active' : '' }}">
                    <i class="nav-icon fas fa-calendar"></i>
                    <p>
                        Agenda
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('/kegiatan') }}" class="nav-link">
                            <i class="fa fa-fw fa-university"></i>
                            <p>Kegiatan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/pilih-anggota') }}" class="nav-link">
                            <i class="fa fa-fw fa-users"></i>
                            <p>Pilih Anggota</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/pembagian-poin') }}" class="nav-link">
                            <i class="fa fa-fw fa-plus-square"></i>
                            <p>Penambahan Poin</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/unggah-dokumen') }}" class="nav-link">
                            <i class="fas fa-file-upload"></i>
                            <p>Unggah Dokumen Akhir</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Progress Kegiatan -->
            <li class="nav-item">
                <a href="{{ url('/progress-kegiatan') }}" class="nav-link {{ ($activemenu == 'progress-kegiatan')? 'active' : '' }}">
                    <i class="fa fa-tasks nav-icon"></i>
                    <p>Progress Kegiatan</p>
                </a>
            </li>

            <!-- Update Progress Agenda -->
            <li class="nav-item">
                <a href="{{ url('/update-progress') }}" class="nav-link {{ ($activemenu == 'update-progress')? 'active' : '' }}">
                    <i class="fa fa-fw fa-hourglass-half"></i>
                    <p>Update Progress Agenda</p>
                </a>
            </li>

            <!-- Kegiatan Non-JTI -->
            <li class="nav-item">
                <a href="{{ url('/kegiatan-non-jti') }}" class="nav-link {{ ($activemenu == 'kegiatan-non-jti')? 'active' : '' }}">
                    <i class="fa fa-fw fa-university"></i>
                    <p>Kegiatan Non-JTI</p>
                </a>
            </li>
        </ul>
    </nav>
</div>