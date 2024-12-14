<?php

namespace App\Http\Controllers;

use App\Models\AgendaModel;
use App\Models\DokumentasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use Illuminate\Support\Facades\DB;

class AdminUpdateProgressAgendaController extends Controller
{
    public function index()
    {
        // Ambil data agenda dengan relasi yang dibutuhkan
        $agendas = AgendaModel::with([
            'kegiatanJurusan',
            'kegiatanProgramStudi',
            'dokumentasi',
            'users'
        ])->get();
    
        // Hitung progress untuk setiap agenda
        $agendas = $agendas->map(function($agenda) {
            $totalUsers = $agenda->users()->count();
            $uploadedUsers = DokumentasiModel::where('agenda_id', $agenda->agenda_id)
                                           ->distinct('user_id')
                                           ->count('user_id');
            
            $progressPercentage = $totalUsers > 0 ? 
                                round(($uploadedUsers / $totalUsers) * 100, 2) : 0;
            
            $agenda->setAttribute('progress', [
                'total_users' => $totalUsers,
                'uploaded_users' => $uploadedUsers,
                'percentage' => $progressPercentage
            ]);
            
            $agenda->setAttribute('display_status', $this->determineStatus($uploadedUsers, $totalUsers));
            return $agenda;
        });
    
        return view('admin.dosen.update-progress', [
            'agendas' => $agendas,
            'breadcrumb' => (object)[
                'title' => 'Update Progress Agenda',
                'list' => ['Home', 'Dosen', 'Update Progress Agenda']
            ]
        ]);
    }

    public function getDetailAgenda($id)
    {
        try {
            $agenda = AgendaModel::with([
                'kegiatanJurusan',
                'kegiatanProgramStudi',
                'users'
            ])->findOrFail($id);
    
            $userSubmissions = [];
            foreach ($agenda->users as $user) {
                $dokumentasi = DokumentasiModel::where('agenda_id', $id)
                                             ->where('user_id', $user->user_id)
                                             ->first();
                
                $userSubmissions[] = [
                    'user_id' => $user->user_id,
                    'user_name' => $user->nama,
                    'has_submitted' => !is_null($dokumentasi),
                    'submission_date' => $dokumentasi ? $dokumentasi->tanggal : null,
                    'dokumentasi' => $dokumentasi ? [
                        'id' => $dokumentasi->dokumentasi_id,
                        'nama' => $dokumentasi->nama_dokumentasi,
                        'deskripsi' => $dokumentasi->deskripsi_dokumentasi,
                        'file_name' => basename($dokumentasi->file_dokumentasi),
                        'tanggal' => $dokumentasi->tanggal
                    ] : null
                ];
            }
    
            return response()->json([
                'status' => 'success',
                'data' => [
                    'agenda' => $agenda,
                    'user_submissions' => $userSubmissions
                ]
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail agenda: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteProgress($id)
    {
        try {
            DB::beginTransaction();

            $dokumentasi = DokumentasiModel::findOrFail($id);
            
            // Hapus file fisik
            if (Storage::exists($dokumentasi->file_dokumentasi)) {
                Storage::delete($dokumentasi->file_dokumentasi);
            }

            // Hapus record dokumentasi
            $agendaId = $dokumentasi->agenda_id;
            $dokumentasi->delete();
            
            // Update status agenda
            $this->updateAgendaStatus($agendaId);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Dokumentasi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus dokumentasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadDokumentasi($id)
    {
        try {
            $agenda = AgendaModel::findOrFail($id);
            $dokumentasi = DokumentasiModel::where('agenda_id', $id)
                                         ->with('user')
                                         ->get();

            if ($dokumentasi->isEmpty()) {
                throw new \Exception('Tidak ada file dokumentasi untuk diunduh');
            }

            // Buat ZIP file
            $zipFileName = 'dokumentasi_' . $agenda->agenda_id . '.zip';
            $tempPath = storage_path('app/temp');
            $zipPath = $tempPath . '/' . $zipFileName;

            if (!File::exists($tempPath)) {
                File::makeDirectory($tempPath, 0755, true);
            }

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                throw new \Exception('Gagal membuat file zip');
            }

            foreach ($dokumentasi as $file) {
                if (Storage::exists($file->file_dokumentasi)) {
                    $originalFile = storage_path('app/' . $file->file_dokumentasi);
                    $fileName = $file->user->nama . '_' . basename($file->file_dokumentasi);
                    $zip->addFile($originalFile, $fileName);
                }
            }
            
            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh file: ' . $e->getMessage());
        }
    }

    private function determineStatus($uploadedUsers, $totalUsers)
    {
        if ($uploadedUsers === 0) return 'berlangsung';
        if ($uploadedUsers === $totalUsers) return 'selesai';
        return 'tahap penyelesaian';
    }

    private function updateAgendaStatus($agendaId)
    {
        $agenda = AgendaModel::findOrFail($agendaId);
        $totalUsers = $agenda->users()->count();
        $uploadedUsers = DokumentasiModel::where('agenda_id', $agendaId)
                                       ->distinct('user_id')
                                       ->count('user_id');

        $agenda->status_agenda = $this->determineStatus($uploadedUsers, $totalUsers);
        $agenda->save();
    }
}