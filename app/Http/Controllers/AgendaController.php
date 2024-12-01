<?php

namespace App\Http\Controllers;

use App\Models\AgendaModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Exception;

class AgendaController extends Controller
{
    // Menampilkan data agenda untuk DataTables
    public function index(Request $request, $type, $id)
    {
        try {
            if (!in_array($type, ['jurusan', 'prodi'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tipe kegiatan tidak valid'
                ], 400);
            }

            // Cek keberadaan kegiatan dan akses user
            $kegiatan = $type === 'jurusan' 
                ? KegiatanJurusanModel::find($id)
                : KegiatanProgramStudiModel::find($id);

            if (!$kegiatan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kegiatan tidak ditemukan'
                ], 404);
            }

            // Verifikasi akses PIC
            if ($kegiatan->user_id !== auth()->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke kegiatan ini'
                ], 403);
            }

            // Query untuk DataTables
            $query = AgendaModel::where($type === 'jurusan' ? 'kegiatan_jurusan_id' : 'kegiatan_program_studi_id', $id)
                ->orderBy('tanggal_agenda', 'asc');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('tanggal_agenda', function ($agenda) {
                    return date('d-m-Y', strtotime($agenda->tanggal_agenda));
                })
                ->addColumn('dokumentasi', function ($agenda) {
                    if ($agenda->file_surat_agenda) {
                        return '<a href="' . Storage::url($agenda->file_surat_agenda) . '" 
                                class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-file-download"></i> Download
                            </a>';
                    }
                    return '-';
                })
                ->addColumn('action', function ($agenda) {
                    return '<div class="btn-group">
                        <button type="button" class="btn btn-warning btn-sm edit-agenda" 
                            data-id="'.$agenda->agenda_id.'"
                            data-nama="'.$agenda->nama_agenda.'"
                            data-tanggal="'.date('Y-m-d', strtotime($agenda->tanggal_agenda)).'"
                            data-deskripsi="'.$agenda->deskripsi.'">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm delete-agenda" 
                            data-id="'.$agenda->agenda_id.'">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>';
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

    // Menyimpan agenda baru
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'agenda' => 'required|array',
                'agenda.*.nama_agenda' => 'required|string|max:200',
                'agenda.*.tanggal_agenda' => 'required|date',
                'agenda.*.deskripsi' => 'required',
                'agenda.*.file_surat_agenda' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                'kegiatan_type' => 'required|in:jurusan,prodi',
                'kegiatan_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validasi tanggal agenda berada dalam rentang tanggal kegiatan
            $kegiatan = $request->kegiatan_type === 'jurusan'
                ? KegiatanJurusanModel::find($request->kegiatan_id)
                : KegiatanProgramStudiModel::find($request->kegiatan_id);

            if (!$kegiatan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Kegiatan tidak ditemukan'
                ], 404);
            }

            $agendas = [];
            foreach ($request->agenda as $agendaData) {
                // Validasi tanggal agenda
                $tanggalAgenda = strtotime($agendaData['tanggal_agenda']);
                $tanggalMulai = strtotime($kegiatan->tanggal_mulai);
                $tanggalSelesai = strtotime($kegiatan->tanggal_selesai);

                if ($tanggalAgenda < $tanggalMulai || $tanggalAgenda > $tanggalSelesai) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Tanggal agenda harus berada dalam rentang tanggal kegiatan'
                    ], 422);
                }

                // Proses upload file jika ada
                $path = null;
                if (isset($agendaData['file_surat_agenda']) && $agendaData['file_surat_agenda']) {
                    $file = $agendaData['file_surat_agenda'];
                    $path = $file->store('public/agenda_documents');
                }

                // Simpan agenda
                $agenda = new AgendaModel();
                $agenda->nama_agenda = $agendaData['nama_agenda'];
                $agenda->tanggal_agenda = $agendaData['tanggal_agenda'];
                $agenda->deskripsi = $agendaData['deskripsi'];
                $agenda->file_surat_agenda = $path;
                $agenda->user_id = auth()->id();
                
                if ($request->kegiatan_type === 'jurusan') {
                    $agenda->kegiatan_jurusan_id = $request->kegiatan_id;
                } else {
                    $agenda->kegiatan_program_studi_id = $request->kegiatan_id;
                }

                $agenda->save();
                $agendas[] = $agenda;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Agenda berhasil ditambahkan',
                'data' => $agendas
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Update agenda
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

            // Validasi akses PIC
            if ($agenda->user_id != auth()->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk mengubah agenda ini'
                ], 403);
            }

            // Validasi tanggal agenda
            $kegiatan = $agenda->kegiatan_jurusan_id 
                ? KegiatanJurusanModel::find($agenda->kegiatan_jurusan_id)
                : KegiatanProgramStudiModel::find($agenda->kegiatan_program_studi_id);

            $tanggalAgenda = strtotime($request->tanggal_agenda);
            $tanggalMulai = strtotime($kegiatan->tanggal_mulai);
            $tanggalSelesai = strtotime($kegiatan->tanggal_selesai);

            if ($tanggalAgenda < $tanggalMulai || $tanggalAgenda > $tanggalSelesai) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tanggal agenda harus berada dalam rentang tanggal kegiatan'
                ], 422);
            }

            // Update file jika ada
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

    // Hapus agenda
    public function destroy($id)
    {
        try {
            $agenda = AgendaModel::findOrFail($id);

            // Validasi akses PIC
            if ($agenda->user_id != auth()->id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menghapus agenda ini'
                ], 403);
            }

            // Hapus file jika ada
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
}