{{-- First, set default value for $activemenu if it's not set --}}
@php
    $activemenu = $activemenu ?? '';
@endphp

<div class="sidebar">
    <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
            {{-- Beranda --}}
            <li class="nav-item">
                <a href="{{ url('/') }}" class="nav-link {{ $activemenu === 'beranda' ? 'active' : '' }}">
                    <i class="fa fa-fw fa-home nav-icon"></i>
                    <p>Beranda</p>
                </a>
            </li>

            {{-- Kegiatan --}}
            <li class="nav-item">
                <a href="#" class="nav-link {{ $activemenu === 'kegiatan' ? 'active' : '' }}">
                    <i class="fa fa-fw fa-calendar nav-icon"></i>
                    <p>
                        Kegiatan
                        <i class="fa fa-fw fa-angle-left right"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('kaprodi.kegiatan') }}" class="nav-link">
                            <i class="fas fa-eye nav-icon"></i>
                            <p>Melihat Kegiatan</p>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Statistik --}}
            <li class="nav-item">
                <a href="#" class="nav-link {{ $activemenu === 'statistik' ? 'active' : '' }}">
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
                            <p> Detail Beban Kerja</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/statistik/hasil') }}" class="nav-link">
                            <i class="fas fa-award nav-icon"></i>
                            <p> Verifikasi Poin Kegiatan </p>
                        </a>
                    </li>
                </ul>
            </li>

            {{-- Surat Tugas --}}
            <li class="nav-item">
                <a href="#" class="nav-link {{ $activemenu === 'surat-tugas' ? 'active' : '' }}">
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