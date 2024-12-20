<?php

namespace App\Http\Controllers;

use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use App\Models\SuratModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use App\Models\KegiatanInstitusiModel;
use App\Models\KegiatanLuarInstitusiModel;

class KegiatanController extends Controller
{
    public function index()
    {
        $surat = SuratModel::whereNull('deleted_at')->get();
        $pic = UserModel::where('level_id', 4)
                       ->whereNull('deleted_at')
                       ->get();
        $kegiatan_id = 4;
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

        // Method untuk mendapatkan data kegiatan institusi
    public function getKegiatanInstitusi()
    {
            try {
                $query = KegiatanInstitusiModel::with(['user', 'surat'])
                            ->where('status_persetujuan', 'disetujui')
                            ->select('t_kegiatan_institusi.*');
        
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
                        return '<button class="btn btn-info btn-sm detail-btn" data-id="'.$row->kegiatan_institusi_id.'">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                                <button class="btn btn-warning btn-sm edit-btn" data-id="'.$row->kegiatan_institusi_id.'">
                                    <i class="fas fa-edit"></i> Edit
                                </button>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
    }

        // Method untuk mendapatkan data kegiatan luar institusi
        public function getKegiatanLuarInstitusi()
        {
            try {
                $query = KegiatanLuarInstitusiModel::with(['user', 'surat'])
                            ->where('status_persetujuan', 'disetujui')
                            ->select('t_kegiatan_luar_institusi.*');
        
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
                        return '<button class="btn btn-info btn-sm detail-btn" data-id="'.$row->kegiatan_luar_institusi_id.'">
                                    <i class="fas fa-eye"></i> Detail
                                </button>
                                <button class="btn btn-warning btn-sm edit-btn" data-id="'.$row->kegiatan_luar_institusi_id.'">
                                    <i class="fas fa-edit"></i> Edit
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

    // Method untuk menampilkan detail kegiatan institusi
    public function showKegiatanInstitusi($id)
    {
        try {
            $kegiatan = KegiatanInstitusiModel::with(['user', 'surat'])->findOrFail($id);
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

    // Method untuk menampilkan detail kegiatan luar institusi
    public function showKegiatanLuarInstitusi($id)
    {
        try {
            $kegiatan = KegiatanLuarInstitusiModel::with(['user', 'surat'])->findOrFail($id);
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

    public function storeKegiatanInstitusi(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'surat_id' => 'required|exists:m_surat,surat_id',
                'user_id' => 'required|exists:m_user,user_id',
                'nama_kegiatan_institusi' => 'required|string|max:200',
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

            $kegiatan = KegiatanInstitusiModel::create([
                'surat_id' => $request->surat_id,
                'user_id' => $request->user_id,
                'nama_kegiatan_institusi' => $request->nama_kegiatan_institusi,
                'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
                'lokasi_kegiatan' => $request->lokasi_kegiatan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status_kegiatan' => 'berlangsung',
                'penyelenggara' => $request->penyelenggara
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kegiatan institusi berhasil ditambahkan',
                'data' => $kegiatan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function storeKegiatanLuarInstitusi(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'surat_id' => 'required|exists:m_surat,surat_id',
                'user_id' => 'required|exists:m_user,user_id',
                'nama_kegiatan_luar_institusi' => 'required|string|max:200',
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

            $kegiatan = KegiatanLuarInstitusiModel::create([
                'surat_id' => $request->surat_id,
                'user_id' => $request->user_id,
                'nama_kegiatan_luar_institusi' => $request->nama_kegiatan_luar_institusi,
                'deskripsi_kegiatan' => $request->deskripsi_kegiatan,
                'lokasi_kegiatan' => $request->lokasi_kegiatan,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'status_kegiatan' => 'berlangsung',
                'penyelenggara' => $request->penyelenggara
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Kegiatan luar institusi berhasil ditambahkan',
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

        // Method untuk update kegiatan institusi
        public function updateKegiatanInstitusi(Request $request, $id)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'surat_id' => 'required|exists:m_surat,surat_id',
                    'user_id' => 'required|exists:m_user,user_id',
                    'nama_kegiatan_institusi' => 'required|string|max:200',
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
    
                $kegiatan = KegiatanInstitusiModel::findOrFail($id);
                
                if($kegiatan->status_persetujuan != 'disetujui') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Hanya kegiatan yang sudah disetujui yang dapat diupdate'
                    ], 422);
                }
                
                $kegiatan->update($request->all());
    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Kegiatan institusi berhasil diupdate',
                    'data' => $kegiatan
                ]);
    
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Terjadi kesalahan saat mengupdate data: ' . $e->getMessage()
                ], 500);
            }
        }
    
        // Method untuk update kegiatan luar institusi
        public function updateKegiatanLuarInstitusi(Request $request, $id)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'surat_id' => 'required|exists:m_surat,surat_id',
                    'user_id' => 'required|exists:m_user,user_id',
                    'nama_kegiatan_luar_institusi' => 'required|string|max:200',
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
    
                $kegiatan = KegiatanLuarInstitusiModel::findOrFail($id);
                
                if($kegiatan->status_persetujuan != 'disetujui') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Hanya kegiatan yang sudah disetujui yang dapat diupdate'
                    ], 422);
                }
    
                $kegiatan->update($request->all());
    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Kegiatan luar institusi berhasil diupdate',
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

    // Method untuk menampilkan kegiatan PIC
    public function showKegiatanPIC()
    {
        try {
            // Mendapatkan ID user yang sedang login
            $userId = session('user_id');
            
            // Mengambil kegiatan jurusan dengan relasi surat
            $kegiatanJurusan = KegiatanJurusanModel::with(['user', 'surat'])
                ->where('user_id', $userId)
                ->where('status_kegiatan', 'berlangsung')
                ->first();
    
            // Mengambil kegiatan prodi dengan relasi surat
            $kegiatanProdi = KegiatanProgramStudiModel::with(['user', 'surat'])
                ->where('user_id', $userId)
                ->where('status_kegiatan', 'berlangsung')
                ->first();
    
            $kegiatanInstitusi = KegiatanInstitusiModel::with(['user', 'surat'])
                ->where('user_id', $userId)
                ->where('status_kegiatan', 'berlangsung')
                ->first();
                
            $kegiatanLuarInstitusi = KegiatanLuarInstitusiModel::with(['user', 'surat'])
                ->where('user_id', $userId)
                ->where('status_kegiatan', 'berlangsung')
                ->first();
                
            $breadcrumb = (object)[
                'title' => 'Kegiatan',
                'list' => ['Home', 'Agenda', 'Kegiatan']
            ];
    
            return view('pic.kegiatan', compact('kegiatanJurusan', 'kegiatanProdi','kegiatanInstitusi','kegiatanLuarInstitusi', 'breadcrumb'));
    
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk mengunduh surat tugas
    public function downloadSuratTugas($type, $id)
    {
        try {
            $kegiatan = null;
            
            if ($type === 'jurusan') {
                $kegiatan = KegiatanJurusanModel::with('surat')->findOrFail($id);
            } else if ($type === 'prodi') {
                $kegiatan = KegiatanProgramStudiModel::with('surat')->findOrFail($id);
            } else if ($type === 'institusi') {
                $kegiatan = KegiatanInstitusiModel::with('surat')->findOrFail($id);
            } else if ($type === 'luar-institusi') {
                $kegiatan = KegiatanLuarInstitusiModel::with('surat')->findOrFail($id);
            }

            if (!$kegiatan || !$kegiatan->surat_penugasan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Surat tugas tidak ditemukan'
                ], 404);
            }

            $filePath = storage_path('app/' . $kegiatan->surat_penugasan);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File surat tugas tidak ditemukan'
                ], 404);
            }

            return response()->download($filePath);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Method untuk validasi tanggal agenda
    public function validateTanggalAgenda(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'tanggal_agenda' => 'required|date',
                'kegiatan_type' => 'required|in:jurusan,prodi',
                'kegiatan_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $kegiatan = null;
            if ($request->kegiatan_type === 'jurusan') {
                $kegiatan = KegiatanJurusanModel::findOrFail($request->kegiatan_id);
            } else if ($request->kegiatan_type === 'prodi') {
                $kegiatan = KegiatanProgramStudiModel::findOrFail($request->kegiatan_id);
            } else if ($request->kegiatan_type === 'institusi') {
                $kegiatan = KegiatanInstitusiModel::findOrFail($request->kegiatan_id);
            } else if ($request->kegiatan_type === 'luar-institusi') {
                $kegiatan = KegiatanLuarInstitusiModel::findOrFail($request->kegiatan_id);
            }

            $tanggalAgenda = strtotime($request->tanggal_agenda);
            $tanggalMulai = strtotime($kegiatan->tanggal_mulai);
            $tanggalSelesai = strtotime($kegiatan->tanggal_selesai);

            if ($tanggalAgenda < $tanggalMulai || $tanggalAgenda > $tanggalSelesai) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tanggal agenda harus berada dalam rentang waktu kegiatan'
                ], 422);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Tanggal agenda valid'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
 }