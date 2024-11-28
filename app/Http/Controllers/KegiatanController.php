<?php

namespace App\Http\Controllers;

use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use App\Models\SuratModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KegiatanController extends Controller
{
    public function index()
    {
        $surat = SuratModel::whereNull('deleted_at')->get();
        $pic = UserModel::where('level_id', 4)
                       ->whereNull('deleted_at')
                       ->get();
        
        $breadcrumb = (object)[
            'title' => 'Kegiatan',
            'list' => ['Home', 'Dosen', 'Agenda', 'Kegiatan']
        ];
        
        return view('admin.dosen.agenda.kegiatan', compact('surat', 'pic', 'breadcrumb'));
    }

    public function getKegiatanJurusan()
    {
        try {
            $query = KegiatanJurusanModel::with(['user', 'surat'])
                        ->select('t_kegiatan_jurusan.*');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('penanggung_jawab', function ($row) {
                    return $row->user->nama_lengkap ?? '-';
                })
                ->addColumn('surat_tugas', function ($row) {
                    return $row->surat->nomer_surat ?? '-';
                })
                ->addColumn('periode', function ($row) {
                    return date('d/m/Y', strtotime($row->tanggal_mulai)) . ' - ' . 
                           date('d/m/Y', strtotime($row->tanggal_selesai));
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-info btn-sm detail-btn" data-id="'.$row->kegiatan_jurusan_id.'">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                            <button class="btn btn-warning btn-sm edit-btn" data-id="'.$row->kegiatan_jurusan_id.'">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="'.$row->kegiatan_jurusan_id.'">
                                <i class="fas fa-trash"></i> Hapus
                            </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getKegiatanProdi()
    {
        try {
            $query = KegiatanProgramStudiModel::with(['user', 'surat'])
                        ->select('t_kegiatan_program_studi.*');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('penanggung_jawab', function ($row) {
                    return $row->user->nama_lengkap ?? '-';
                })
                ->addColumn('surat_tugas', function ($row) {
                    return $row->surat->nomer_surat ?? '-';
                })
                ->addColumn('periode', function ($row) {
                    return date('d/m/Y', strtotime($row->tanggal_mulai)) . ' - ' . 
                           date('d/m/Y', strtotime($row->tanggal_selesai));
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-info btn-sm detail-btn" data-id="'.$row->kegiatan_program_studi_id.'">
                                <i class="fas fa-eye"></i> Detail
                            </button>
                            <button class="btn btn-warning btn-sm edit-btn" data-id="'.$row->kegiatan_program_studi_id.'">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="'.$row->kegiatan_program_studi_id.'">
                                <i class="fas fa-trash"></i> Hapus
                            </button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function showKegiatanJurusan($id)
    {
        try {
            $kegiatan = KegiatanJurusanModel::with(['user', 'surat'])->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $kegiatan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function showKegiatanProdi($id)
    {
        try {
            $kegiatan = KegiatanProgramStudiModel::with(['user', 'surat'])->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $kegiatan
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }

    public function storeKegiatanJurusan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'surat_id' => 'required|exists:m_surat,surat_id',
                'user_id' => 'required|exists:m_user,user_id',
                'nama_kegiatan_jurusan' => 'required|string|max:200',
                'deskripsi_kegiatan' => 'required|string',
                'lokasi_kegiatan' => 'required|string',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'penyelenggara' => 'required|string|max:150'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verifikasi user adalah PIC
            $user = UserModel::find($request->user_id);
            if ($user->level_id != 4) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Penanggung jawab harus memiliki level PIC'
                ], 422);
            }

            $kegiatan = KegiatanJurusanModel::create([
                'surat_id' => $request->surat_id,
                'user_id' => $request->user_id,
                'nama_kegiatan_jurusan' => $request->nama_kegiatan_jurusan,
                'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
                'lokasi_kegiatan' => $request->lokasi_kegiatan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status_kegiatan' => 'berlangsung',
                'penyelenggara' => $request->penyelenggara
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kegiatan jurusan berhasil ditambahkan',
                'data' => $kegiatan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeKegiatanProdi(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'surat_id' => 'required|exists:m_surat,surat_id',
                'user_id' => 'required|exists:m_user,user_id',
                'nama_kegiatan_program_studi' => 'required|string|max:200',
                'deskripsi_kegiatan' => 'required|string',
                'lokasi_kegiatan' => 'required|string',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'penyelenggara' => 'required|string|max:150'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verifikasi user adalah PIC
            $user = UserModel::find($request->user_id);
            if ($user->level_id != 4) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Penanggung jawab harus memiliki level PIC'
                ], 422);
            }

            $kegiatan = KegiatanProgramStudiModel::create([
                'surat_id' => $request->surat_id,
                'user_id' => $request->user_id,
                'nama_kegiatan_program_studi' => $request->nama_kegiatan_program_studi,
                'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
                'lokasi_kegiatan' => $request->lokasi_kegiatan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status_kegiatan' => 'berlangsung',
                'penyelenggara' => $request->penyelenggara
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kegiatan program studi berhasil ditambahkan',
                'data' => $kegiatan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateKegiatanJurusan(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'surat_id' => 'required|exists:m_surat,surat_id',
                'user_id' => 'required|exists:m_user,user_id',
                'nama_kegiatan_jurusan' => 'required|string|max:200',
                'deskripsi_kegiatan' => 'required|string',
                'lokasi_kegiatan' => 'required|string',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'penyelenggara' => 'required|string|max:150'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $kegiatan = KegiatanJurusanModel::findOrFail($id);
            $kegiatan->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Kegiatan jurusan berhasil diupdate',
                'data' => $kegiatan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateKegiatanProdi(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'surat_id' => 'required|exists:m_surat,surat_id',
                'user_id' => 'required|exists:m_user,user_id',
                'nama_kegiatan_program_studi' => 'required|string|max:200',
                'deskripsi_kegiatan' => 'required|string',
                'lokasi_kegiatan' => 'required|string',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
                'penyelenggara' => 'required|string|max:150'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $kegiatan = KegiatanProgramStudiModel::findOrFail($id);
            $kegiatan->update($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Kegiatan program studi berhasil diupdate',
                'data' => $kegiatan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyKegiatanJurusan($id)
    {
        try {
            $kegiatan = KegiatanJurusanModel::findOrFail($id);
            $kegiatan->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Kegiatan jurusan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyKegiatanProdi($id)
    {
        try {
            $kegiatan = KegiatanProgramStudiModel::findOrFail($id);
            $kegiatan->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Kegiatan program studi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
 }