<?php

namespace App\Http\Controllers;

use App\Models\AgendaModel;
use App\Models\DokumentasiModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AgendaController extends Controller
{
    public function index($type, $id)
    {
        try {
            $kegiatan = null;
            
            if ($type === 'jurusan') {
                $kegiatan = KegiatanJurusanModel::with('user')->findOrFail($id);
            } else if ($type === 'prodi') {
                $kegiatan = KegiatanProgramStudiModel::with('user')->findOrFail($id);
            }

            // Verifikasi apakah user adalah PIC dari kegiatan ini
            if ($kegiatan->user_id != session('user_id')) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kegiatan ini');
            }

            $agendas = AgendaModel::where($type . '_id', $id)
                                 ->orderBy('tanggal_agenda', 'asc')
                                 ->get();

            return response()->json([
                'status' => 'success',
                'data' => $agendas
            ]);

        } catch (\Exception $e) {
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
                'file_surat_agenda' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
                'kegiatan_type' => 'required|in:jurusan,prodi',
                'kegiatan_id' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Upload file dokumentasi jika ada
            $dokumentasiId = null;
            if ($request->hasFile('file_surat_agenda')) {
                $file = $request->file('file_surat_agenda');
                $path = $file->store('public/agenda_documents');
                
                $dokumentasi = DokumentasiModel::create([
                    'nama_dokumentasi' => $file->getClientOriginalName(),
                    'file_dokumentasi' => $path,
                    'tanggal' => now()
                ]);
                
                $dokumentasiId = $dokumentasi->dokumentasi_id;
            }

            // Menyimpan agenda
            $agenda = new AgendaModel();
            $agenda->nama_agenda = $request->nama_agenda;
            $agenda->tanggal_agenda = $request->tanggal_agenda;
            $agenda->deskripsi = $request->deskripsi;
            $agenda->dokumentasi_id = $dokumentasiId;
            $agenda->user_id = session('user_id');
            
            // Set foreign key berdasarkan tipe kegiatan
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

        } catch (\Exception $e) {
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

            // Verifikasi apakah user adalah PIC dari agenda ini
            if ($agenda->user_id != session('user_id')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk mengubah agenda ini'
                ], 403);
            }

            // Update file dokumentasi jika ada
            if ($request->hasFile('file_surat_agenda')) {
                // Hapus file lama jika ada
                if ($agenda->dokumentasi_id) {
                    $oldDokumentasi = DokumentasiModel::find($agenda->dokumentasi_id);
                    if ($oldDokumentasi) {
                        Storage::delete($oldDokumentasi->file_dokumentasi);
                        $oldDokumentasi->delete();
                    }
                }

                // Upload file baru
                $file = $request->file('file_surat_agenda');
                $path = $file->store('public/agenda_documents');
                
                $dokumentasi = DokumentasiModel::create([
                    'nama_dokumentasi' => $file->getClientOriginalName(),
                    'file_dokumentasi' => $path,
                    'tanggal' => now()
                ]);
                
                $agenda->dokumentasi_id = $dokumentasi->dokumentasi_id;
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

        } catch (\Exception $e) {
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

            // Verifikasi apakah user adalah PIC dari agenda ini
            if ($agenda->user_id != session('user_id')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menghapus agenda ini'
                ], 403);
            }

            // Hapus file dokumentasi jika ada
            if ($agenda->dokumentasi_id) {
                $dokumentasi = DokumentasiModel::find($agenda->dokumentasi_id);
                if ($dokumentasi) {
                    Storage::delete($dokumentasi->file_dokumentasi);
                    $dokumentasi->delete();
                }
            }

            $agenda->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Agenda berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}