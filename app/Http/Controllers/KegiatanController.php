<?php

namespace App\Http\Controllers;

use App\Models\KegiatanModel;
use App\Models\DokumentasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KegiatanController extends Controller
{
    /**
     * Menampilkan daftar kegiatan
     */
    public function index(Request $request)
    {
        // Mengambil parameter pencarian
        $cari = $request->input('cari');
        
        // Query dasar untuk kegiatan
        $query = KegiatanModel::select([
            'kegiatan_id',
            'nama_kegiatan',
            'tanggal_mulai',
            'tanggal_selesai',
            'status',
            'file_surat'
        ]);

        // Jika ada parameter pencarian
        if ($cari) {
            $query->where('nama_kegiatan', 'like', "%{$cari}%");
        }

        // Mengambil data kegiatan dan mengurutkan berdasarkan tanggal terbaru
        $kegiatans = $query->orderBy('tanggal_mulai', 'desc')->paginate(10);

        // Menghitung status penugasan
        foreach ($kegiatans as $kegiatan) {
            $kegiatan->status_penugasan = $this->hitungStatusPenugasan($kegiatan);
        }

        return view('kegiatan.index', compact('kegiatans', 'cari'));
    }

    /**
     * Menghitung status penugasan berdasarkan tanggal
     */
    private function hitungStatusPenugasan($kegiatan)
    {
        $today = now();
        $tanggalMulai = \Carbon\Carbon::parse($kegiatan->tanggal_mulai);
        $tanggalSelesai = \Carbon\Carbon::parse($kegiatan->tanggal_selesai);

        if ($today < $tanggalMulai) {
            return 'Terjadwal';
        } elseif ($today >= $tanggalMulai && $today <= $tanggalSelesai) {
            return 'Sedang Berlangsung';
        } elseif ($kegiatan->status === 'selesai') {
            return 'Selesai';
        } else {
            return 'Draft';
        }
    }

    /**
     * Mengunduh berkas kegiatan
     */
    public function unduhBerkas($id)
    {
        $kegiatan = KegiatanModel::findOrFail($id);
        
        // Memeriksa apakah file ada
        if ($kegiatan->file_surat && Storage::exists($kegiatan->file_surat)) {
            return Storage::download($kegiatan->file_surat);
        }

        return back()->with('error', 'Berkas tidak ditemukan');
    }

    /**
     * Menampilkan form tambah kegiatan
     */
    public function create()
    {
        return view('kegiatan.create');
    }

    /**
     * Menyimpan kegiatan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:200',
            'deskripsi_kegiatan' => 'required',
            'tempat_kegiatan' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'bobot' => 'required|in:ringan,sedang,berat',
            'nama_kelompok' => 'required|string|max:50',
            'file_surat' => 'required|file|mimes:pdf,doc,docx|max:2048'
        ]);

        // Upload file
        if ($request->hasFile('file_surat')) {
            $path = $request->file('file_surat')->store('public/surat');
            $validated['file_surat'] = $path;
        }

        KegiatanModel::create($validated);

        return redirect()->route('kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan');
    }

    /**
     * Menampilkan detail kegiatan
     */
    public function show($id)
    {
        $kegiatan = KegiatanModel::with(['user', 'surat', 'agendas'])->findOrFail($id);
        return view('kegiatan.show', compact('kegiatan'));
    }

    /**
     * Menampilkan form edit kegiatan
     */
    public function edit($id)
    {
        $kegiatan = KegiatanModel::findOrFail($id);
        return view('kegiatan.edit', compact('kegiatan'));
    }

    /**
     * Mengupdate kegiatan
     */
    public function update(Request $request, $id)
    {
        $kegiatan = KegiatanModel::findOrFail($id);
        
        $validated = $request->validate([
            'nama_kegiatan' => 'required|string|max:200',
            'deskripsi_kegiatan' => 'required',
            'tempat_kegiatan' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'bobot' => 'required|in:ringan,sedang,berat',
            'nama_kelompok' => 'required|string|max:50',
            'file_surat' => 'nullable|file|mimes:pdf,doc,docx|max:2048'
        ]);

        // Upload file baru jika ada
        if ($request->hasFile('file_surat')) {
            // Hapus file lama
            if ($kegiatan->file_surat) {
                Storage::delete($kegiatan->file_surat);
            }
            
            $path = $request->file('file_surat')->store('public/surat');
            $validated['file_surat'] = $path;
        }

        $kegiatan->update($validated);

        return redirect()->route('kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui');
    }

    /**
     * Menghapus kegiatan
     */
    public function destroy($id)
    {
        $kegiatan = KegiatanModel::findOrFail($id);
        
        // Hapus file terkait
        if ($kegiatan->file_surat) {
            Storage::delete($kegiatan->file_surat);
        }

        $kegiatan->delete();

        return redirect()->route('kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus');
    }
}