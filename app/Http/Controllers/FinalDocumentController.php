<?php


namespace App\Http\Controllers;


use App\Models\FinalDocumentModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;


class FinalDocumentController extends Controller
{
    public function index()
    {
        return view('dosen.final-document.index', [
            'breadcrumb' => (object)[
                'title' => 'Unggah Dokumen Akhir',
                'list' => ['Home', 'Unggah Dokumen']
            ]
        ]);
    }


    public function getKegiatan()
    {
        $kegiatanJurusan = KegiatanJurusanModel::where('user_id', Auth::id())
        ->whereNull('final_id')
        ->select([
            'kegiatan_jurusan_id as id',
            'nama_kegiatan_jurusan as nama',
            'tanggal_mulai',
            'tanggal_selesai',
            'status_kegiatan',
            DB::raw("'jurusan' as jenis")
        ]);
   
    // Query remains same
    $kegiatanProdi = KegiatanProgramStudiModel::where('user_id', Auth::id())
        ->whereNull('final_id')
        ->select([
            'kegiatan_program_studi_id as id',
            'nama_kegiatan_program_studi as nama',
            'tanggal_mulai',
            'tanggal_selesai',
            'status_kegiatan',
            DB::raw("'prodi' as jenis")
        ]);


        $kegiatan = $kegiatanJurusan->union($kegiatanProdi);


        return DataTables::of($kegiatan)
            ->addColumn('action', function ($row) {
                $buttons = '<div class="btn-group">';
                $buttons .= '<button type="button" class="btn btn-primary btn-sm upload-doc" data-id="'.$row->id.'" data-jenis="'.$row->jenis.'">
                    <i class="fas fa-upload"></i> Upload
                </button>';
                $buttons .= '</div>';
                return $buttons;
            })
            ->editColumn('tanggal_mulai', function($row) {
                return date('d/m/Y', strtotime($row->tanggal_mulai));
            })
            ->editColumn('tanggal_selesai', function($row) {
                return date('d/m/Y', strtotime($row->tanggal_selesai));
            })
            ->rawColumns(['action'])
            ->make(true);
    }


    public function store(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required',
            'jenis_kegiatan' => 'required|in:jurusan,prodi',
            'file_akhir' => 'required|file|max:10240|mimes:pdf,doc,docx'
        ]);


        try {
            $filePath = $request->file('file_akhir')->store('dokumen_akhir');
           
            $finalDoc = new FinalDocumentModel();
            $finalDoc->file_akhir = $filePath;
           
            if ($request->jenis_kegiatan === 'jurusan') {
                $finalDoc->kegiatan_jurusan_id = $request->kegiatan_id;
                $kegiatan = KegiatanJurusanModel::findOrFail($request->kegiatan_id);
            } else {
                $finalDoc->kegiatan_program_studi_id = $request->kegiatan_id;
                $kegiatan = KegiatanProgramStudiModel::findOrFail($request->kegiatan_id);
            }
           
            $finalDoc->save();
           
            $kegiatan->status_kegiatan = 'selesai';
            $kegiatan->final_id = $finalDoc->final_id;
            $kegiatan->save();


            return response()->json([
                'status' => 'success',
                'message' => 'Dokumen akhir berhasil diunggah'
            ]);


        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}


