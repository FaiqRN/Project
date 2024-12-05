<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AgendaModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Exception;

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
            $query = AgendaModel::with(['user', 'kegiatanJurusan', 'kegiatanProgramStudi'])
                ->orderBy('tanggal_agenda', 'desc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nama_kegiatan', function ($agenda) {
                    if ($agenda->kegiatanJurusan) {
                        return $agenda->kegiatanJurusan->nama_kegiatan_jurusan;
                    } elseif ($agenda->kegiatanProgramStudi) {
                        return $agenda->kegiatanProgramStudi->nama_kegiatan_program_studi;
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
                // Di dalam fungsi getAgendaList controller
                ->addColumn('aksi', function ($agenda) {
                    $tanggalMulai = $agenda->kegiatanJurusan ? $agenda->kegiatanJurusan->tanggal_mulai : 
                                    ($agenda->kegiatanProgramStudi ? $agenda->kegiatanProgramStudi->tanggal_mulai : null);
                    $tanggalSelesai = $agenda->kegiatanJurusan ? $agenda->kegiatanJurusan->tanggal_selesai : 
                                      ($agenda->kegiatanProgramStudi ? $agenda->kegiatanProgramStudi->tanggal_selesai : null);
                    
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
                'kegiatan_type' => 'required|in:jurusan,prodi',
                'kegiatan_id' => 'required|integer'
            ], [
                'nama_agenda.required' => 'Nama agenda harus diisi',
                'nama_agenda.max' => 'Nama agenda maksimal 200 karakter',
                'tanggal_agenda.required' => 'Tanggal agenda harus diisi',
                'tanggal_agenda.date' => 'Format tanggal tidak valid',
                'deskripsi.required' => 'Deskripsi harus diisi',
                'file_surat_agenda.mimes' => 'File harus berformat PDF, DOC, atau DOCX',
                'file_surat_agenda.max' => 'Ukuran file maksimal 5MB',
                'kegiatan_type.required' => 'Tipe kegiatan harus dipilih',
                'kegiatan_type.in' => 'Tipe kegiatan tidak valid',
                'kegiatan_id.required' => 'Kegiatan harus dipilih',
                'kegiatan_id.integer' => 'ID kegiatan tidak valid'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validasi keberadaan kegiatan dan rentang tanggal
            if ($request->kegiatan_type === 'jurusan') {
                $kegiatan = KegiatanJurusanModel::find($request->kegiatan_id);
                if (!$kegiatan) {
                    throw new Exception('Kegiatan jurusan tidak ditemukan');
                }
            } else {
                $kegiatan = KegiatanProgramStudiModel::find($request->kegiatan_id);
                if (!$kegiatan) {
                    throw new Exception('Kegiatan program studi tidak ditemukan');
                }
            }

            // Validasi tanggal agenda berada dalam rentang tanggal kegiatan
            $tanggalAgenda = strtotime($request->tanggal_agenda);
            $tanggalMulai = strtotime($kegiatan->tanggal_mulai);
            $tanggalSelesai = strtotime($kegiatan->tanggal_selesai);

            if ($tanggalAgenda < $tanggalMulai || $tanggalAgenda > $tanggalSelesai) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tanggal agenda harus berada dalam rentang tanggal kegiatan (' . 
                                date('d-m-Y', $tanggalMulai) . ' sampai ' . 
                                date('d-m-Y', $tanggalSelesai) . ')'
                ], 422);
            }

            $agenda = new AgendaModel();
            $agenda->nama_agenda = $request->nama_agenda;
            $agenda->tanggal_agenda = $request->tanggal_agenda;
            $agenda->deskripsi = $request->deskripsi;

            // Upload file jika ada
            if ($request->hasFile('file_surat_agenda')) {
                $file = $request->file('file_surat_agenda');
                $fileName = 'agenda_' . time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('public/agenda_files', $fileName);
                
                if (!$filePath) {
                    throw new Exception('Gagal mengupload file');
                }
                
                $agenda->file_surat_agenda = $filePath;
            }

            // Set kegiatan ID berdasarkan tipe
            if ($request->kegiatan_type === 'jurusan') {
                $agenda->kegiatan_jurusan_id = $request->kegiatan_id;
            } else {
                $agenda->kegiatan_program_studi_id = $request->kegiatan_id;
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
                $fileName = 'agenda_' . time() . '_' . $file->getClientOriginalName();
                
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
            if ($request->type === 'jurusan') {
                $kegiatan = KegiatanJurusanModel::select('kegiatan_jurusan_id as id', 
                    'nama_kegiatan_jurusan', 'tanggal_mulai', 'tanggal_selesai')
                    ->orderBy('nama_kegiatan_jurusan')
                    ->get();
            } else {
                $kegiatan = KegiatanProgramStudiModel::select('kegiatan_program_studi_id as id', 
                    'nama_kegiatan_program_studi', 'tanggal_mulai', 'tanggal_selesai')
                    ->orderBy('nama_kegiatan_program_studi')
                    ->get();
            }

            return response()->json(['status' => 'success', 'data' => $kegiatan]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

}