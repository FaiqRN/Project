<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\FinalDocumentModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
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
        $kegiatanJurusan = KegiatanJurusanModel::select('kegiatan_jurusan_id as id', 'nama_kegiatan_jurusan as nama_kegiatan', 'tanggal_selesai', 'status_kegiatan')
            ->where('status_kegiatan', 'selesai')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'jenis' => 'jurusan',
                    'nama_kegiatan' => $item->nama_kegiatan,
                    'tanggal' => $item->tanggal_selesai,
                    'status' => $item->status_kegiatan,
                ];
            });

        $kegiatanProdi = KegiatanProgramStudiModel::select('kegiatan_program_studi_id as id', 'nama_kegiatan_program_studi as nama_kegiatan', 'tanggal_selesai', 'status_kegiatan')
            ->where('status_kegiatan', 'selesai')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'jenis' => 'prodi',
                    'nama_kegiatan' => $item->nama_kegiatan,
                    'tanggal' => $item->tanggal_selesai,
                    'status' => $item->status_kegiatan,
                ];
            });

        $allKegiatan = $kegiatanJurusan->concat($kegiatanProdi);

        return DataTables::of($allKegiatan)
        ->addIndexColumn()
        ->addColumn('status', function ($row) {
            $statusClass = $row['status'] === 'selesai' ? 'success' : 'warning';
            return '<span class="badge badge-' . $statusClass . '">' . ucfirst($row['status']) . '</span>';
        })
        ->addColumn('action', function ($row) {
            $downloadUrl = route('kaprodi.kegiatan.download-dokumen', ['jenis' => $row['jenis'], 'id' => $row['id']]);
            return '<a href="' . $downloadUrl . '" class="btn btn-sm btn-primary">
                        <i class="fas fa-download"></i> Download
                    </a>';
        })
        ->rawColumns(['status', 'action'])
        ->make(true);
    
    }

    public function downloadDokumenFinal($jenis, $id)
    {
        try {
            if ($jenis === 'jurusan') {
                $finalDoc = FinalDocumentModel::where('kegiatan_jurusan_id', $id)->firstOrFail();
            } else {
                $finalDoc = FinalDocumentModel::where('kegiatan_program_studi_id', $id)->firstOrFail();
            }

            return Storage::download($finalDoc->file_akhir);
        } catch (\Exception $e) {
            return back()->with('error', 'Dokumen tidak ditemukan atau terjadi kesalahan.');
        }
    }
}
