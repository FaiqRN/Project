@extends('layouts.template')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 text-center">
                    @if(session('foto'))
                        <img src="{{ session('foto') }}" class="img-circle elevation-2" alt="User Image" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <img src="{{ asset('adminlte/dist/img/user.jpg') }}" class="img-circle elevation-2" alt="User Image" style="width: 150px; height: 150px; object-fit: cover;">
                    @endif
                </div>
                <div class="col-md-9">
                    <h4>Selamat Datang,</h4>
                    <h5>
                        {{ session('gelar_depan') ? session('gelar_depan') . ' ' : '' }}
                        {{ session('nama_lengkap') }}
                        {{ session('gelar_belakang') ? ', ' . session('gelar_belakang') : '' }}
                    </h5>
                    <p class="text-muted mb-2">
                        NIDN: {{ session('nidn') }}<br>
                        Program Studi: {{ session('program_studi') }}
                    </p>
                    <a href="{{ route('profile') }}" class="btn btn-primary">
                        <i class="fas fa-user mr-2"></i>Lihat Profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .info-box {
        min-height: 100px;
    }
    .img-circle {
        border: 3px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
</style>
@endpush
@endsection