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
                    <i class="fa fa-fw fa-home nav-icon"></i>
                    <p>Beranda</p>
                </a>
            </li>

            <!-- Kegiatan -->
            <li class="nav-item">
                <a href="#" class="nav-link {{ ($activemenu == 'kegiatan')? 'active' : '' }}"> 
                    <i class="fa fa-fw fa-calendar nav-icon"></i>
                    <p>
                        Kegiatan
                        <i class="fa fa-fw fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('/kegiatan/lihat') }}" class="nav-link">
                            <i class="fa fa-fw fa-list-alt nav-icon"></i>
                            <p>Melihat Kegiatan</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Statistik -->
            <li class="nav-item">
                <a href="#" class="nav-link {{ ($activemenu == 'statistik')? 'active' : '' }}">
                    <i class="fa fa-fw fa-chart-bar nav-icon"></i>
                    <p>
                        Statistik
                        <i class="fa fa-fw fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('/statistik/beban-kerja') }}" class="nav-link">
                            <i class="fa fa-fw fa-binoculars nav-icon"></i>
                            <p>Melihat Beban Kerja</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/statistik/hasil') }}" class="nav-link">
                            <i class="fa fa-fw fa-sitemap"></i>
                            <p>Hasil Statistik</p>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Surat Tugas -->
            <li class="nav-item">
                <a href="#" class="nav-link {{ ($activemenu == 'surat-tugas')? 'active' : '' }}">
                    <i class="fa fa-fw fa-file-alt nav-icon"></i>
                    <p>
                        Surat Tugas
                        <i class="fa fa-fw fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('/kaprodi/surat-tugas/download') }}" class="nav-link">
                            <i class="fa fa-fw fa-file-download nav-icon"></i>
                            <p>Download Dokumen</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
</div>