@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold mb-6">Tambah Kegiatan Baru</h1>

        <form action="{{ route('kegiatan.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Kegiatan</label>
                    <input 
                        type="text" 
                        name="nama_kegiatan" 
                        class="w-full p-2 border rounded"
                        value="{{ old('nama_kegiatan') }}"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Deskripsi Kegiatan</label>
                    <textarea 
                        name="deskripsi_kegiatan" 
                        class="w-full p-2 border rounded"
                        rows="4"
                        required
                    >{{ old('deskripsi_kegiatan') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Tempat Kegiatan</label>
                    <input 
                        type="text" 
                        name="tempat_kegiatan" 
                        class="w-full p-2 border rounded"
                        value="{{ old('tempat_kegiatan') }}"
                        required
                    >
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal Mulai</label>
                        <input 
                            type="date" 
                            name="tanggal_mulai" 
                            class="w-full p-2 border rounded"
                            value="{{ old('tanggal_mulai') }}"
                            required
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal Selesai</label>
                        <input 
                            type="date" 
                            name="tanggal_selesai" 
                            class="w-full p-2 border rounded"
                            value="{{ old('tanggal_selesai') }}"
                            required
                        >
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Bobot</label>
                    <select name="bobot" class="w-full p-2 border rounded" required>
                        <option value="">Pilih Bobot</option>
                        <option value="ringan" {{ old('bobot') == 'ringan' ? 'selected' : '' }}>Ringan</option>
                        <option value="sedang" {{ old('bobot') == 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="berat" {{ old('bobot') == 'berat' ? 'selected' : '' }}>Berat</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Nama Kelompok</label>
                    <input 
                        type="text" 
                        name="nama_kelompok" 
                        class="w-full p-2 border rounded"
                        value="{{ old('nama_kelompok') }}"
                        required
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">File Surat</label>
                    <input 
                        type="file" 
                        name="file_surat" 
                        class="w-full p-2 border rounded"
                        accept=".pdf,.doc,.docx"
                        required
                    >
                    <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, DOCX (Max: 2MB)</p>
                </div>

                <div class="flex justify-end space-x-2">
                    <a 
                        href="{{ route('kegiatan.index') }}" 
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                    >
                        Batal
                    </a>
                    <button 
                        type="submit" 
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection