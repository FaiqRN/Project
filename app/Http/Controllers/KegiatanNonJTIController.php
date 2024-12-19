<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\KegiatanLuarInstitusiModel;
use App\Models\KegiatanInstitusiModel;
use App\Models\SuratModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Models\UserModel;

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


    public function getKegiatanList(Request $request)
    {
        try {
            $status = $request->get('status');
            $tanggal = $request->get('tanggal');
            $search = $request->get('search');
            $loggedInUserId = session('user_id');
    
            // Query base untuk kegiatan luar institusi
            $kegiatanLuar = KegiatanLuarInstitusiModel::with(['user', 'surat']);
            $kegiatanInstitusi = KegiatanInstitusiModel::with(['user', 'surat']);
    
            // Filter status
            if ($status) {
                $kegiatanLuar->where('status_persetujuan', $status);
                $kegiatanInstitusi->where('status_persetujuan', $status);
            }
    
            // Filter tanggal
            if ($tanggal) {
                $kegiatanLuar->whereDate('tanggal_mulai', '<=', $tanggal)
                            ->whereDate('tanggal_selesai', '>=', $tanggal);
                $kegiatanInstitusi->whereDate('tanggal_mulai', '<=', $tanggal)
                               ->whereDate('tanggal_selesai', '>=', $tanggal);
            }
    
            // Filter pencarian
            if ($search) {
                $kegiatanLuar->where(function($query) use ($search) {
                    $query->where('nama_kegiatan_luar_institusi', 'like', "%{$search}%")
                          ->orWhere('penyelenggara', 'like', "%{$search}%")
                          ->orWhere('lokasi_kegiatan', 'like', "%{$search}%");
                });
                
                $kegiatanInstitusi->where(function($query) use ($search) {
                    $query->where('nama_kegiatan_institusi', 'like', "%{$search}%")
                          ->orWhere('penyelenggara', 'like', "%{$search}%")
                          ->orWhere('lokasi_kegiatan', 'like', "%{$search}%");
                });
            }
    
            // Eksekusi query
            $kegiatanLuar = $kegiatanLuar->orderBy('created_at', 'desc')->get();
            $kegiatanInstitusi = $kegiatanInstitusi->orderBy('created_at', 'desc')->get();
    
            $kegiatan = [];
    
            // Format kegiatan luar institusi
            foreach ($kegiatanLuar as $k) {
                $kegiatan[] = [
                    'id' => $k->kegiatan_luar_institusi_id,
                    'jenis_kegiatan' => 'luar_institusi',
                    'nama' => $k->nama_kegiatan_luar_institusi,
                    'penyelenggara' => $k->penyelenggara,
                    'lokasi' => $k->lokasi_kegiatan,
                    'tanggal_mulai' => $k->tanggal_mulai,
                    'tanggal_selesai' => $k->tanggal_selesai,
                    'status_persetujuan' => $k->status_persetujuan,
                    'status_kegiatan' => $k->status_kegiatan,
                    'created_by' => $k->created_by,
                    'can_delete' => $k->created_by == $loggedInUserId
                ];
            }
    
            // Format kegiatan institusi
            foreach ($kegiatanInstitusi as $k) {
                $kegiatan[] = [
                    'id' => $k->kegiatan_institusi_id,
                    'jenis_kegiatan' => 'institusi',
                    'nama' => $k->nama_kegiatan_institusi,
                    'penyelenggara' => $k->penyelenggara,
                    'lokasi' => $k->lokasi_kegiatan,
                    'tanggal_mulai' => $k->tanggal_mulai,
                    'tanggal_selesai' => $k->tanggal_selesai,
                    'status_persetujuan' => $k->status_persetujuan,
                    'status_kegiatan' => $k->status_kegiatan,
                    'created_by' => $k->created_by,
                    'can_delete' => $k->created_by == $loggedInUserId
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
   
// Modifikasi KegiatanNonJTIController.php
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // Validasi request
            $request->validate([
                'jenis_kegiatan' => 'required|in:institusi,luar_institusi',
                'user_id' => 'required|exists:m_user,user_id',
                'nama_kegiatan' => 'required|string|max:255',
                'penyelenggara' => 'required|string|max:255',
                'lokasi_kegiatan' => 'required|string|max:255',
                'deskripsi_kegiatan' => 'required|string',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'judul_surat' => 'required|string|max:255',
                'nomer_surat' => 'required|string|max:100',
                'tanggal_surat' => 'required|date',
                'file_surat' => 'required|file|mimes:pdf|max:2048'
            ]);


            // Upload file surat
            if ($request->hasFile('file_surat')) {
                $file = $request->file('file_surat');
                $fileName = $file->getClientOriginalName();
                $filePath = $file->storeAs('surat-tugas', $fileName, 'public');
            } else {
                throw new \Exception('File surat tidak ditemukan');
            }


        // Simpan data surat
        $surat = new SuratModel();
        $surat->judul_surat = $request->judul_surat;
        $surat->nomer_surat = $request->nomer_surat;
        $surat->tanggal_surat = $request->tanggal_surat;
        $surat->file_surat = $filePath;
        if (!$surat->save()) {
            throw new \Exception('Gagal menyimpan data surat');
        }

        // Simpan data kegiatan berdasarkan jenisnya
        if ($request->jenis_kegiatan === 'luar_institusi') {
            $kegiatan = new KegiatanLuarInstitusiModel();
            $kegiatan->nama_kegiatan_luar_institusi = $request->nama_kegiatan;
        } else {
            $kegiatan = new KegiatanInstitusiModel();
            $kegiatan->nama_kegiatan_institusi = $request->nama_kegiatan;
        }


            // Set properti umum
            $kegiatan->user_id = $request->user_id; // User yang dipilih sebagai penanggung jawab
            $kegiatan->created_by = session('user_id'); // User yang membuat kegiatan
            $kegiatan->surat_id = $surat->surat_id;
            $kegiatan->penyelenggara = $request->penyelenggara;
            $kegiatan->lokasi_kegiatan = $request->lokasi_kegiatan;
            $kegiatan->deskripsi_kegiatan = $request->deskripsi_kegiatan;
            $kegiatan->tanggal_mulai = $request->tanggal_mulai;
            $kegiatan->tanggal_selesai = $request->tanggal_selesai;
            $kegiatan->status_persetujuan = 'pending';
            $kegiatan->status_kegiatan = 'berlangsung';


            if (!$kegiatan->save()) {
                throw new \Exception('Gagal menyimpan data kegiatan');
            }


            DB::commit();


            return response()->json([
                'success' => true,
                'message' => 'Data kegiatan berhasil disimpan'
            ]);


        } catch (\Exception $e) {
            DB::rollBack();
            // Hapus file yang telah diupload jika ada error
            if (isset($filePath) && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }
           
            Log::error('Error in store: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data kegiatan: ' . $e->getMessage()
            ], 500);
        }
    }


// Tambahkan method untuk download surat
    public function downloadSurat($id)
    {
        try {
            $kegiatan = KegiatanLuarInstitusiModel::with('surat')->find($id);
            if (!$kegiatan) {
                $kegiatan = KegiatanInstitusiModel::with('surat')->find($id);
            }


            if (!$kegiatan || !$kegiatan->surat) {
                return response()->json(['message' => 'Surat tidak ditemukan'], 404);
            }


            $filePath = storage_path('app/public/' . $kegiatan->surat->file_surat);
           
            if (!file_exists($filePath)) {
                return response()->json(['message' => 'File tidak ditemukan'], 404);
            }


            return Response::download($filePath);


        } catch (\Exception $e) {
            Log::error('Error in downloadSurat: ' . $e->getMessage());
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengunduh file'
            ], 500);
        }
    }
    public function show($id)
    {
        try {
            $kegiatan = KegiatanLuarInstitusiModel::with(['user', 'surat'])->find($id);
            if (!$kegiatan) {
                $kegiatan = KegiatanInstitusiModel::with(['user', 'surat'])->find($id);
            }


            if (!$kegiatan) {
                return redirect()->route('dosen.kegiatan-non-jti.index')
                    ->with('error', 'Kegiatan tidak ditemukan');
            }


            return view('dosen.kegiatan-non-jti.show', [
                'kegiatan' => $kegiatan,
                'breadcrumb' => (object)[
                    'title' => 'Detail Kegiatan Non-JTI',
                    'list' => ['Home', 'Dosen', 'Kegiatan Non-JTI', 'Detail']
                ]
            ]);


        } catch (\Exception $e) {
            Log::error('Error in show: ' . $e->getMessage());
            return redirect()->route('dosen.kegiatan-non-jti.index')
                ->with('error', 'Terjadi kesalahan saat memuat detail kegiatan');
        }
    }


    public function getDetail($id)
    {
        try {
            // Coba cari di kegiatan luar institusi
            $kegiatan = KegiatanLuarInstitusiModel::with(['user', 'surat'])
                ->where('kegiatan_luar_institusi_id', $id)
                ->first();


            if (!$kegiatan) {
                // Jika tidak ditemukan, cari di kegiatan institusi
                $kegiatan = KegiatanInstitusiModel::with(['user', 'surat'])
                    ->where('kegiatan_institusi_id', $id)
                    ->first();
            }


            if (!$kegiatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kegiatan tidak ditemukan'
                ], 404);
            }


            // Format data sesuai jenis kegiatan
            $data = [
                'id' => $kegiatan instanceof KegiatanLuarInstitusiModel
                    ? $kegiatan->kegiatan_luar_institusi_id
                    : $kegiatan->kegiatan_institusi_id,
                'jenis_kegiatan' => $kegiatan instanceof KegiatanLuarInstitusiModel
                    ? 'Kegiatan Luar Institusi'
                    : 'Kegiatan Institusi',
                'nama_kegiatan' => $kegiatan instanceof KegiatanLuarInstitusiModel
                    ? $kegiatan->nama_kegiatan_luar_institusi
                    : $kegiatan->nama_kegiatan_institusi,
                'penyelenggara' => $kegiatan->penyelenggara,
                'lokasi_kegiatan' => $kegiatan->lokasi_kegiatan,
                'deskripsi_kegiatan' => $kegiatan->deskripsi_kegiatan,
                'tanggal_mulai' => $kegiatan->tanggal_mulai,
                'tanggal_selesai' => $kegiatan->tanggal_selesai,
                'status_persetujuan' => $kegiatan->status_persetujuan,
                'surat' => [
                    'judul_surat' => $kegiatan->surat->judul_surat,
                    'nomer_surat' => $kegiatan->surat->nomer_surat,
                    'tanggal_surat' => $kegiatan->surat->tanggal_surat
                ]
            ];


            return response()->json([
                'success' => true,
                'data' => $data
            ]);


        } catch (\Exception $e) {
            Log::error('Error in getDetail: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memuat detail kegiatan'
            ], 500);
        }
    }

    // Change getDosenList to getDosen since that's what's used in the route
    public function getDosen()
    {
        try {
            $dosen = UserModel::select('user_id', 'nama_lengkap', 'gelar_depan', 'gelar_belakang', 'nidn')
                ->where('level_id', 3)  // Level Dosen
                ->orderBy('nama_lengkap', 'asc')
                ->get()
                ->map(function($user) {
                    $nama = trim(($user->gelar_depan ? $user->gelar_depan . ' ' : '') . 
                            $user->nama_lengkap . 
                            ($user->gelar_belakang ? ', ' . $user->gelar_belakang : ''));
                    return [
                        'user_id' => $user->user_id,
                        'nama' => $nama,
                        'nidn' => $user->nidn
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $dosen
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getDosen: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data dosen'
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $loggedInUserId = session('user_id');
    
            // Cari di kegiatan luar institusi
            $kegiatan = KegiatanLuarInstitusiModel::with('surat')->find($id);
            $jenisKegiatan = 'luar_institusi';
    
            if (!$kegiatan) {
                // Jika tidak ditemukan, cari di kegiatan institusi
                $kegiatan = KegiatanInstitusiModel::with('surat')->find($id);
                $jenisKegiatan = 'institusi';
            }
    
            if (!$kegiatan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data kegiatan tidak ditemukan'
                ], 404);
            }
    
            // Cek kepemilikan data
            if ($kegiatan->user_id != $loggedInUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki izin untuk menghapus data ini'
                ], 403);
            }
    
            // Proses penghapusan seperti sebelumnya
            if ($kegiatan->surat && $kegiatan->surat->file_surat) {
                $filePath = $kegiatan->surat->file_surat;
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
            }
    
            $suratId = $kegiatan->surat_id;
            $kegiatan->delete();
    
            if ($suratId) {
                $surat = SuratModel::find($suratId);
                if ($surat) {
                    $surat->delete();
                }
            }
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Data kegiatan dan surat berhasil dihapus'
            ]);
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error in destroy: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus data'
            ], 500);
        }
    }
}