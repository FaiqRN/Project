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

            <!-- Kegiatan -->
            <li class="nav-item">
                <a href="#" class="nav-link {{ ($activemenu == 'kegiatan')? 'active' : '' }}">
                    <i class="nav-icon fas fa-calendar-alt"></i>
                    <p>
                        Kegiatan
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('/kegiatan/lihat') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Melihat Kegiatan</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Statistik -->
            <li class="nav-item">
                <a href="#" class="nav-link {{ ($activemenu == 'statistik')? 'active' : '' }}">
                    <i class="nav-icon fas fa-chart-bar"></i>
                    <p>
                        Statistik
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('/statistik/beban-kerja') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Melihat Beban Kerja</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/statistik/hasil') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Hasil Statistik</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Surat Tugas -->
            <li class="nav-item">
                <a href="#" class="nav-link {{ ($activemenu == 'surat-tugas')? 'active' : '' }}">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>
                        Surat Tugas
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('/surat-tugas/download') }}" class="nav-link">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Download Dokumen</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div>