@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Tambah Data Pimpinan</h3>
                    <div class="card-tools">
                        <a href="{{ route('profil.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('profil.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Level</label>
                                    <select name="level_id" class="form-control @error('level_id') is-invalid @enderror">
                                        <option value="">Pilih Level</option>
                                        @foreach($levels as $level)
                                        <option value="{{ $level->level_id }}">{{ $level->level_nama }}</option>
                                        @endforeach
                                    </select>
                                    @error('level_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label>Username</label>
                                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                                           value="{{ old('username') }}">
                                    @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <!-- Tambahkan field lainnya sesuai migration -->
                            </div>
                            <div class="col-md-6">
                                <!-- Lanjutan field lainnya -->
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection