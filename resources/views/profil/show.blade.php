@extends('layouts.template')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Profil Pimpinan</h3>
                    <div class="card-tools">
                        <a href="{{ route('profil.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            @if($pimpinan->foto)
                                <img src="{{ asset('storage/foto_user/'.$pimpinan->foto) }}" 
                                     alt="Foto" class="img-fluid rounded mb-3" style="max-width: 300px;">
                            @else
                                <img src="{{ asset('adminlte/dist/img/user2-160x160.jpg') }}" 
                                     alt="Default" class="img-fluid rounded mb-3" style="max-width: 300px;">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px">Nama Lengkap</th>
                                    <td>
                                        {{ $pimpinan->gelar_depan ? $pimpinan->gelar_depan . ' ' : '' }}
                                        {{ $pimpinan->nama_lengkap }}
                                        {{ $pimpinan->gelar_belakang ? ', ' . $pimpinan->gelar_belakang : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>NIDN</th>
                                    <td>{{ $pimpinan->nidn }}</td>
                                </tr>
                                <tr>
                                    <th>Program Studi</th>
                                    <td>{{ $pimpinan->program_studi }}</td>
                                </tr>
                                <tr>
                                    <th>Jabatan Fungsional</th>
                                    <td>{{ $pimpinan->jabatan_fungsional }}</td>
                                </tr>
                                <tr>
                                    <th>Pendidikan Terakhir</th>
                                    <td>{{ $pimpinan->pendidikan_terakhir }}</td>
                                </tr>
                                <tr>
                                    <th>Asal Perguruan Tinggi</th>
                                    <td>{{ $pimpinan->asal_perguruan_tinggi }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $pimpinan->email }}</td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td>{{ $pimpinan->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                                </tr>
                                <tr>
                                    <th>Status Nikah</th>
                                    <td>{{ $pimpinan->status_nikah }}</td>
                                </tr>
                                <tr>
                                    <th>Status Ikatan Kerja</th>
                                    <td>{{ $pimpinan->status_ikatan_kerja }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $pimpinan->alamat }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection