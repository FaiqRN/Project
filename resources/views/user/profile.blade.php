@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profil {{ $user->level->level_nama }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-4">
                            @if($user->foto)
                                <img src="{{ asset('storage/'.$user->foto) }}" class="img-circle elevation-2" alt="Foto Profil" style="width: 200px; height: 200px; object-fit: cover;">
                            @else
                                <img src="{{ asset('adminlte/dist/img/user.jpg') }}" class="img-circle elevation-2" alt="Foto Default" style="width: 200px; height: 200px; object-fit: cover;">
                            @endif
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Username</label>
                                        <p class="form-control">{{ $user->username }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Nama Lengkap</label>
                                        <p class="form-control">
                                            {{ $user->gelar_depan ? $user->gelar_depan . ' ' : '' }}
                                            {{ $user->nama_lengkap }}
                                            {{ $user->gelar_belakang ? ', ' . $user->gelar_belakang : '' }}
                                        </p>
                                    </div>
                                    <div class="form-group">
                                        <label>NIDN</label>
                                        <p class="form-control">{{ $user->nidn }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Jenis Kelamin</label>
                                        <p class="form-control">{{ $user->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Tempat, Tanggal Lahir</label>
                                        <p class="form-control">{{ $user->tempat_lahir }}, {{ date('d/m/Y', strtotime($user->tanggal_lahir)) }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Agama</label>
                                        <p class="form-control">{{ $user->agama }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Status Pernikahan</label>
                                        <p class="form-control">{{ $user->status_nikah }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Alamat</label>
                                        <p class="form-control">{{ $user->alamat }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Program Studi</label>
                                        <p class="form-control">{{ $user->program_studi }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Jabatan Fungsional</label>
                                        <p class="form-control">{{ $user->jabatan_fungsional }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Pendidikan Terakhir</label>
                                        <p class="form-control">{{ $user->pendidikan_terakhir }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Asal Perguruan Tinggi</label>
                                        <p class="form-control">{{ $user->asal_perguruan_tinggi }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Status Ikatan Kerja</label>
                                        <p class="form-control">{{ $user->status_ikatan_kerja }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <p class="form-control">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <small class="text-muted">
                        @if($user->created_by && $user->created_at)
                            Dibuat oleh: {{ $user->created_by }} pada {{ \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') }}
                        @endif
                        @if($user->updated_by && $user->updated_at)
                            <br>
                            Terakhir diperbarui oleh: {{ $user->updated_by }} pada {{ \Carbon\Carbon::parse($user->updated_at)->format('d/m/Y H:i') }}
                        @endif
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .form-control {
        background-color: #f8f9fa;
    }
    .form-group label {
        font-weight: bold;
        color: #666;
    }
</style>
@endpush
@endsection