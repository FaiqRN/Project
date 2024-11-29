@php
    // Ensure $activemenu is always defined
    $activemenu = $activemenu ?? '';
    
    // Helper function to check if menu is active
    function isMenuActive($menu, $activemenu) {
        return $activemenu === $menu ? 'active' : '';
    }
@endphp

<div class="sidebar">
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            {{-- Beranda --}}
            <li class="nav-item">
                <a href="{{ url('/') }}" class="nav-link {{ isMenuActive('beranda', $activemenu) }}">
                    <i class="fa fa-fw fa-home nav-icon"></i>
                    <p>Beranda</p>
                </a>
            </li>

            {{-- Kegiatan --}}
            <li class="nav-item">
                <a href="#" class="nav-link {{ isMenuActive('kegiatan', $activemenu) }}">
                    <i class="fa fa-fw fa-calendar nav-icon"></i>
                    <p>
                        Kegiatan
                        <i class="fa fa-fw fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('/kegiatan/lihat') }}" class="nav-link">
                            <i class="fas fa-eye nav-icon"></i>
                            <p>Melihat Kegiatan</p>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Statistik --}}
            <li class="nav-item">
                <a href="#" class="nav-link {{ isMenuActive('statistik', $activemenu) }}">
                    <i class="fa fa-fw fa-chart-bar nav-icon"></i>
                    <p>
                        Statistik
                        <i class="fa fa-fw fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('/statistik/beban-kerja') }}" class="nav-link">
                            <i class="fas fa-briefcase nav-icon"></i>
                            <p>Melihat Beban Kerja</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/statistik/hasil') }}" class="nav-link">
                            <i class="fas fa-layer-group nav-icon"></i>
                            <p>Hasil Statistik</p>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Surat Tugas --}}
            <li class="nav-item">
                <a href="#" class="nav-link {{ isMenuActive('surat-tugas', $activemenu) }}">
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

@push('css')
<style>
    .nav-sidebar .nav-treeview {
        padding-left: 1rem;
    }
    .nav-sidebar .nav-treeview .nav-item {
        font-size: 0.95em;
    }
    .nav-icon {
        width: 1.6em !important;
        text-align: center;
    }
    .nav-sidebar .nav-link p {
        margin-left: 0.5rem;
    }
    .nav-sidebar .nav-link.active i.right {
        transform: rotate(-90deg);
    }
</style>
@endpush