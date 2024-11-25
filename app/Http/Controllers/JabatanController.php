<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JabatanModel;
use App\Models\KegiatanLuarInstitusiModel;
use App\Models\KegiatanInstitusiModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use App\Models\LevelModel;
use App\Models\UserModel;

class JabatanController extends Controller
{
    public function index()
    {
        try {
            // Data untuk breadcrumb
            $breadcrumb = (object)[
                'title' => 'Pilih Jabatan',
                'list' => ['Home', 'Dosen', 'Agenda', 'Pilih Jabatan']
            ];
    
            // Mengambil data jabatan dengan relasi
            $jabatan = JabatanModel::with([
                'user', 
                'level',
                'kegiatanLuarInstitusi',
                'kegiatanInstitusi',
                'kegiatanJurusan',
                'kegiatanProgramStudi'
            ])->get();
            
            // Mengambil data untuk dropdown
            $users = UserModel::whereHas('level', function($query) {
                $query->whereIn('level_nama', ['Dosen', 'PIC']);
            })
            ->select('user_id', 'nidn', 'nama_lengkap', 'jabatan_fungsional', 'level_id')
            ->get();

        $levels = LevelModel::all();
            
            // Mengambil data kegiatan untuk dropdown
            $kegiatanLuar = KegiatanLuarInstitusiModel::where('status_kegiatan', 'berlangsung')->get();
            $kegiatanInstitusi = KegiatanInstitusiModel::where('status_kegiatan', 'berlangsung')->get();
            $kegiatanJurusan = KegiatanJurusanModel::where('status_kegiatan', 'berlangsung')->get();
            $kegiatanProdi = KegiatanProgramStudiModel::where('status_kegiatan', 'berlangsung')->get();
            
            return view('admin.dosen.agenda.jabatan', compact(
                'breadcrumb',
                'jabatan', 
                'users', 
                'levels',
                'kegiatanLuar',
                'kegiatanInstitusi',
                'kegiatanJurusan',
                'kegiatanProdi'
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'user_id' => 'required|exists:m_user,user_id',
                'level_id' => 'required|exists:m_level,level_id',
                'jabatan' => 'required|in:ketua_pelaksana,sekertaris,bendahara,anggota',
                'jenis_kegiatan' => 'required|in:luar,institusi,jurusan,prodi',
                'kegiatan_id' => 'required'
            ]);

            DB::beginTransaction();

            // Cek apakah user sudah memiliki jabatan di kegiatan yang sama
            $existingJabatan = JabatanModel::where('user_id', $request->user_id)
                ->where(function($query) use ($request) {
                    switch($request->jenis_kegiatan) {
                        case 'luar':
                            $query->where('kegiatan_luar_institusi_id', $request->kegiatan_id);
                            break;
                        case 'institusi':
                            $query->where('kegiatan_institusi_id', $request->kegiatan_id);
                            break;
                        case 'jurusan':
                            $query->where('kegiatan_jurusan_id', $request->kegiatan_id);
                            break;
                        case 'prodi':
                            $query->where('kegiatan_program_studi_id', $request->kegiatan_id);
                            break;
                    }
                })->exists();

            if ($existingJabatan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User sudah memiliki jabatan dalam kegiatan ini'
                ], 422);
            }

            // Buat jabatan baru
            $jabatan = new JabatanModel();
            $jabatan->user_id = $request->user_id;
            $jabatan->level_id = $request->level_id;
            $jabatan->jabatan = $request->jabatan;

            // Set kegiatan ID sesuai jenis
            switch($request->jenis_kegiatan) {
                case 'luar':
                    $jabatan->kegiatan_luar_institusi_id = $request->kegiatan_id;
                    break;
                case 'institusi':
                    $jabatan->kegiatan_institusi_id = $request->kegiatan_id;
                    break;
                case 'jurusan':
                    $jabatan->kegiatan_jurusan_id = $request->kegiatan_id;
                    break;
                case 'prodi':
                    $jabatan->kegiatan_program_studi_id = $request->kegiatan_id;
                    break;
            }

