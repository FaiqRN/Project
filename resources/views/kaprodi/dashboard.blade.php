@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <!-- Info boxes -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-tasks"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Kegiatan</span>
                    <span class="info-box-number">10</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Kegiatan Selesai</span>
                    <span class="info-box-number">5</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Kegiatan Berlangsung</span>
                    <span class="info-box-number">5</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            @if(session('foto'))
                                <img src="{{ asset('storage/'.session('foto')) }}" class="img-circle elevation-2" alt="User Image" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <img src="{{ asset('adminlte/dist/img/user.jpg') }}" class="img-circle elevation-2" alt="User Image" style="width: 100px; height: 100px; object-fit: cover;">
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h4>Selamat Datang, 
                                @if(session('gelar_depan'))
                                    {{ session('gelar_depan') }}
                                @endif
                                {{ session('nama_lengkap') }}
                                @if(session('gelar_belakang'))
                                    , {{ session('gelar_belakang') }}
                                @endif
                            </h4>
                            <p class="mb-2">NIDN: {{ session('nidn') }}</p>
                            <p class="mb-2">Program Studi: {{ session('program_studi') }}</p>
                            <div class="mt-3">
                                <a href="{{ route('profile') }}" class="btn btn-primary">
                                    <i class="fas fa-user mr-2"></i>Lihat Profil
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kegiatan Terbaru</h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Timeline items here -->
                        <div>
                            <i class="fas fa-clock bg-info"></i>
                            <div class="timeline-item">
                                <span class="time"><i class="fas fa-clock"></i> 2 hari yang lalu</span>
                                <h3 class="timeline-header">Kegiatan Terakhir</h3>
                                <div class="timeline-body">
                                    Belum ada kegiatan yang tercatat.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .info-box {
        min-height: 100px;
        background: #ffffff;
        width: 100%;
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        border-radius: 0.25rem;
        margin-bottom: 20px;
        display: flex;
        position: relative;
    }

    .timeline {
        margin: 0;
        padding: 0;
        position: relative;
    }

    .timeline:before {
        background: #dee2e6;
        bottom: 0;
        content: "";
        left: 31px;
        margin: 0;
        position: absolute;
        top: 0;
        width: 4px;
    }

    .timeline > div {
        margin-bottom: 15px;
        margin-right: 10px;
        position: relative;
    }

    .timeline > div > .fa,
    .timeline > div > .fas,
    .timeline > div > .far {
        background: #adb5bd;
        border-radius: 50%;
        font-size: 15px;
        height: 30px;
        left: 18px;
        line-height: 30px;
        position: absolute;
        text-align: center;
        top: 0;
        width: 30px;
        color: #fff;
    }

    .timeline-item {
        background: #fff;
        border-radius: 3px;
        margin-left: 60px;
        margin-right: 15px;
        margin-top: 0;
        padding: 0;
        position: relative;
    }

    .timeline-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
        color: #495057;
        font-size: 16px;
        line-height: 1.1;
        margin: 0;
        padding: 10px;
    }

    .timeline-body {
        padding: 10px;
    }

    .time {
        color: #999;
        float: right;
        padding: 10px;
    }
</style>
@endpush
@endsection