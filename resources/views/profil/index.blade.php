@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profil Singkat Pimpinan</h3>
                </div>
                <div class="card-body">
                    <!-- Deskripsi -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <p>
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do
                                eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
                                quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                            </p>
                        </div>
                    </div>

                    @if($data_pimpinan)
                        <!-- Foto dan Nama -->
                        <div class="row mb-4">
                            <div class="col-12 text-center">
                                <div class="mb-3">
                                    @if($data_pimpinan->foto && Storage::exists('public/foto_user/' . $data_pimpinan->foto))
                                        <img src="{{ asset('storage/foto_user/' . $data_pimpinan->foto) }}" 
                                             alt="Foto {{ $data_pimpinan->nama_lengkap }}"
                                             class="img-circle elevation-2" 
                                             style="width: 200px; height: 200px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('adminlte/dist/img/user2-160x160.jpg') }}" 
                                             alt="Default Profile"
                                             class="img-circle elevation-2"
                                             style="width: 200px; height: 200px; object-fit: cover;">
                                    @endif
                                </div>
                                <h4>
                                    {{ $data_pimpinan->gelar_depan ? $data_pimpinan->gelar_depan . ' ' : '' }}
                                    {{ $data_pimpinan->nama_lengkap }}
                                    {{ $data_pimpinan->gelar_belakang ? ', ' . $data_pimpinan->gelar_belakang : '' }}
                                </h4>
                            </div>
                        </div>

                        <!-- Informasi Profil -->
                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6">
                                <!-- Nama -->
                                <div class="form-group border-bottom pb-2">
                                    <label class="font-weight-bold">Nama</label>
                                    <p class="mb-0">{{ $data_pimpinan->nama_lengkap }}</p>
                                </div>

                                <!-- Unit Kerja -->
                                <div class="form-group border-bottom pb-2">
                                    <label class="font-weight-bold">Unit Kerja</label>
                                    <p class="mb-0">{{ $data_pimpinan->program_studi }}</p>
                                </div>

                                <!-- Alamat Unit Kerja -->
                                <div class="form-group border-bottom pb-2">
                                    <label class="font-weight-bold">Alamat Unit Kerja</label>
                                    <p class="mb-0">{{ $data_pimpinan->alamat }}</p>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                                <!-- Pendidikan -->
                                <div class="form-group border-bottom pb-2">
                                    <label class="font-weight-bold">Pendidikan</label>
                                    <p class="mb-0">
                                        {{ $data_pimpinan->pendidikan_terakhir }} - {{ $data_pimpinan->asal_perguruan_tinggi }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            Data pimpinan belum tersedia.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('css')
<style>
.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #666;
}
.form-group p {
    color: #2679DA;
}
.border-bottom {
    border-color: #dee2e6!important;
}
</style>
@endpush