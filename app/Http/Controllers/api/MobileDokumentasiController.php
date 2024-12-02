<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgendaModel;
use App\Models\DokumentasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MobileDokumentasiController extends Controller
{
    public function getAgendaList()
    {
        try {
            $agendas = AgendaModel::select(
                't_agenda.*',
                't_kegiatan_jurusan.nama_kegiatan_jurusan',
                't_kegiatan_program_studi.nama_kegiatan_program_studi'
            )
            ->where('t_agenda.user_id', Auth::id())
            ->leftJoin('t_kegiatan_jurusan', 't_agenda.kegiatan_jurusan_id', '=', 't_kegiatan_jurusan.kegiatan_jurusan_id')
            ->leftJoin('t_kegiatan_program_studi', 't_agenda.kegiatan_program_studi_id', '=', 't_kegiatan_program_studi.kegiatan_program_studi_id')
            ->get()
            ->map(function ($agenda) {
                return [
                    'agenda_id' => $agenda->agenda_id,
                    'nama_agenda' => $agenda->nama_agenda,
                    'nama_kegiatan' => $agenda->nama_kegiatan_jurusan ?? $agenda->nama_kegiatan_program_studi ?? '-',
                    'tanggal_agenda' => $agenda->tanggal_agenda,
                    'status' => $agenda->dokumentasi_id ? 'Selesai' : 'Berlangsung',
                    'has_dokumentasi' => (bool)$agenda->dokumentasi_id
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data agenda berhasil diambil',
                'data' => $agendas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDokumentasi($id)
    {
        try {
            $agenda = AgendaModel::with('dokumentasi')
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            if ($agenda->dokumentasi) {
                $dokumentasi = [
                    'dokumentasi_id' => $agenda->dokumentasi->dokumentasi_id,
                    'nama_dokumentasi' => $agenda->dokumentasi->nama_dokumentasi,
                    'deskripsi_dokumentasi' => $agenda->dokumentasi->deskripsi_dokumentasi,
                    'file_url' => Storage::url($agenda->dokumentasi->file_dokumentasi),
                    'tanggal' => $agenda->dokumentasi->tanggal
                ];
            } else {
                $dokumentasi = null;
            }

            return response()->json([
                'status' => 'success',
                'data' => $dokumentasi
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
        $request->validate([
            'agenda_id' => 'required|exists:t_agenda,agenda_id',
            'deskripsi_dokumentasi' => 'required|string',
            'file_dokumentasi' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx'
        ]);

        DB::beginTransaction();
        try {
            $agenda = AgendaModel::findOrFail($request->agenda_id);
            
            if ($agenda->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk agenda ini'
                ], 403);
            }

            $filePath = $request->file('file_dokumentasi')->store('dokumentasi');

            $dokumentasi = DokumentasiModel::create([
                'nama_dokumentasi' => $agenda->nama_agenda,
                'deskripsi_dokumentasi' => $request->deskripsi_dokumentasi,
                'file_dokumentasi' => $filePath,
                'tanggal' => now()
            ]);

            $agenda->dokumentasi_id = $dokumentasi->dokumentasi_id;
            $agenda->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Dokumentasi berhasil ditambahkan',
                'data' => [
                    'dokumentasi_id' => $dokumentasi->dokumentasi_id,
                    'file_url' => Storage::url($filePath)
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'deskripsi_dokumentasi' => 'required|string',
            'file_dokumentasi' => 'nullable|file|max:10240|mimes:pdf,doc,docx,xls,xlsx'
        ]);

        DB::beginTransaction();
        try {
            $agenda = AgendaModel::with('dokumentasi')->findOrFail($id);
            
            if ($agenda->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk agenda ini'
                ], 403);
            }

            $fileUrl = null;
            if ($request->hasFile('file_dokumentasi')) {
                Storage::delete($agenda->dokumentasi->file_dokumentasi);
                $filePath = $request->file('file_dokumentasi')->store('dokumentasi');
                $agenda->dokumentasi->file_dokumentasi = $filePath;
                $fileUrl = Storage::url($filePath);
            }

            $agenda->dokumentasi->deskripsi_dokumentasi = $request->deskripsi_dokumentasi;
            $agenda->dokumentasi->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Dokumentasi berhasil diperbarui',
                'data' => [
                    'dokumentasi_id' => $agenda->dokumentasi->dokumentasi_id,
                    'file_url' => $fileUrl
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $agenda = AgendaModel::with('dokumentasi')->findOrFail($id);
            
            if ($agenda->user_id !== Auth::id()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk agenda ini'
                ], 403);
            }

            Storage::delete($agenda->dokumentasi->file_dokumentasi);
            
            $dokumentasiId = $agenda->dokumentasi_id;
            $agenda->dokumentasi_id = null;
            $agenda->save();
            
            DokumentasiModel::destroy($dokumentasiId);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Dokumentasi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}