@php
    // Ensure $activemenu is always defined
    $activemenu = $activemenu ?? '';
    
    // Helper function to check if menu is active
    function isMenuActive($menu, $activemenu) {
        return $activemenu === $menu ? 'active' : '';
    }
    
    // Helper function to check if menu should be open
    function isMenuOpen($menuItems, $activemenu) {
        return in_array($activemenu, $menuItems) ? 'menu-open' : '';
    }
@endphp

<div class="sidebar">
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            {{-- Dashboard --}}
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ isMenuActive('dashboard', $activemenu) }}">
                    <i class="nav-icon fa fa-home"></i>
                    <p>Home</p>
                </a>
            </li>

            {{-- Profile --}}
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ isMenuActive('profile', $activemenu) }}">
                    <i class="nav-icon fas fa-user-cog"></i>
                    <p>Profile</p>
                </a>
            </li>
            
            {{-- Persetujuan Poin --}}
            <li class="nav-item">
                <a href="{{ route('admin.dosen.agenda.persetujuan-poin') }}" class="nav-link {{ isMenuActive('persetujuan-poin', $activemenu) }}">
                    <i class="fa fa-fw fa-star"></i>
                    <p>Persetujuan Poin</p>
                </a>
            </li>

            {{-- Menu Dosen --}}
            @php
                $dosenMenuItems = ['profile', 'agenda', 'progress', 'kegiatan-non-jti'];
            @endphp
            <li class="nav-item {{ isMenuOpen($dosenMenuItems, $activemenu) }}">
                <a href="#" class="nav-link {{ isMenuOpen($dosenMenuItems, $activemenu) ? 'active' : '' }}">
                    <i class="nav-icon fas fa-chalkboard-teacher"></i>
                    <p>
                        Dosen
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    {{-- Agenda --}}
                    <li class="nav-item">
                        <a href="#" class="nav-link {{ isMenuActive('agenda', $activemenu) }}">
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

                    {{-- Progress Kegiatan --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.dosen.progress-kegiatan') }}" class="nav-link {{ isMenuActive('progress', $activemenu) }}">
                            <i class="fa fa-tasks nav-icon"></i>
                            <p>Progress Kegiatan</p>
                        </a>
                    </li>

                    {{-- Update Progress Kegiatan --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.dosen.update-progress') }}" class="nav-link {{ isMenuActive('update-progress', $activemenu) }}">
                            <i class="fa fa-fw fa-hourglass-half"></i>
                            <p>Update Progress</p>
                        </a>
                    </li>

                    {{-- Kegiatan Non-JTI --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.dosen.kegiatan-non-jti') }}" class="nav-link {{ isMenuActive('kegiatan-non-jti', $activemenu) }}">
                            <i class="fa fa-fw fa-university"></i>
                            <p>Kegiatan Non-JTI</p>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Menu Kaprodi --}}
            @php
                $kaprodiMenuItems = ['surat-tugas'];
            @endphp
            <li class="nav-item {{ isMenuOpen($kaprodiMenuItems, $activemenu) }}">
                <a href="#" class="nav-link {{ isMenuOpen($kaprodiMenuItems, $activemenu) ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-tie"></i>
                    <p>
                        Kaprodi
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    {{-- Surat Tugas --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.kaprodi.surat-tugas') }}" class="nav-link {{ isMenuActive('surat-tugas', $activemenu) }}">
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
    .nav-icon {
        width: 1.6em !important;
        text-align: center;
    }
    .nav-sidebar .nav-link p {
        margin-left: 0.5rem;
    }
</style>
@endpush