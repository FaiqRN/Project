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

    // Get Data Kegiatan Jurusan untuk DataTable
    public function getKegiatanJurusan()
    {
        $query = KegiatanJurusanModel::with(['user', 'surat'])
                    ->select('t_kegiatan_jurusan.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('penanggung_jawab', function ($row) {
                return $row->user->nama_lengkap;
            })
            ->addColumn('surat_tugas', function ($row) {
                return $row->surat->nomer_surat;
            })
            ->addColumn('periode', function ($row) {
                return date('d/m/Y', strtotime($row->tanggal_mulai)) . ' - ' . 
                       date('d/m/Y', strtotime($row->tanggal_selesai));
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group" role="group">';
                $btn .= '<button class="btn btn-info btn-sm detail-btn" data-id="'.$row->kegiatan_jurusan_id.'" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>';
                $btn .= '<button class="btn btn-primary btn-sm edit-btn" data-id="'.$row->kegiatan_jurusan_id.'" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>';
                $btn .= '<button class="btn btn-danger btn-sm delete-btn" data-id="'.$row->kegiatan_jurusan_id.'" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Get Data Kegiatan Prodi untuk DataTable
    public function getKegiatanProdi()
    {
        $query = KegiatanProgramStudiModel::with(['user', 'surat'])
                    ->select('t_kegiatan_program_studi.*');

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('penanggung_jawab', function ($row) {
                return $row->user->nama_lengkap;
            })
            ->addColumn('surat_tugas', function ($row) {
                return $row->surat->nomer_surat;
            })
            ->addColumn('periode', function ($row) {
                return date('d/m/Y', strtotime($row->tanggal_mulai)) . ' - ' . 
                       date('d/m/Y', strtotime($row->tanggal_selesai));
            })
            ->addColumn('action', function ($row) {
                $btn = '<div class="btn-group" role="group">';
                $btn .= '<button class="btn btn-info btn-sm detail-btn" data-id="'.$row->kegiatan_program_studi_id.'" title="Detail">
                            <i class="fas fa-eye"></i>
                        </button>';
                $btn .= '<button class="btn btn-primary btn-sm edit-btn" data-id="'.$row->kegiatan_program_studi_id.'" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>';
                $btn .= '<button class="btn btn-danger btn-sm delete-btn" data-id="'.$row->kegiatan_program_studi_id.'" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>';
                $btn .= '</div>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Store Kegiatan Jurusan
    public function storeKegiatanJurusan(Request $request)
    {
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

        try {
            // Verifikasi bahwa user_id adalah PIC
            $user = UserModel::find($request->user_id);
            if ($user->level_id != 4) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Penanggung jawab harus memiliki level PIC'
                ], 422);
            }

            // Verifikasi surat belum digunakan
            $existingKegiatan = KegiatanJurusanModel::where('surat_id', $request->surat_id)->exists();
            if ($existingKegiatan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Surat tugas sudah digunakan untuk kegiatan lain'
                ], 422);
            }

            $kegiatan = KegiatanJurusanModel::create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Kegiatan jurusan berhasil ditambahkan',
                'data' => $kegiatan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Store Kegiatan Prodi
    public function storeKegiatanProdi(Request $request)
    {
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

        try {
            // Verifikasi bahwa user_id adalah PIC
            $user = UserModel::find($request->user_id);
            if ($user->level_id != 4) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Penanggung jawab harus memiliki level PIC'
                ], 422);
            }

            // Verifikasi surat belum digunakan
            $existingKegiatan = KegiatanProgramStudiModel::where('surat_id', $request->surat_id)->exists();
            if ($existingKegiatan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Surat tugas sudah digunakan untuk kegiatan lain'
                ], 422);
            }

            $kegiatan = KegiatanProgramStudiModel::create($request->all());

            return response()->json([
                'status' => 'success',
                'message' => 'Kegiatan program studi berhasil ditambahkan',
                'data' => $kegiatan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}