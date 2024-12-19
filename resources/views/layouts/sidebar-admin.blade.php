@php
    $activemenu = $activemenu ?? '';
    $agendaMenus = ['agenda', 'progress', 'kegiatan-non-jti'];
    $currentRoute = request()->route()->getName();
@endphp

<div class="sidebar">
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            {{-- Dashboard --}}
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ $activemenu === 'dashboard' ? 'active' : '' }}">
                    <i class="nav-icon fa fa-home"></i>
                    <p>Home</p>
                </a>
            </li>

            {{-- Profile --}}
            <li class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ $activemenu === 'users' ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-cog"></i>
                    <p>Manajemen User</p>
                </a>
            </li>

            {{-- Menu Dosen --}}
            <li class="nav-item {{ in_array($activemenu, $agendaMenus) ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ in_array($activemenu, $agendaMenus) ? 'active' : '' }}">
                    <i class="nav-icon fas fa-chalkboard-teacher"></i>
                    <p>
                        Dosen
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    {{-- Agenda --}}
                    <li class="nav-item">
                        <a href="#" class="nav-link {{ $activemenu === 'agenda' ? 'active' : '' }}">
                            <i class="fa fa-calendar nav-icon"></i>
                            <p>
                                Kegiatan & Agenda
                                <i class="right fa fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('admin.dosen.agenda.jabatan') }}" class="nav-link {{ $currentRoute === 'admin.dosen.agenda.jabatan' ? 'active' : '' }}">
                                    <i class="fa fa-fw fa-circle nav-icon"></i>
                                    <p>Pilih Jabatan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.dosen.agenda.kegiatan') }}" class="nav-link {{ $currentRoute === 'admin.dosen.agenda.kegiatan' ? 'active' : '' }}">
                                    <i class="fa fa-fw fa-circle nav-icon"></i>
                                    <p>Kegiatan</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{route ('admin.dosen.agenda.agenda-setting')}}" class="nav-link {{$currentRoute === 'admin.dosen.agenda.agenda-setting' ? 'active' : ''}}">
                                    <i class="fa fa-fw fa-circle nav-icon"></i>
                                    <p>Agenda</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.dosen.agenda.pilih-anggota.index') }}" class="nav-link {{ $currentRoute === 'admin.dosen.agenda.pilih-anggota.index' ? 'active' : '' }}">
                                    <i class="fa fa-fw fa-circle nav-icon"></i>
                                    <p>Pilih Anggota</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.dosen.agenda.persetujuan-poin.index') }}" class="nav-link {{ str_starts_with($currentRoute, 'admin.dosen.agenda.persetujuan-poin.') ? 'active' : '' }}">
                                    <i class="fa fa-fw fa-circle nav-icon"></i>
                                    <p>Persetujuan Poin</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.dosen.agenda.unggah-dokumen') }}" class="nav-link {{ $currentRoute === 'admin.dosen.agenda.unggah-dokumen' ? 'active' : '' }}">
                                    <i class="fa fa-fw fa-circle nav-icon"></i>
                                    <p>Unggah Dokumen</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                    {{-- Update Progress --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.dosen.update-progress') }}" 
                           class="nav-link {{ request()->routeIs('admin.dosen.update-progress') ? 'active' : '' }}">
                            <i class="fa fa-fw fa-hourglass-half nav-icon"></i>
                            <p>Update Progress</p>
                        </a>
                    </li>

                    {{-- Kegiatan Non-JTI --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.dosen.kegiatan-non-jti.index') }}" class="nav-link {{ $currentRoute === 'admin.dosen.kegiatan-non-jti' ? 'active' : '' }}">
                            <i class="fa fa-fw fa-university nav-icon"></i>
                            <p>Kegiatan Non-JTI</p>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Menu Kaprodi --}}
            <li class="nav-item">
                <a href="#" class="nav-link {{ str_contains($currentRoute, 'admin.kaprodi') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user-tie"></i>
                    <p>
                        Kaprodi
                        <i class="right fa fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    {{-- Surat Tugas --}}
                    <li class="nav-item">
                        <a href="{{ route('admin.kaprodi.surat-tugas') }}" class="nav-link {{ $currentRoute === 'admin.kaprodi.surat-tugas' ? 'active' : '' }}">
                            <i class="fa fa-fw fa-file nav-icon"></i>
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