<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\AgendaModel;
use App\Models\DokumentasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UpdateProgressAgendaController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        
        $agendas = AgendaModel::where('user_id', $userId)
                    ->where('status_agenda', 'berlangsung')
                    ->with(['kegiatanJurusan', 'kegiatanProgramStudi'])
                    ->get();
    
        return view('dosen.update', [  // Diubah dari 'dosen.update-progress'
            'agendas' => $agendas,
            'breadcrumb' => (object)[
                'title' => 'Update Progress Agenda',
                'list' => ['Home', 'Update Progress Agenda']
            ]
        ]);
    }

    public function getDetailAgenda($id)
    {
        $agenda = AgendaModel::with(['kegiatanJurusan', 'kegiatanProgramStudi'])
                    ->findOrFail($id);
                    
        return response()->json($agenda);
    }

    public function updateProgress(Request $request, $id)
    {
        $request->validate([
            'nama_dokumentasi' => 'required|string|max:200',
            'deskripsi_dokumentasi' => 'required|string',
            'file_dokumentasi' => 'required|file|max:10240', // Max 10MB
            'tanggal' => 'required|date'
        ]);

        try {
            // Upload file dokumentasi
            $file = $request->file('file_dokumentasi');
            $path = $file->store('public/dokumentasi');

            // Buat dokumentasi baru
            $dokumentasi = DokumentasiModel::create([
                'nama_dokumentasi' => $request->nama_dokumentasi,
                'deskripsi_dokumentasi' => $request->deskripsi_dokumentasi,
                'file_dokumentasi' => $path,
                'tanggal' => $request->tanggal
            ]);

            // Update agenda
            $agenda = AgendaModel::findOrFail($id);
            $agenda->update([
                'dokumentasi_id' => $dokumentasi->dokumentasi_id,
                'status_agenda' => 'selesai'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Progress agenda berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}