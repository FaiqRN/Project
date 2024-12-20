<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinalDocumentModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use App\Models\KegiatanInstitusiModel;
use App\Models\KegiatanLuarInstitusiModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AdminUnggahDokumenAkhirController extends Controller
{
    public function index()
    {
        return view('admin.dosen.agenda.unggah-dokumen', [
            'breadcrumb' => (object)[
                'title' => 'Unggah Dokumen Akhir',
                'list' => ['Home', 'Admin', 'Dosen', 'Agenda', 'Unggah Dokumen']
            ]
        ]);
    }

    public function getKegiatanList()
    {
        // Ambil semua kegiatan jurusan
        $kegiatanJurusan = KegiatanJurusanModel::with(['finalDocument', 'user'])
            ->get()
            ->map(function ($kegiatan) {
                return [
                    'id' => $kegiatan->kegiatan_jurusan_id,
                    'nama' => $kegiatan->nama_kegiatan_jurusan,
                    'type' => 'jurusan',
                    'pic' => $kegiatan->user->name,
                    'status' => $kegiatan->status_kegiatan,
                    'has_final' => $kegiatan->finalDocument()->exists(),
                    'file_path' => optional($kegiatan->finalDocument)->file_akhir,
                    'created_at' => $kegiatan->created_at
                ];
            });

        // Ambil semua kegiatan program studi
        $kegiatanProdi = KegiatanProgramStudiModel::with(['finalDocument', 'user'])
            ->get()
            ->map(function ($kegiatan) {
                return [
                    'id' => $kegiatan->kegiatan_program_studi_id,
                    'nama' => $kegiatan->nama_kegiatan_program_studi,
                    'type' => 'prodi',
                    'pic' => $kegiatan->user->name,
                    'status' => $kegiatan->status_kegiatan,
                    'has_final' => $kegiatan->finalDocument()->exists(),
                    'file_path' => optional($kegiatan->finalDocument)->file_akhir,
                    'created_at' => $kegiatan->created_at
                ];
            });

        $kegiatanInstitusi= KegiatanInstitusiModel::with(['finalDocument', 'user'])
            ->get()
            ->map(function ($kegiatan) {
                return [
                    'id' => $kegiatan->kegiatan_institusi_id,
                    'nama' => $kegiatan->nama_kegiatan_institusi,
                    'type' => 'institusi',
                    'pic' => $kegiatan->user->name,
                    'status' => $kegiatan->status_kegiatan,
                    'has_final' => $kegiatan->finalDocument()->exists(),
                    'file_path' => optional($kegiatan->finalDocument)->file_akhir,
                    'created_at' => $kegiatan->created_at
                ];
            });

        $kegiatanLuarInstitusi= KegiatanLuarInstitusiModel::with(['finalDocument', 'user'])
            ->get()
            ->map(function ($kegiatan) {
                return [
                    'id' => $kegiatan->kegiatan_luar_institusi_id,
                    'nama' => $kegiatan->nama_kegiatan_luar_institusi,
                    'type' => 'luar_institusi',
                    'pic' => $kegiatan->user->name,
                    'status' => $kegiatan->status_kegiatan,
                    'has_final' => $kegiatan->finalDocument()->exists(),
                    'file_path' => optional($kegiatan->finalDocument)->file_akhir,
                    'created_at' => $kegiatan->created_at
                ];
            });

        $combinedKegiatan = $kegiatanJurusan->concat($kegiatanProdi)->concat($kegiatanInstitusi)->concat($kegiatanLuarInstitusi);

        return DataTables::of($combinedKegiatan)
            ->addIndexColumn()
            ->addColumn('status', function($row) {
                $statusClass = $row['status'] === 'selesai' ? 'success' : 'warning';
                $statusText = ucfirst($row['status']);
                return "<span class='badge badge-{$statusClass}'>{$statusText}</span>";
            })
            ->addColumn('dokumen', function($row) {
                if ($row['has_final']) {
                    return '<button type="button" class="btn btn-info btn-sm download-btn" 
                        data-id="'.$row['id'].'" data-type="'.$row['type'].'">
                        <i class="fas fa-download"></i> Download
                    </button>';
                }
                return '<span class="badge badge-danger">Belum ada</span>';
            })
            ->addColumn('action', function($row) {
                $buttons = '';
                if ($row['has_final']) {
                    $buttons .= '<button type="button" class="btn btn-warning btn-sm mr-1 edit-btn" 
                        data-id="'.$row['id'].'" data-type="'.$row['type'].'">
                        <i class="fas fa-edit"></i> Edit
                    </button>';
                    $buttons .= '<button type="button" class="btn btn-danger btn-sm delete-btn" 
                        data-id="'.$row['id'].'" data-type="'.$row['type'].'">
                        <i class="fas fa-trash"></i> Hapus
                    </button>';
                } else if ($row['status'] === 'selesai') {
                    $buttons .= '<button type="button" class="btn btn-success btn-sm upload-btn" 
                        data-id="'.$row['id'].'" data-type="'.$row['type'].'">
                        <i class="fas fa-upload"></i> Upload
                    </button>';
                }
                return $buttons;
            })
            ->rawColumns(['status', 'dokumen', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'kegiatan_id' => 'required',
                'kegiatan_type' => 'required|in:jurusan,prodi,institusi,luar_institusi',
                'dokumen_akhir' => 'required|file|mimes:pdf|max:10240'
            ]);

            // Upload file baru
            $file = $request->file('dokumen_akhir');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/dokumen_akhir', $fileName);

            // Cek apakah sudah ada dokumen sebelumnya
            if ($request->kegiatan_type === 'jurusan') {
                $finalDoc = FinalDocumentModel::firstOrNew([
                    'kegiatan_jurusan_id' => $request->kegiatan_id
                ]);
            } elseif ($request->kegiatan_type === 'prodi') {
                $finalDoc = FinalDocumentModel::firstOrNew([
                    'kegiatan_program_studi_id' => $request->kegiatan_id
                ]);
            } elseif ($request->kegiatan_type === 'institusi') {
                $finalDoc = FinalDocumentModel::firstOrNew([
                    'kegiatan_institusi_id' => $request->kegiatan_id
                ]);
            } elseif ($request->kegiatan_type === 'luar_institusi') {
                $finalDoc = FinalDocumentModel::firstOrNew([
                    'kegiatan_luar_institusi_id' => $request->kegiatan_id
                ]);
            }

            // Hapus file lama jika ada
            if ($finalDoc->exists && Storage::exists($finalDoc->file_akhir)) {
                Storage::delete($finalDoc->file_akhir);
            }

            // Update atau buat dokumen baru
            $finalDoc->file_akhir = $path;
            $finalDoc->save();

            DB::commit();
            return response()->json(['message' => 'Dokumen berhasil diunggah'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'kegiatan_id' => 'required',
                'kegiatan_type' => 'required|in:jurusan,prodi,institusi,luar_institusi',
                'dokumen_akhir' => 'required|file|mimes:pdf|max:10240'
            ]);

            if ($request->kegiatan_type === 'jurusan') {
                $finalDoc = FinalDocumentModel::where('kegiatan_jurusan_id', $request->kegiatan_id)->firstOrFail();
            } elseif ($request->kegiatan_type === 'prodi') {
                $finalDoc = FinalDocumentModel::where('kegiatan_program_studi_id', $request->kegiatan_id)->firstOrFail();
            } elseif ($request->kegiatan_type === 'institusi') {
                $finalDoc = FinalDocumentModel::where('kegiatan_institusi_id', $request->kegiatan_id)->firstOrFail();
            } elseif ($request->kegiatan_type === 'luar_institusi') {
                $finalDoc = FinalDocumentModel::where('kegiatan_luar_institusi_id', $request->kegiatan_id)->firstOrFail();
            }

            if (Storage::exists($finalDoc->file_akhir)) {
                Storage::delete($finalDoc->file_akhir);
            }

            $file = $request->file('dokumen_akhir');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/dokumen_akhir', $fileName);

            $finalDoc->file_akhir = $path;
            $finalDoc->save();

            DB::commit();
            return response()->json(['message' => 'Dokumen berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id, $type)
    {
        try {
            DB::beginTransaction();

            if ($type === 'jurusan') {
                $finalDoc = FinalDocumentModel::where('kegiatan_jurusan_id', $id)->firstOrFail();
                $kegiatan = KegiatanJurusanModel::findOrFail($id);
            } elseif ($type === 'prodi') {
                $finalDoc = FinalDocumentModel::where('kegiatan_program_studi_id', $id)->firstOrFail();
                $kegiatan = KegiatanProgramStudiModel::findOrFail($id);
            } elseif ($type === 'institusi') {
                $finalDoc = FinalDocumentModel::where('kegiatan_institusi_id', $id)->firstOrFail();
                $kegiatan = KegiatanInstitusiModel::findOrFail($id);
            } elseif ($type === 'luar_institusi') {
                $finalDoc = FinalDocumentModel::where('kegiatan_luar_institusi_id', $id)->firstOrFail();
                $kegiatan = KegiatanLuarInstitusiModel::findOrFail($id);
            }

            if (Storage::exists($finalDoc->file_akhir)) {
                Storage::delete($finalDoc->file_akhir);
            }

            $finalDoc->delete();

            DB::commit();
            return response()->json([
                'message' => 'Dokumen berhasil dihapus. Dosen dapat mengunggah ulang dokumen.',
                'status' => 'success'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function download($id, $type)
    {
        try {
            if ($type === 'jurusan') {
                $finalDoc = FinalDocumentModel::where('kegiatan_jurusan_id', $id)->firstOrFail();
            } elseif ($type === 'prodi') {
                $finalDoc = FinalDocumentModel::where('kegiatan_program_studi_id', $id)->firstOrFail();
            } elseif ($type === 'institusi') {
                $finalDoc = FinalDocumentModel::where('kegiatan_institusi_id', $id)->firstOrFail();
            } elseif ($type === 'luar_institusi') {
                $finalDoc = FinalDocumentModel::where('kegiatan_luar_institusi_id', $id)->firstOrFail();
            }
            return Storage::download($finalDoc->file_akhir);
        } catch (\Exception $e) {
            return response()->json(['message' => 'File tidak ditemukan'], 404);
        }
    }
}