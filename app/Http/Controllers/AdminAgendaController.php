<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AgendaModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use App\Models\KegiatanInstitusiModel;
use App\Models\KegiatanLuarInstitusiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Exception;
use Illuminate\Support\Facades\Log;

class AdminAgendaController extends Controller
{
    public function index()
    {
        return view('admin.dosen.agenda.agenda-setting', [
            'breadcrumb' => (object)[
                'title' => 'Manajemen Agenda',
                'list' => ['Home', 'Dosen', 'Agenda', 'Manajemen']
            ]
        ]);
    }

    public function getAgendaList(Request $request)
    {
        try {
            $query = AgendaModel::with([
                'user', 
                'kegiatanJurusan', 
                'kegiatanProgramStudi',
                'kegiatanInstitusi', 
                'kegiatanLuarInstitusi' 
            ])->orderBy('tanggal_agenda', 'desc');
    
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_kegiatan', function ($agenda) {
                    if ($agenda->kegiatanJurusan) {
                        return $agenda->kegiatanJurusan->nama_kegiatan_jurusan;
                    } elseif ($agenda->kegiatanProgramStudi) {
                        return $agenda->kegiatanProgramStudi->nama_kegiatan_program_studi;
                    } elseif ($agenda->kegiatanInstitusi) {
                        return $agenda->kegiatanInstitusi->nama_kegiatan_institusi;
                    } elseif ($agenda->kegiatanLuarInstitusi) {
                        return $agenda->kegiatanLuarInstitusi->nama_kegiatan_luar_institusi;
                    }
                    return '-';
                })
                ->addColumn('pic', function ($agenda) {
                    return $agenda->user ? $agenda->user->nama_user : '-';
                })
                ->addColumn('tanggal_agenda', function ($agenda) {
                    return date('d-m-Y', strtotime($agenda->tanggal_agenda));
                })
                ->addColumn('dokumen', function ($agenda) {
                    if ($agenda->file_surat_agenda) {
                        return '<a href="' . route('admin.dosen.agenda.download', $agenda->agenda_id) . '" 
                                class="btn btn-info btn-sm">
                                <i class="fas fa-download"></i> Download
                            </a>';
                    }
                    return '-';
                })
                ->addColumn('aksi', function ($agenda) {
                    // Ambil tanggal dari kegiatan yang sesuai
                    $tanggalMulai = null;
                    $tanggalSelesai = null;
                    
                    if ($agenda->kegiatanJurusan) {
                        $tanggalMulai = $agenda->kegiatanJurusan->tanggal_mulai;
                        $tanggalSelesai = $agenda->kegiatanJurusan->tanggal_selesai;
                    } elseif ($agenda->kegiatanProgramStudi) {
                        $tanggalMulai = $agenda->kegiatanProgramStudi->tanggal_mulai;
                        $tanggalSelesai = $agenda->kegiatanProgramStudi->tanggal_selesai;
                    } elseif ($agenda->kegiatanInstitusi) {
                        $tanggalMulai = $agenda->kegiatanInstitusi->tanggal_mulai;
                        $tanggalSelesai = $agenda->kegiatanInstitusi->tanggal_selesai;
                    } elseif ($agenda->kegiatanLuarInstitusi) {
                        $tanggalMulai = $agenda->kegiatanLuarInstitusi->tanggal_mulai;
                        $tanggalSelesai = $agenda->kegiatanLuarInstitusi->tanggal_selesai;
                    }
    
                    return '<div class="btn-group">
                            <button type="button" class="btn btn-warning btn-sm edit-agenda" 
                                data-id="' . $agenda->agenda_id . '"
                                data-nama="' . $agenda->nama_agenda . '"
                                data-tanggal="' . date('Y-m-d', strtotime($agenda->tanggal_agenda)) . '"
                                data-deskripsi="' . $agenda->deskripsi . '"
                                data-tanggal_mulai="' . date('Y-m-d', strtotime($tanggalMulai)) . '"
                                data-tanggal_selesai="' . date('Y-m-d', strtotime($tanggalSelesai)) . '">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-agenda" 
                                data-id="' . $agenda->agenda_id . '">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </div>';
                })
                ->rawColumns(['dokumen', 'aksi'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_agenda' => 'required|string|max:200',
                'tanggal_agenda' => 'required|date',
                'deskripsi' => 'required',
                'file_surat_agenda' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
                'kegiatan_type' => 'required|in:jurusan,prodi,institusi,luar_institusi',
                'kegiatan_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validasi keberadaan kegiatan dan rentang tanggal
            $kegiatan = null;
            switch ($request->kegiatan_type) {
                case 'jurusan':
                    $kegiatan = KegiatanJurusanModel::find($request->kegiatan_id);
                    break;
                case 'prodi':
                    $kegiatan = KegiatanProgramStudiModel::find($request->kegiatan_id);
                    break;
                case 'institusi':
                    $kegiatan = KegiatanInstitusiModel::find($request->kegiatan_id);
                    break;
                case 'luar_institusi':
                    $kegiatan = KegiatanLuarInstitusiModel::find($request->kegiatan_id);
                    break;
            }

            if (!$kegiatan) {
                throw new Exception('Kegiatan tidak ditemukan');
            }

            // Validasi tanggal
            $tanggalAgenda = strtotime($request->tanggal_agenda);
            $tanggalMulai = strtotime($kegiatan->tanggal_mulai);
            $tanggalSelesai = strtotime($kegiatan->tanggal_selesai);

            if ($tanggalAgenda < $tanggalMulai || $tanggalAgenda > $tanggalSelesai) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tanggal agenda harus berada dalam rentang tanggal kegiatan'
                ], 422);
            }

            // Buat agenda baru
            $agenda = new AgendaModel();
            $agenda->nama_agenda = $request->nama_agenda;
            $agenda->tanggal_agenda = $request->tanggal_agenda;
            $agenda->deskripsi = $request->deskripsi;

            // Upload file jika ada
            if ($request->hasFile('file_surat_agenda')) {
                $file = $request->file('file_surat_agenda');
                $fileName = $file->getClientOriginalName();
                $filePath = $file->storeAs('public/agenda_files', $fileName);
                
                if (!$filePath) {
                    throw new Exception('Gagal mengupload file');
                }
                
                $agenda->file_surat_agenda = $filePath;
            }

            // Set foreign key berdasarkan tipe kegiatan
            switch ($request->kegiatan_type) {
                case 'jurusan':
                    $agenda->kegiatan_jurusan_id = $request->kegiatan_id;
                    break;
                case 'prodi':
                    $agenda->kegiatan_program_studi_id = $request->kegiatan_id;
                    break;
                case 'institusi':
                    $agenda->kegiatan_institusi_id = $request->kegiatan_id;
                    break;
                case 'luar_institusi':
                    $agenda->kegiatan_luar_institusi_id = $request->kegiatan_id;
                    break;
            }

            $agenda->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Agenda berhasil ditambahkan',
                'data' => $agenda
            ]);

        } catch (Exception $e) {
            // Hapus file yang sudah terupload jika terjadi error
            if (isset($filePath) && Storage::exists($filePath)) {
                Storage::delete($filePath);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nama_agenda' => 'required|string|max:200',
                'tanggal_agenda' => 'nullable|date',
                'deskripsi' => 'required',
                'file_surat_agenda' => 'nullable|file|mimes:pdf,doc,docx|max:5120', 
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $agenda = AgendaModel::findOrFail($id);

            $agenda->nama_agenda = $request->nama_agenda;
            // Hanya update tanggal jika ada input baru
            if ($request->filled('tanggal_agenda')) {
                $agenda->tanggal_agenda = $request->tanggal_agenda;
            }
            $agenda->deskripsi = $request->deskripsi;

            // Upload file baru jika ada
            if ($request->hasFile('file_surat_agenda')) {
                $file = $request->file('file_surat_agenda');
                
                // Validasi tipe file secara manual
                $allowedMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                if (!in_array($file->getMimeType(), $allowedMimes)) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Tipe file tidak diizinkan. Hanya file PDF dan DOC/DOCX yang diperbolehkan.'
                    ], 422);
                }

                // Generate nama file yang aman
                $fileName = $file->getClientOriginalName();
                
                // Upload file baru
                $newFilePath = $file->storeAs('public/agenda_files', $fileName);
                
                if (!$newFilePath) {
                    throw new Exception('Gagal mengupload file');
                }

                // Hapus file lama jika ada
                if ($agenda->file_surat_agenda && Storage::exists($agenda->file_surat_agenda)) {
                    Storage::delete($agenda->file_surat_agenda);
                }

                $agenda->file_surat_agenda = $newFilePath;
            }

            // Update data agenda
            $agenda->nama_agenda = $request->nama_agenda;

            $agenda->deskripsi = $request->deskripsi;

            // Validasi tanggal agenda dengan kegiatan terkait


            $agenda->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Agenda berhasil diperbarui',
                'data' => $agenda
            ]);

        } catch (Exception $e) {
            // Hapus file yang baru diupload jika terjadi error
            if (isset($newFilePath) && Storage::exists($newFilePath)) {
                Storage::delete($newFilePath);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $agenda = AgendaModel::findOrFail($id);

            if ($agenda->file_surat_agenda) {
                Storage::delete($agenda->file_surat_agenda);
            }

            $agenda->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Agenda berhasil dihapus'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download($id)
    {
        try {
            $agenda = AgendaModel::findOrFail($id);
            
            if (!$agenda->file_surat_agenda) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'File tidak ditemukan'
                ], 404);
            }

            return Storage::download($agenda->file_surat_agenda);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getKegiatan(Request $request)
    {
        try {
            Log::info('Type yang diterima:', ['type' => $request->type]); // Tambahkan log
            
            $kegiatan = [];
            switch ($request->type) {
                case 'jurusan':
                    $kegiatan = KegiatanJurusanModel::select('kegiatan_jurusan_id as id', 'nama_kegiatan_jurusan', 'tanggal_mulai', 'tanggal_selesai')
                        ->orderBy('nama_kegiatan_jurusan')
                        ->get();
                    break;
                case 'prodi':
                    $kegiatan = KegiatanProgramStudiModel::select('kegiatan_program_studi_id as id', 'nama_kegiatan_program_studi', 'tanggal_mulai', 'tanggal_selesai')
                        ->orderBy('nama_kegiatan_program_studi')
                        ->get();
                    break;
                case 'institusi':
                    $kegiatan = KegiatanInstitusiModel::select('kegiatan_institusi_id as id', 'nama_kegiatan_institusi', 'tanggal_mulai', 'tanggal_selesai')
                        ->orderBy('nama_kegiatan_institusi')
                        ->get();
                    break;
                case 'luar_institusi':
                    $kegiatan = KegiatanLuarInstitusiModel::select('kegiatan_luar_institusi_id as id', 'nama_kegiatan_luar_institusi', 'tanggal_mulai', 'tanggal_selesai')
                        ->orderBy('nama_kegiatan_luar_institusi')
                        ->get();
                    break;
            }
    
            Log::info('Data kegiatan:', ['kegiatan' => $kegiatan]); // Tambahkan log
            
            return response()->json(['status' => 'success', 'data' => $kegiatan]);
        } catch (Exception $e) {
            Log::error('Error:', ['message' => $e->getMessage()]); // Tambahkan log
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

}