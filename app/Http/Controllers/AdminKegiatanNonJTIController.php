<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KegiatanLuarInstitusiModel;
use App\Models\KegiatanInstitusiModel;
use App\Models\SuratModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AdminKegiatanNonJTIController extends Controller
{
    public function index()
    {
        return view('admin.dosen.kegiatan-non-jti', [
            'breadcrumb' => (object)[
                'title' => 'Kegiatan Non-JTI',
                'list' => ['Home', 'Dosen', 'Kegiatan Non-JTI']
            ],
            'activemenu' => 'kegiatan-non-jti'
        ]);
    }

    public function getKegiatanList(Request $request)
    {
        try {
            // Ambil parameter filter
            $status = $request->get('status');
            $tanggal = $request->get('tanggal');
            $search = $request->get('search');
    
            // Query dasar
            $kegiatanLuar = KegiatanLuarInstitusiModel::with(['user', 'surat']);
            $kegiatanInstitusi = KegiatanInstitusiModel::with(['user', 'surat']);
    
            // Terapkan filter status
            if ($status) {
                $kegiatanLuar->where('status_persetujuan', $status);
                $kegiatanInstitusi->where('status_persetujuan', $status);
            }
    
            // Terapkan filter tanggal
            if ($tanggal) {
                $kegiatanLuar->where(function($query) use ($tanggal) {
                    $query->whereDate('tanggal_mulai', '<=', $tanggal)
                          ->whereDate('tanggal_selesai', '>=', $tanggal);
                });
                $kegiatanInstitusi->where(function($query) use ($tanggal) {
                    $query->whereDate('tanggal_mulai', '<=', $tanggal)
                          ->whereDate('tanggal_selesai', '>=', $tanggal);
                });
            }
    
            // Terapkan pencarian
            if ($search) {
                $kegiatanLuar->where(function($query) use ($search) {
                    $query->where('nama_kegiatan_luar_institusi', 'like', "%{$search}%")
                          ->orWhere('penyelenggara', 'like', "%{$search}%")
                          ->orWhere('lokasi_kegiatan', 'like', "%{$search}%")
                          ->orWhereHas('user', function($q) use ($search) {
                              $q->where('nama_lengkap', 'like', "%{$search}%");
                          });
                });
                $kegiatanInstitusi->where(function($query) use ($search) {
                    $query->where('nama_kegiatan_institusi', 'like', "%{$search}%")
                          ->orWhere('penyelenggara', 'like', "%{$search}%")
                          ->orWhere('lokasi_kegiatan', 'like', "%{$search}%")
                          ->orWhereHas('user', function($q) use ($search) {
                              $q->where('nama_lengkap', 'like', "%{$search}%");
                          });
                });
            }
    
            // Ambil data
            $kegiatanLuar = $kegiatanLuar->get();
            $kegiatanInstitusi = $kegiatanInstitusi->get();
    
            // Gabungkan dan format data
            $kegiatan = [];
    
            foreach ($kegiatanLuar as $k) {
                $kegiatan[] = [
                    'id' => $k->kegiatan_luar_institusi_id,
                    'jenis_kegiatan' => 'luar_institusi',
                    'nama' => $k->nama_kegiatan_luar_institusi,
                    'user_name' => $k->user ? $k->user->nama_lengkap : '-',
                    'penyelenggara' => $k->penyelenggara,
                    'lokasi' => $k->lokasi_kegiatan,
                    'tanggal_mulai' => $k->tanggal_mulai,
                    'tanggal_selesai' => $k->tanggal_selesai,
                    'status_persetujuan' => $k->status_persetujuan,
                    'keterangan' => $k->keterangan
                ];
            }
    
            foreach ($kegiatanInstitusi as $k) {
                $kegiatan[] = [
                    'id' => $k->kegiatan_institusi_id,
                    'jenis_kegiatan' => 'institusi',
                    'nama' => $k->nama_kegiatan_institusi,
                    'user_name' => $k->user ? $k->user->nama_lengkap : '-',
                    'penyelenggara' => $k->penyelenggara,
                    'lokasi' => $k->lokasi_kegiatan,
                    'tanggal_mulai' => $k->tanggal_mulai,
                    'tanggal_selesai' => $k->tanggal_selesai,
                    'status_persetujuan' => $k->status_persetujuan,
                    'keterangan' => $k->keterangan
                ];
            }
    
            return response()->json([
                'success' => true,
                'data' => $kegiatan
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error in getKegiatanList: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat data'
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            // Validasi request tetap sama
            $validated = $request->validate([
                'jenis_kegiatan' => 'required|in:institusi,luar_institusi',
                'status' => 'required|in:disetujui,ditolak',
                'keterangan' => 'required_if:status,ditolak|string|max:255|nullable'
            ], [
                'jenis_kegiatan.required' => 'Jenis kegiatan harus diisi',
                'jenis_kegiatan.in' => 'Jenis kegiatan tidak valid',
                'status.required' => 'Status persetujuan harus diisi',
                'status.in' => 'Status persetujuan tidak valid',
                'keterangan.required_if' => 'Keterangan wajib diisi jika status ditolak',
                'keterangan.max' => 'Keterangan tidak boleh lebih dari 255 karakter'
            ]);
    
            // Tentukan model dan ambil data kegiatan
            $modelClass = $validated['jenis_kegiatan'] === 'institusi' 
                ? KegiatanInstitusiModel::class 
                : KegiatanLuarInstitusiModel::class;
    
            $kegiatan = $modelClass::with('surat')->findOrFail($id);
            
            if ($validated['status'] === 'ditolak') {
                if ($kegiatan->surat) {
                    try {
                        // Simpan referensi ke surat
                        $surat = $kegiatan->surat;
                        $filePath = $surat->file_surat;
    
                        // Hapus file fisik jika ada
                        if ($filePath && Storage::disk('public')->exists($filePath)) {
                            Storage::disk('public')->delete($filePath);
                        }
    
                        // Hapus relasi terlebih dahulu
                        $kegiatan->surat()->dissociate();
                        $kegiatan->save();
    
                        // Setelah itu baru hapus record surat
                        $surat->delete();
                    } catch (\Exception $e) {
                        DB::rollback();
                        Log::error('Error saat menghapus surat: ' . $e->getMessage());
                        throw new \Exception('Gagal menghapus file surat: ' . $e->getMessage());
                    }
                }
                
                // Update status dan keterangan kegiatan
                $kegiatan->status_persetujuan = $validated['status'];
                $kegiatan->keterangan = $validated['keterangan'];
            } else {
                // Jika disetujui
                $kegiatan->status_persetujuan = $validated['status'];
                $kegiatan->keterangan = null;
            }
    
            // Simpan perubahan kegiatan
            $kegiatan->save();
            
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => $validated['status'] === 'disetujui' 
                    ? 'Kegiatan berhasil disetujui' 
                    : 'Kegiatan berhasil ditolak',
                'data' => [
                    'status' => $kegiatan->status_persetujuan,
                    'keterangan' => $kegiatan->keterangan
                ]
            ]);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in updateStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadSurat($id, $jenis)
    {
        try {
            // Tentukan model berdasarkan jenis kegiatan
            $model = $jenis === 'institusi' 
                ? KegiatanInstitusiModel::with('surat')
                : KegiatanLuarInstitusiModel::with('surat');

            // Cari kegiatan
            $kegiatan = $model->findOrFail($id);
            
            // Validasi keberadaan surat
            if (!$kegiatan->surat || !$kegiatan->surat->file_surat) {
                return response()->json([
                    'success' => false,
                    'message' => 'File surat tidak ditemukan'
                ], 404);
            }

            // Dapatkan path file
            $filePath = storage_path('app/public/' . $kegiatan->surat->file_surat);
            
            // Validasi keberadaan file fisik
            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File surat tidak ditemukan di sistem'
                ], 404);
            }

            // Return file untuk didownload
            return response()->download($filePath);

        } catch (\Exception $e) {
            Log::error('Error in downloadSurat: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengunduh surat'
            ], 500);
        }
    }

    public function getDetail($id, Request $request)
    {
        try {
            $model = $request->query('jenis_kegiatan') === 'institusi' 
                ? KegiatanInstitusiModel::with(['user', 'surat'])
                : KegiatanLuarInstitusiModel::with(['user', 'surat']);
    
            $kegiatan = $model->findOrFail($id);
            
            $jenisKegiatan = $request->query('jenis_kegiatan') === 'institusi' 
                ? 'Kegiatan Institusi' 
                : 'Kegiatan Luar Institusi';
    
            $data = [
                'id' => $id,
                'jenis_kegiatan' => $jenisKegiatan,
                'nama' => $request->query('jenis_kegiatan') === 'institusi' 
                    ? $kegiatan->nama_kegiatan_institusi 
                    : $kegiatan->nama_kegiatan_luar_institusi,
                'penyelenggara' => $kegiatan->penyelenggara,
                'lokasi' => $kegiatan->lokasi_kegiatan,
                'deskripsi' => $kegiatan->deskripsi_kegiatan,
                'tanggal_mulai' => $kegiatan->tanggal_mulai,
                'tanggal_selesai' => $kegiatan->tanggal_selesai,
                'surat' => $kegiatan->surat ? [
                    'id' => $kegiatan->surat->surat_id,
                    'judul' => $kegiatan->surat->judul_surat,
                    'nomor' => $kegiatan->surat->nomer_surat,
                    'tanggal' => $kegiatan->surat->tanggal_surat,
                    'file' => $kegiatan->surat->file_surat
                ] : null
            ];
    
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error in getDetail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat detail kegiatan'
            ], 500);
        }
    }

    public function destroy($id, Request $request)
    {
        DB::beginTransaction();
        try {
            // Validasi jenis kegiatan
            $request->validate([
                'jenis_kegiatan' => 'required|in:institusi,luar_institusi'
            ]);

            // Tentukan model berdasarkan jenis kegiatan
            $model = $request->jenis_kegiatan === 'institusi' 
                ? KegiatanInstitusiModel::class 
                : KegiatanLuarInstitusiModel::class;

            // Ambil data kegiatan beserta surat
            $kegiatan = $model::with('surat')->findOrFail($id);

            // Hapus file surat jika ada
            if ($kegiatan->surat) {
                // Hapus file fisik
                if (Storage::disk('public')->exists($kegiatan->surat->file_surat)) {
                    Storage::disk('public')->delete($kegiatan->surat->file_surat);
                }
                
                // Hapus record surat
                $kegiatan->surat->delete();
            }

            // Hapus kegiatan
            $kegiatan->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus kegiatan'
            ], 500);
        }
    }
}