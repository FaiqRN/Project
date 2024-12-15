<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinalDocumentModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
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

        $combinedKegiatan = $kegiatanJurusan->concat($kegiatanProdi);

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
                'kegiatan_type' => 'required|in:jurusan,prodi',
                'dokumen_akhir' => 'required|file|mimes:pdf|max:10240'
            ]);

            $file = $request->file('dokumen_akhir');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/dokumen_akhir', $fileName);

            $finalDoc = new FinalDocumentModel();
            $finalDoc->file_akhir = $path;

            if ($request->kegiatan_type === 'jurusan') {
                $finalDoc->kegiatan_jurusan_id = $request->kegiatan_id;
            } else {
                $finalDoc->kegiatan_program_studi_id = $request->kegiatan_id;
            }

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
                'kegiatan_type' => 'required|in:jurusan,prodi',
                'dokumen_akhir' => 'required|file|mimes:pdf|max:10240'
            ]);

            if ($request->kegiatan_type === 'jurusan') {
                $finalDoc = FinalDocumentModel::where('kegiatan_jurusan_id', $request->kegiatan_id)->firstOrFail();
            } else {
                $finalDoc = FinalDocumentModel::where('kegiatan_program_studi_id', $request->kegiatan_id)->firstOrFail();
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
            } else {
                $finalDoc = FinalDocumentModel::where('kegiatan_program_studi_id', $id)->firstOrFail();
            }

            if (Storage::exists($finalDoc->file_akhir)) {
                Storage::delete($finalDoc->file_akhir);
            }

            $finalDoc->delete();

            DB::commit();
            return response()->json(['message' => 'Dokumen berhasil dihapus'], 200);
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
            } else {
                $finalDoc = FinalDocumentModel::where('kegiatan_program_studi_id', $id)->firstOrFail();
            }
            return Storage::download($finalDoc->file_akhir);
        } catch (\Exception $e) {
            return response()->json(['message' => 'File tidak ditemukan'], 404);
        }
    }
}