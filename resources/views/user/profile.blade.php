@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profil {{ session('level_nama') }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('profile.edit') }}" class="btn btn-success">
                            <i class="fas fa-edit"></i> EDIT
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            @if($user->foto)
                                <img src="{{ $user->foto }}" class="img-circle elevation-2" alt="Foto Profil" style="width: 200px; height: 200px; object-fit: cover;">
                            @else
                                <img src="{{ asset('adminlte/dist/img/user.jpg') }}" class="img-circle elevation-2" alt="Foto Default" style="width: 200px; height: 200px; object-fit: cover;">
                            @endif
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Username</label>
                                        <p class="form-control-static bg-light p-2 rounded">{{ $user->username }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Nama Lengkap</label>
                                        <p class="form-control-static bg-light p-2 rounded">
                                            {{ $user->gelar_depan ? $user->gelar_depan . ' ' : '' }}
                                            {{ $user->nama_lengkap }}
                                            {{ $user->gelar_belakang ? ', ' . $user->gelar_belakang : '' }}
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">NIDN</label>
                                        <p class="form-control-static bg-light p-2 rounded">{{ $user->nidn }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Jenis Kelamin</label>
                                        <p class="form-control-static bg-light p-2 rounded">
                                            {{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Tempat, Tanggal Lahir</label>
                                        <p class="form-control-static bg-light p-2 rounded">
                                            {{ $user->tempat_lahir }}, {{ date('d F Y', strtotime($user->tanggal_lahir)) }}
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Agama</label>
                                        <p class="form-control-static bg-light p-2 rounded">{{ $user->agama }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Program Studi</label>
                                        <p class="form-control-static bg-light p-2 rounded">{{ $user->program_studi }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Jabatan Fungsional</label>
                                        <p class="form-control-static bg-light p-2 rounded">{{ $user->jabatan_fungsional }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Pendidikan Terakhir</label>
                                        <p class="form-control-static bg-light p-2 rounded">{{ $user->pendidikan_terakhir }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Status Pernikahan</label>
                                        <p class="form-control-static bg-light p-2 rounded">{{ $user->status_nikah }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Status Ikatan Kerja</label>
                                        <p class="form-control-static bg-light p-2 rounded">{{ $user->status_ikatan_kerja }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="font-weight-bold">Email</label>
                                        <p class="form-control-static bg-light p-2 rounded">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="font-weight-bold">Alamat</label>
                                        <p class="form-control-static bg-light p-2 rounded">{{ $user->alamat }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <small>
                        Terakhir diperbarui: {{ $user->updated_at ? date('d F Y H:i', strtotime($user->updated_at)) : '-' }}
                        @if($user->updated_by)
                            oleh {{ $user->updated_by }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .form-control-static {
        min-height: 38px;
        border-radius: 4px;
    }
    .card-tools {
        float: right;
    }
    .img-circle {
        border: 3px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
</style>
@endpush
@endsection