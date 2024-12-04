<?php

namespace App\Http\Controllers;

use App\Models\AgendaModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; // Tambahkan import ini
use Yajra\DataTables\Facades\DataTables;
use Exception;

class AgendaController extends Controller
{
    // Method index tetap sama
    
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'agenda' => 'required|array',
                'agenda.*.nama_agenda' => 'required|string|max:200',
                'agenda.*.tanggal_agenda' => 'required|date',
                'agenda.*.deskripsi' => 'required|string',
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

            DB::beginTransaction();
            try {
                foreach ($request->agenda as $agendaData) {
                    $newAgenda = new AgendaModel();
                    $newAgenda->nama_agenda = $agendaData['nama_agenda'];
                    $newAgenda->tanggal_agenda = $agendaData['tanggal_agenda'];
                    $newAgenda->deskripsi = $agendaData['deskripsi'];
                    $newAgenda->user_id = auth()->id();
                    
                    // Set kegiatan ID berdasarkan type
                    if ($request->kegiatan_type === 'jurusan') {
                        $newAgenda->kegiatan_jurusan_id = $request->kegiatan_id;
                    } else {
                        $newAgenda->kegiatan_program_studi_id = $request->kegiatan_id;
                    }

                    // Handle file upload jika ada
                    if (isset($agendaData['file_surat_agenda']) && $agendaData['file_surat_agenda']) {
                        $path = $agendaData['file_surat_agenda']->store('agenda_documents', 'public');
                        $newAgenda->file_surat_agenda = $path;
                    }

                    $newAgenda->save();
                }

                DB::commit();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Agenda berhasil disimpan'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadDocument($id)
{
    try {
        $agenda = AgendaModel::findOrFail($id);
        
        if (!$agenda->file_surat_agenda) {
            return response()->json([
                'status' => 'error',
                'message' => 'Dokumen tidak ditemukan'
            ], 404);
        }

        $path = storage_path('app/public/' . $agenda->file_surat_agenda);
        
        if (!file_exists($path)) {
            return response()->json([
                'status' => 'error',
                'message' => 'File tidak ditemukan'
            ], 404);
        }

        return response()->download($path);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

    // Method update dan destroy tetap sama
}