            $jabatan->save();
            
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Jabatan berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $jabatan = JabatanModel::with([
                'user', 
                'level',
                'kegiatanLuarInstitusi',
                'kegiatanInstitusi',
                'kegiatanJurusan',
                'kegiatanProgramStudi'
            ])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'data' => $jabatan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'user_id' => 'required|exists:m_user,user_id',
                'level_id' => 'required|exists:m_level,level_id',
                'jabatan' => 'required|in:ketua_pelaksana,sekertaris,bendahara,anggota',
                'jenis_kegiatan' => 'required|in:luar,institusi,jurusan,prodi',
                'kegiatan_id' => 'required'
            ]);

            DB::beginTransaction();

            $jabatan = JabatanModel::findOrFail($id);

            // Cek apakah ada perubahan data
            if ($jabatan->user_id == $request->user_id &&
                $jabatan->level_id == $request->level_id &&
                $jabatan->jabatan == $request->jabatan &&
                $this->checkKegiatanSama($jabatan, $request->jenis_kegiatan, $request->kegiatan_id)) {
                return response()->json([
                    'status' => 'warning',
                    'message' => 'Tidak ada perubahan data'
                ]);
            }

            // Update data jabatan
            $jabatan->user_id = $request->user_id;
            $jabatan->level_id = $request->level_id;
            $jabatan->jabatan = $request->jabatan;

            // Reset semua kegiatan ID
            $jabatan->kegiatan_luar_institusi_id = null;
            $jabatan->kegiatan_institusi_id = null;
            $jabatan->kegiatan_jurusan_id = null;
            $jabatan->kegiatan_program_studi_id = null;

            // Set kegiatan ID yang baru
            switch($request->jenis_kegiatan) {
                case 'luar':
                    $jabatan->kegiatan_luar_institusi_id = $request->kegiatan_id;
                    break;
                case 'institusi':
                    $jabatan->kegiatan_institusi_id = $request->kegiatan_id;
                    break;
                case 'jurusan':
                    $jabatan->kegiatan_jurusan_id = $request->kegiatan_id;
                    break;
                case 'prodi':
                    $jabatan->kegiatan_program_studi_id = $request->kegiatan_id;
                    break;
            }

            $jabatan->save();
            
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Jabatan berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $jabatan = JabatanModel::findOrFail($id);
            $jabatan->delete();
            
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Jabatan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Helper method untuk cek kegiatan sama
    private function checkKegiatanSama($jabatan, $jenisKegiatan, $kegiatanId)
    {
        switch($jenisKegiatan) {
            case 'luar':
                return $jabatan->kegiatan_luar_institusi_id == $kegiatanId;
            case 'institusi':
                return $jabatan->kegiatan_institusi_id == $kegiatanId;
            case 'jurusan':
                return $jabatan->kegiatan_jurusan_id == $kegiatanId;
            case 'prodi':
                return $jabatan->kegiatan_program_studi_id == $kegiatanId;
            default:
                return false;
        }
    }

    public function getUserLevel($userId)
    {
        try {
            $user = UserModel::with('level')->findOrFail($userId);
            return response()->json([
                'status' => 'success',
                'data' => [
                    'level_id' => $user->level_id,
                    'level_nama' => $user->level->level_nama
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk mengambil data kegiatan berdasarkan jenis
    public function getKegiatan(Request $request)
    {
        try {
            $jenisKegiatan = $request->jenis_kegiatan;
            $kegiatan = [];

            switch($jenisKegiatan) {
                case 'luar':
                    $kegiatan = KegiatanLuarInstitusiModel::where('status_kegiatan', 'berlangsung')
                        ->select('kegiatan_luar_institusi_id as id', 'nama_kegiatan_luar_institusi as nama')
                        ->get();
                    break;
                case 'institusi':
                    $kegiatan = KegiatanInstitusiModel::where('status_kegiatan', 'berlangsung')
                        ->select('kegiatan_institusi_id as id', 'nama_kegiatan_institusi as nama')
                        ->get();
                    break;
                case 'jurusan':
                    $kegiatan = KegiatanJurusanModel::where('status_kegiatan', 'berlangsung')
                        ->select('kegiatan_jurusan_id as id', 'nama_kegiatan_jurusan as nama')
                        ->get();
                    break;
                case 'prodi':
                    $kegiatan = KegiatanProgramStudiModel::where('status_kegiatan', 'berlangsung')
                        ->select('kegiatan_program_studi_id as id', 'nama_kegiatan_program_studi as nama')
                        ->get();
                    break;
            }

            return response()->json([
                'status' => 'success',
                'data' => $kegiatan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
}