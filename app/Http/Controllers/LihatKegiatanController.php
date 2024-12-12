<?php

namespace App\Http\Controllers;

use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use App\Models\KegiatanInstitusiModel;
use App\Models\KegiatanLuarInstitusiModel;
use App\Models\FinalDocumentModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LihatKegiatanController extends Controller
{
    public function index()
    {
        return view('kaprodi.kegiatan', [
            'breadcrumb' => (object)[
                'title' => 'Melihat Kegiatan',
                'list' => ['Home', 'Kegiatan', 'Melihat Kegiatan']
            ]
        ]);
    }

    public function getKegiatanData()
    {
        // Mengambil data kegiatan jurusan yang sudah selesai
        $kegiatan = KegiatanJurusanModel::with('user')
            ->select([
                'kegiatan_jurusan_id as id',
                'nama_kegiatan_jurusan as nama_kegiatan',
                'tanggal_selesai',
                'user_id',
                'status_kegiatan'
            ])
            ->where('status_kegiatan', 'selesai')
            ->get();

        return DataTables::of($kegiatan)
            ->addIndexColumn()
            ->addColumn('tanggal', function($row) {
                return date('d/m/Y', strtotime($row->tanggal_selesai));
            })
            ->addColumn('pic', function($row) {
                return $row->user->nama_lengkap;
            })
            ->addColumn('status', function($row) {
                return 'Selesai';
            })
            ->addColumn('action', function($row) {
                return '<button onclick="downloadDokumen('.$row->id.')" class="btn btn-sm btn-primary">
                        <i class="fas fa-download"></i> Download
                        </button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function downloadDokumenFinal($id)
    {
        $dokumen = FinalDocumentModel::where('kegiatan_jurusan_id', $id)->first();
        
        if (!$dokumen) {
            return response()->json([
                'success' => false,
                'message' => 'Dokumen tidak ditemukan'
            ], 404);
        }

        $path = storage_path('app/public/' . $dokumen->file_akhir);
        
        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        return response()->download($path);
    }
}