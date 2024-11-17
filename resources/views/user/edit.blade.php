@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Profil {{ session('level_nama') }}</h3>
                </div>
                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Foto Profil -->
                        <div class="row mb-4">
                            <div class="col-md-12 text-center">
                                @if($user->foto)
                                    <img src="{{ $user->foto }}" id="preview" class="img-circle elevation-2" alt="Foto Profil" style="width: 200px; height: 200px; object-fit: cover; margin-bottom: 1rem;">
                                @else
                                    <img src="{{ asset('adminlte/dist/img/user.jpg') }}" id="preview" class="img-circle elevation-2" alt="Foto Default" style="width: 200px; height: 200px; object-fit: cover; margin-bottom: 1rem;">
                                @endif
                                <div>
                                    <input type="file" name="foto" id="foto" class="d-none" accept="image/*" onchange="previewImage()">
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('foto').click()">
                                        <i class="fas fa-camera"></i> Pilih Foto
                                    </button>
                                    <p class="text-muted mt-2">Format: JPG, JPEG, PNG (Max. 2MB)</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username <span class="text-danger">*</span></label>
                                    <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}">
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Gelar Depan</label>
                                    <input type="text" name="gelar_depan" class="form-control" value="{{ old('gelar_depan', $user->gelar_depan) }}">
                                </div>

                                <div class="form-group">
                                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_lengkap" class="form-control @error('nama_lengkap') is-invalid @enderror" value="{{ old('nama_lengkap', $user->nama_lengkap) }}">
                                    @error('nama_lengkap')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Gelar Belakang</label>
                                    <input type="text" name="gelar_belakang" class="form-control" value="{{ old('gelar_belakang', $user->gelar_belakang) }}">
                                </div>

                                <div class="form-group">
                                    <label>NIDN <span class="text-danger">*</span></label>
                                    <input type="text" name="nidn" class="form-control @error('nidn') is-invalid @enderror" value="{{ old('nidn', $user->nidn) }}">
                                    @error('nidn')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="jenis_kelamin" class="form-control @error('jenis_kelamin') is-invalid @enderror">
                                        <option value="L" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin', $user->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Jabatan Fungsional <span class="text-danger">*</span></label>
                                    <input type="text" name="jabatan_fungsional" class="form-control @error('jabatan_fungsional') is-invalid @enderror" value="{{ old('jabatan_fungsional', $user->jabatan_fungsional) }}">
                                    @error('jabatan_fungsional')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Program Studi <span class="text-danger">*</span></label>
                                    <input type="text" name="program_studi" class="form-control @error('program_studi') is-invalid @enderror" value="{{ old('program_studi', $user->program_studi) }}">
                                    @error('program_studi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Pendidikan Terakhir <span class="text-danger">*</span></label>
                                    <select name="pendidikan_terakhir" class="form-control @error('pendidikan_terakhir') is-invalid @enderror">
                                        @foreach(['S1', 'S2', 'S3', 'Profesor'] as $pendidikan)
                                            <option value="{{ $pendidikan }}" {{ old('pendidikan_terakhir', $user->pendidikan_terakhir) == $pendidikan ? 'selected' : '' }}>
                                                {{ $pendidikan }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('pendidikan_terakhir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Status Pernikahan <span class="text-danger">*</span></label>
                                    <select name="status_nikah" class="form-control @error('status_nikah') is-invalid @enderror">
                                        <option value="Menikah" {{ old('status_nikah', $user->status_nikah) == 'Menikah' ? 'selected' : '' }}>Menikah</option>
                                        <option value="Belum Menikah" {{ old('status_nikah', $user->status_nikah) == 'Belum Menikah' ? 'selected' : '' }}>Belum Menikah</option>
                                        <option value="Cerai" {{ old('status_nikah', $user->status_nikah) == 'Cerai' ? 'selected' : '' }}>Cerai</option>
                                    </select>
                                    @error('status_nikah')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Status Ikatan Kerja <span class="text-danger">*</span></label>
                                    <input type="text" name="status_ikatan_kerja" class="form-control @error('status_ikatan_kerja') is-invalid @enderror" value="{{ old('status_ikatan_kerja', $user->status_ikatan_kerja) }}">
                                    @error('status_ikatan_kerja')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Data Tambahan Full Width -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tempat Lahir <span class="text-danger">*</span></label>
                                    <input type="text" name="tempat_lahir" class="form-control @error('tempat_lahir') is-invalid @enderror" value="{{ old('tempat_lahir', $user->tempat_lahir) }}">
                                    @error('tempat_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" name="tanggal_lahir" class="form-control @error('tanggal_lahir') is-invalid @enderror" value="{{ old('tanggal_lahir', $user->tanggal_lahir) }}">
                                    @error('tanggal_lahir')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Agama <span class="text-danger">*</span></label>
                                    <input type="text" name="agama" class="form-control @error('agama') is-invalid @enderror" value="{{ old('agama', $user->agama) }}">
                                    @error('agama')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Asal Perguruan Tinggi <span class="text-danger">*</span></label>
                                    <input type="text" name="asal_perguruan_tinggi" class="form-control @error('asal_perguruan_tinggi') is-invalid @enderror" value="{{ old('asal_perguruan_tinggi', $user->asal_perguruan_tinggi) }}">
                                    @error('asal_perguruan_tinggi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Alamat <span class="text-danger">*</span></label>
                                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $user->alamat) }}</textarea>
                                    @error('alamat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h3 class="card-title">Ganti Password</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Password Saat Ini</label>
                                        <div class="input-group">
                                            <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" id="current_password">
                                            <div class="input-group-append">
                                                <span class="input-group-text" onclick="togglePassword('current_password')">
                                                    <i class="fas fa-eye" id="current_password_icon"></i>
                                                </span>
                                            </div>
                                        </div>
                                        @error('current_password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Password Baru</label>
                                        <div class="input-group">
                                            <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" id="new_password">
                                            <div class="input-group-append">
                                                <span class="input-group-text" onclick="togglePassword('new_password')">
                                                    <i class="fas fa-eye" id="new_password_icon"></i>
                                                </span>
                                            </div>
                                        </div>
                                        @error('new_password')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Konfirmasi Password Baru</label>
                                        <div class="input-group">
                                            <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation">
                                            <div class="input-group-append">
                                                <span class="input-group-text" onclick="togglePassword('password_confirmation')">
                                                    <i class="fas fa-eye" id="password_confirmation_icon"></i>
                                                </span>
                                            </div>
                                        </div>
                                        @error('password_confirmation')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">
                                * Kosongkan semua field password jika tidak ingin mengubah password
                            </small>
                        </div>
                    </div>
                    
                    @push('js')
                    <script>
                    function togglePassword(fieldId) {
                        const field = document.getElementById(fieldId);
                        const icon = document.getElementById(fieldId + '_icon');
                        
                        if (field.type === 'password') {
                            field.type = 'text';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            field.type = 'password';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    }
                    </script>
                    @endpush
                    
                    @push('css')
                    <style>
                    .input-group-text {
                        cursor: pointer;
                    }
                    .invalid-feedback.d-block {
                        display: block !important;
                    }
                    </style>
                    @endpush
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-1"></i> Simpan
                        </button>
                        <a href="{{ route('profile') }}" class="btn btn-danger">
                            <i class="fas fa-times mr-1"></i> Hapus
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('css')
<style>
    .form-control.is-invalid {
        background-image: none;
    }
    .card-footer {
        background-color: #fff;
    }
    .img-circle {
        border: 3px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
</style>
@endpush

@push('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function previewImage() {
    const foto = document.querySelector('#foto');
    const preview = document.querySelector('#preview');
    
    if (foto.files && foto.files[0]) {
        const reader = new FileReader();
        if (foto.files[0].size > 2 * 1024 * 1024) {
            Swal.fire({
                icon: 'error',
                title: 'Ukuran File Terlalu Besar',
                text: 'Ukuran file tidak boleh lebih dari 2MB',
                confirmButtonText: 'OK'
            });
            foto.value = ''; // Reset input file
            return;
        }
        
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        
        reader.readAsDataURL(foto.files[0]);
    }
}
</script>
@endpush
@endsection