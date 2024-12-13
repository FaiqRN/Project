<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AgendaModel;
use App\Models\FinalDocumentModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;


class UnggahDokumenAkhirController extends Controller
{
    public function index()
    {
        return view('pic.dokumen', [
            'breadcrumb' => (object)[
                'title' => 'Unggah Dokumen Akhir',
                'list' => ['Home', 'PIC', 'Unggah Dokumen Akhir']
            ]
        ]);
    }

    public function getKegiatanList()
    {
        $userId = session('user_id');

        // Ambil kegiatan jurusan
        $kegiatanJurusan = KegiatanJurusanModel::where('user_id', $userId)
            ->with(['finalDocument'])
            ->get()
            ->map(function ($kegiatan) {
                return [
                    'id' => $kegiatan->kegiatan_jurusan_id,
                    'nama' => $kegiatan->nama_kegiatan_jurusan,
                    'type' => 'jurusan',
                    'status' => $kegiatan->status_kegiatan,
                    'has_final' => $kegiatan->finalDocument()->exists(),
                    'file_path' => optional($kegiatan->finalDocument)->file_akhir
                ];
            });

        // Ambil kegiatan program studi
        $kegiatanProdi = KegiatanProgramStudiModel::where('user_id', $userId)
            ->with(['finalDocument'])
            ->get()
            ->map(function ($kegiatan) {
                return [
                    'id' => $kegiatan->kegiatan_program_studi_id,
                    'nama' => $kegiatan->nama_kegiatan_program_studi,
                    'type' => 'prodi',
                    'status' => $kegiatan->status_kegiatan,
                    'has_final' => $kegiatan->finalDocument()->exists(),
                    'file_path' => optional($kegiatan->finalDocument)->file_akhir
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
                if ($row['status'] === 'selesai') {
                    if ($row['has_final']) {
                        $buttons .= '<button type="button" class="btn btn-primary btn-sm mr-1 edit-btn" 
                            data-id="'.$row['id'].'" data-type="'.$row['type'].'">
                            <i class="fas fa-edit"></i> Edit
                        </button>';
                    } else {
                        $buttons .= '<button type="button" class="btn btn-success btn-sm mr-1 upload-btn" 
                            data-id="'.$row['id'].'" data-type="'.$row['type'].'">
                            <i class="fas fa-upload"></i> Upload
                        </button>';
                    }
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

            // Upload file
            $file = $request->file('dokumen_akhir');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/dokumen_akhir', $fileName);

            // Simpan dokumen final
            $finalDoc = new FinalDocumentModel();
            $finalDoc->file_akhir = $path;

            if ($request->kegiatan_type === 'jurusan') {
                $finalDoc->kegiatan_jurusan_id = $request->kegiatan_id;
                $kegiatan = KegiatanJurusanModel::findOrFail($request->kegiatan_id);
            } else {
                $finalDoc->kegiatan_program_studi_id = $request->kegiatan_id;
                $kegiatan = KegiatanProgramStudiModel::findOrFail($request->kegiatan_id);
            }

            $finalDoc->save();

            // Update status kegiatan
            $kegiatan->status_kegiatan = 'selesai';
            $kegiatan->save();

            DB::commit();
            return response()->json(['message' => 'Dokumen akhir berhasil diunggah'], 200);
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

    public function update(Request $request)
    {
        try {
            DB::beginTransaction();
    
            $request->validate([
                'kegiatan_id' => 'required',
                'kegiatan_type' => 'required|in:jurusan,prodi',
                'dokumen_akhir' => 'required|file|mimes:pdf|max:10240'
            ]);
    
            // Cari dokumen yang akan diupdate
            if ($request->kegiatan_type === 'jurusan') {
                $finalDoc = FinalDocumentModel::where('kegiatan_jurusan_id', $request->kegiatan_id)->firstOrFail();
            } else {
                $finalDoc = FinalDocumentModel::where('kegiatan_program_studi_id', $request->kegiatan_id)->firstOrFail();
            }
    
            // Hapus file lama jika ada
            if (Storage::exists($finalDoc->file_akhir)) {
                Storage::delete($finalDoc->file_akhir);
            }
    
            // Upload file baru
            $file = $request->file('dokumen_akhir');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('public/dokumen_akhir', $fileName);
    
            // Update record di database
            $finalDoc->file_akhir = $path;
            $finalDoc->save();
    
            DB::commit();
            return response()->json(['message' => 'Dokumen akhir berhasil diperbarui'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating document: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}