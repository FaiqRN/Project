<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KegiatanLuarInstitusiModel;
use App\Models\KegiatanInstitusiModel;
use App\Models\SuratModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class KegiatanNonJTIController extends Controller
{
    public function index()
    {
        return view('dosen.kegiatan-non-jti', [
            'breadcrumb' => (object)[
                'title' => 'Kegiatan Non-JTI',
                'list' => ['Home', 'Dosen', 'Kegiatan Non-JTI']
            ]
        ]);
    }

    public function getKegiatanList()
    {
        $userId = session('user_id');
        $userLevel = session('level_nama');

        // Ambil data kegiatan luar institusi
        $kegiatanLuar = KegiatanLuarInstitusiModel::with(['user'])
            ->where('user_id', $userId)
            ->get();

        // Ambil data kegiatan institusi
        $kegiatanInstitusi = KegiatanInstitusiModel::with(['user'])
            ->where('user_id', $userId)
            ->get();

        // Gabungkan dan format data
        $kegiatan = [];
        
        foreach ($kegiatanLuar as $k) {
            $kegiatan[] = [
                'id' => $k->kegiatan_luar_institusi_id,
                'jenis' => 'Luar Institusi',
                'nama' => $k->nama_kegiatan_luar_institusi,
                'penyelenggara' => $k->penyelenggara,
                'lokasi' => $k->lokasi_kegiatan,
                'tanggal_mulai' => $k->tanggal_mulai,
                'tanggal_selesai' => $k->tanggal_selesai,
                'status_persetujuan' => $k->status_persetujuan,
                'status' => $k->status_kegiatan
            ];
        }

        foreach ($kegiatanInstitusi as $k) {
            $kegiatan[] = [
                'id' => $k->kegiatan_institusi_id,
                'jenis' => 'Institusi',
                'nama' => $k->nama_kegiatan_institusi,
                'penyelenggara' => $k->penyelenggara,
                'lokasi' => $k->lokasi_kegiatan,
                'tanggal_mulai' => $k->tanggal_mulai,
                'tanggal_selesai' => $k->tanggal_selesai,
                'status_persetujuan' => $k->status_persetujuan,
                'status' => $k->status_kegiatan
            ];
        }

        return response()->json(['data' => $kegiatan]);
    }

// Modifikasi KegiatanNonJTIController.php
public function store(Request $request)
{
    $request->validate([
        'jenis_kegiatan' => 'required|in:institusi,luar_institusi',
        'nama_kegiatan' => 'required|string|max:200',
        'penyelenggara' => 'required|string|max:150',
        'deskripsi_kegiatan' => 'required',
        'lokasi_kegiatan' => 'required',
        'tanggal_mulai' => 'required|date',
        'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
        'judul_surat' => 'required|string|max:200',
        'nomer_surat' => 'required|string|max:100',
        'file_surat' => 'required|mimes:pdf|max:2048',
        'tanggal_surat' => 'required|date'
    ]);

    try {
        DB::beginTransaction();

        // Upload dan simpan surat
        $surat = new SuratModel();
        $surat->judul_surat = $request->judul_surat;
        $surat->nomer_surat = $request->nomer_surat;
        $surat->tanggal_surat = $request->tanggal_surat;
        
        if ($request->hasFile('file_surat')) {
            $file = $request->file('file_surat');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('public/surat-tugas', $fileName);
            $surat->file_surat = $filePath;
        }
        
        $surat->save();

        // Buat kegiatan berdasarkan jenis yang dipilih
        if ($request->jenis_kegiatan == 'luar_institusi') {
            $kegiatan = new KegiatanLuarInstitusiModel();
            $kegiatan->nama_kegiatan_luar_institusi = $request->nama_kegiatan;
        } else {
            $kegiatan = new KegiatanInstitusiModel();
            $kegiatan->nama_kegiatan_institusi = $request->nama_kegiatan;
        }

        // Set atribut umum
        $kegiatan->user_id = session('user_id');
        $kegiatan->surat_id = $surat->surat_id;
        $kegiatan->penyelenggara = $request->penyelenggara;
        $kegiatan->deskripsi_kegiatan = $request->deskripsi_kegiatan;
        $kegiatan->lokasi_kegiatan = $request->lokasi_kegiatan;
        $kegiatan->tanggal_mulai = $request->tanggal_mulai;
        $kegiatan->tanggal_selesai = $request->tanggal_selesai;
        $kegiatan->status_persetujuan = 'pending';
        $kegiatan->status_kegiatan = 'berlangsung';

        $kegiatan->save();

        DB::commit();
        return response()->json([
            'message' => 'Kegiatan berhasil ditambahkan',
            'jenis' => $request->jenis_kegiatan
        ], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error in store:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

// Tambahkan method untuk download surat
public function downloadSurat($id)
{
    $kegiatan = KegiatanLuarInstitusiModel::with('surat')->find($id);
    if (!$kegiatan || !$kegiatan->surat) {
        return response()->json(['message' => 'File tidak ditemukan'], 404);
    }

    $filePath = storage_path('app/' . $kegiatan->surat->file_surat);
    if (!file_exists($filePath)) {
        return response()->json(['message' => 'File tidak ditemukan'], 404);
    }

    return response()->download($filePath);
}
}