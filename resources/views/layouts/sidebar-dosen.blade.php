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
                    <i class="nav-icon fas fa-home"></i>
                    <p>Beranda</p>
                </a>
            </li>

            {{-- Agenda --}}
            <li class="nav-item">
                <a href="#" class="nav-link {{ isMenuActive('agenda', $activemenu) }}">
                    <i class="nav-icon fas fa-calendar"></i>
                    <p>
                        Agenda
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ url('/kegiatan') }}" class="nav-link">
                            <i class="nav-icon fa fa-fw fa-university"></i>
                            <p>Kegiatan</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/pilih-anggota') }}" class="nav-link">
                            <i class="nav-icon fa fa-fw fa-users"></i>
                            <p>Pilih Anggota</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/pembagian-poin') }}" class="nav-link">
                            <i class="nav-icon fa fa-fw fa-plus-square"></i>
                            <p>Penambahan Poin</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/unggah-dokumen') }}" class="nav-link">
                            <i class="nav-icon fas fa-file-upload"></i>
                            <p>Unggah Dokumen Akhir</p>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Progress Kegiatan --}}
            <li class="nav-item">
                <a href="{{ url('/progress-kegiatan') }}" class="nav-link {{ isMenuActive('progress-kegiatan', $activemenu) }}">
                    <i class="nav-icon fa fa-tasks"></i>
                    <p>Progress Kegiatan</p>
                </a>
            </li>

            {{-- Update Progress Agenda --}}
            <li class="nav-item">
                <a href="{{ url('/update-progress') }}" class="nav-link {{ isMenuActive('update-progress', $activemenu) }}">
                    <i class="nav-icon fa fa-fw fa-hourglass-half"></i>
                    <p>Update Progress Agenda</p>
                </a>
            </li>

            {{-- Kegiatan Non-JTI --}}
            <li class="nav-item">
                <a href="{{ url('/kegiatan-non-jti') }}" class="nav-link {{ isMenuActive('kegiatan-non-jti', $activemenu) }}">
                    <i class="nav-icon fa fa-fw fa-university"></i>
                    <p>Kegiatan Non-JTI</p>
                </a>
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
    .nav-sidebar .menu-open > .nav-link i.right {
        transform: rotate(-90deg);
    }
</style>
@endpush