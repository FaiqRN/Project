<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AgendaController extends Controller
{
    public function index()
    {
        $kegiatanJurusan = KegiatanJurusanModel::where('user_id', auth()->id())
            ->orderBy('tanggal_mulai', 'desc')
            ->first();
            
        $kegiatanProdi = KegiatanProgramStudiModel::where('user_id', auth()->id())
            ->orderBy('tanggal_mulai', 'desc')
            ->first();

        $kegiatanInstitusi = KegiatanInstitusiModel::where('user_id', auth()->id())
            ->orderBy('tanggal_mulai', 'desc')
            ->first();

        $kegiatanLuarInstitusi = KegiatanLuarInstitusiModel::where('user_id', auth()->id())
            ->orderBy('tanggal_mulai', 'desc')
            ->first();

        return view('dosen.agenda.index', [
            'kegiatanJurusan' => $kegiatanJurusan,
            'kegiatanProdi' => $kegiatanProdi,
            'kegiatanInstitusi' => $kegiatanInstitusi,
            'kegiatanLuarInstitusi' => $kegiatanLuarInstitusi,
            'breadcrumb' => (object)[
                'title' => 'Detail Kegiatan',
                'list' => ['Home', 'Agenda', 'Detail']
            ]
        ]);
    }

    public function getAgendaList(Request $request, $type, $id)
    {
        try {
            if (!in_array($type, ['jurusan', 'prodi', 'institusi', 'luar_institusi'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tipe kegiatan tidak valid'
                ], 400);
            }

            $kegiatan = match ($type) {
                'jurusan' => KegiatanJurusanModel::find($id),
                'prodi' => KegiatanProgramStudiModel::find($id),
                'institusi' => KegiatanInstitusiModel::find($id),
                'luar_institusi' => KegiatanLuarInstitusiModel::find($id),
            };

            if (!$kegiatan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kegiatan tidak ditemukan'
                ], 404);
            }

            if ($kegiatan->user_id !== auth()->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke kegiatan ini'
                ], 403);
            }

            $columnMap = [
                'jurusan' => 'kegiatan_jurusan_id',
                'prodi' => 'kegiatan_program_studi_id',
                'institusi' => 'kegiatan_institusi_id',
                'luar_institusi' => 'kegiatan_luar_institusi_id'
            ];

            $query = AgendaModel::where($columnMap[$type], $id)
                ->orderBy('tanggal_agenda', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('tanggal_agenda', function ($agenda) {
                    return date('d-m-Y', strtotime($agenda->tanggal_agenda));
                })
                ->addColumn('dokumentasi', function ($agenda) {
                    if ($agenda->file_surat_agenda) {
                        return sprintf(
                            '<a href="%s" class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-file-download"></i> Download
                            </a>',
                            Storage::url($agenda->file_surat_agenda)
                        );
                    }
                    return '-';
                })
                ->addColumn('action', function ($agenda) {
                    return sprintf(
                        '<div class="btn-group">
                            <button type="button" class="btn btn-warning btn-sm edit-agenda" 
                                data-id="%s"
                                data-nama="%s"
                                data-tanggal="%s"
                                data-deskripsi="%s">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm delete-agenda" 
                                data-id="%s">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>',
                        $agenda->agenda_id,
                        $agenda->nama_agenda,
                        date('Y-m-d', strtotime($agenda->tanggal_agenda)),
                        $agenda->deskripsi,
                        $agenda->agenda_id
                    );
                })
                ->rawColumns(['dokumentasi', 'action'])
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
            DB::beginTransaction();
            
            $validator = Validator::make($request->all(), [
                'agenda' => 'required|array',
                'agenda.*.nama_agenda' => 'required|string|max:200',
                'agenda.*.tanggal_agenda' => 'required|date',
                'agenda.*.deskripsi' => 'required|string',
                'agenda.*.file_surat_agenda' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                'kegiatan_type' => 'required|in:jurusan,prodi,institusi,luar_institusi',
                'kegiatan_id' => 'required|integer'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
    
            foreach ($request->agenda as $item) {
                $agenda = new AgendaModel();
                $agenda->nama_agenda = $item['nama_agenda'];
                $agenda->tanggal_agenda = $item['tanggal_agenda'];
                $agenda->deskripsi = $item['deskripsi'];
                $agenda->user_id = auth()->id();
    
                // Set kegiatan ID
                match ($request->kegiatan_type) {
                    'jurusan' => $agenda->kegiatan_jurusan_id = $request->kegiatan_id,
                    'prodi' => $agenda->kegiatan_program_studi_id = $request->kegiatan_id,
                    'institusi' => $agenda->kegiatan_institusi_id = $request->kegiatan_id,
                    'luar_institusi' => $agenda->kegiatan_luar_institusi_id = $request->kegiatan_id,
                };
    
                // Handle file upload
                if (isset($item['file_surat_agenda']) && $item['file_surat_agenda'] instanceof \Illuminate\Http\UploadedFile) {
                    $file = $item['file_surat_agenda'];
                    $filename =Str::slug($file->getClientOriginalName()) . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('public/agenda_files', $filename);
                    if (!$path) {
                        throw new \Exception('Gagal mengupload file');
                    }
                    $agenda->file_surat_agenda = $path;
                }
    
                if (!$agenda->save()) {
                    throw new \Exception('Gagal menyimpan agenda');
                }
            }
    
            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Agenda berhasil disimpan']);
    
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing agenda: ' . $e->getMessage());
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
                'tanggal_agenda' => 'required|date',
                'deskripsi' => 'required',
                'file_surat_agenda' => 'nullable|file|mimes:pdf,doc,docx|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $agenda = AgendaModel::findOrFail($id);

            if ($agenda->user_id != auth()->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk mengubah agenda ini'
                ], 403);
            }

            $kegiatan = match (true) {
                !is_null($agenda->kegiatan_jurusan_id) => KegiatanJurusanModel::find($agenda->kegiatan_jurusan_id),
                !is_null($agenda->kegiatan_program_studi_id) => KegiatanProgramStudiModel::find($agenda->kegiatan_program_studi_id),
                !is_null($agenda->kegiatan_institusi_id) => KegiatanInstitusiModel::find($agenda->kegiatan_institusi_id),
                !is_null($agenda->kegiatan_luar_institusi_id) => KegiatanLuarInstitusiModel::find($agenda->kegiatan_luar_institusi_id),
            };

            $tanggalAgenda = strtotime($request->tanggal_agenda);
            $tanggalMulai = strtotime($kegiatan->tanggal_mulai);
            $tanggalSelesai = strtotime($kegiatan->tanggal_selesai);

            if ($tanggalAgenda < $tanggalMulai || $tanggalAgenda > $tanggalSelesai) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tanggal agenda harus berada dalam rentang tanggal kegiatan'
                ], 422);
            }

            if ($request->hasFile('file_surat_agenda')) {
                if ($agenda->file_surat_agenda) {
                    Storage::delete($agenda->file_surat_agenda);
                }
                $path = $request->file('file_surat_agenda')->store('public/agenda_documents');
                $agenda->file_surat_agenda = $path;
            }

            $agenda->nama_agenda = $request->nama_agenda;
            $agenda->tanggal_agenda = $request->tanggal_agenda;
            $agenda->deskripsi = $request->deskripsi;
            $agenda->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Agenda berhasil diperbarui',
                'data' => $agenda
            ]);

        } catch (Exception $e) {
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

            if ($agenda->user_id != auth()->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menghapus agenda ini'
                ], 403);
            }

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

    public function showKegiatanPIC()
    {
        // Get active activities for current user
        $user_id = Auth::id();
        
        $kegiatanJurusan = KegiatanJurusanModel::where('user_id', $user_id)
            ->where('status_kegiatan', 'berlangsung')
            ->with(['user', 'surat'])
            ->first();

        $kegiatanProdi = KegiatanProgramStudiModel::where('user_id', $user_id)
            ->where('status_kegiatan', 'berlangsung')
            ->with(['user', 'surat'])
            ->first();

        $kegiatanInstitusi = KegiatanInstitusiModel::where('user_id', $user_id)
            ->where('status_kegiatan', 'berlangsung')
            ->with(['user', 'surat'])
            ->first();

        $kegiatanLuarInstitusi = KegiatanLuarInstitusiModel::where('user_id', $user_id)
            ->where('status_kegiatan', 'berlangsung')
            ->with(['user', 'surat'])
            ->first();

        return view('pic.kegiatan', [
            'kegiatanJurusan' => $kegiatanJurusan,
            'kegiatanProdi' => $kegiatanProdi,
            'kegiatanInstitusi' => $kegiatanInstitusi,
            'kegiatanLuarInstitusi' => $kegiatanLuarInstitusi,
            'breadcrumb' => (object)[
                'title' => 'Detail Kegiatan',
                'list' => ['Home', 'Kegiatan', 'Detail']
            ]
        ]);
    }
}