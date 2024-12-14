<?php

namespace App\Http\Controllers;

use App\Models\AgendaModel;
use App\Models\DokumentasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class AdminUpdateProgressAgendaController extends Controller
{
    public function index()
    {
        $agendas = AgendaModel::with([
            'kegiatanJurusan',
            'kegiatanProgramStudi',
            'dokumentasi',
            'users'
        ])->get();
    
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
            Log::info('Getting agenda detail for ID: ' . $id);
            
            $agenda = AgendaModel::with([
                'kegiatanJurusan',
                'kegiatanProgramStudi',
                'dokumentasi.user',
                'users'
            ])->findOrFail($id);
            
            Log::info('Agenda found:', ['agenda' => $agenda->toArray()]);
    
            $userSubmissions = [];
            foreach ($agenda->users as $user) {
                $dokumentasi = DokumentasiModel::where('agenda_id', $id)
                                             ->where('user_id', $user->user_id)
                                             ->first();
                
                Log::info('Processing user submission:', [
                    'user_id' => $user->user_id,
                    'has_dokumentasi' => !is_null($dokumentasi)
                ]);
                
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
    
            $response = [
                'status' => 'success',
                'data' => [
                    'agenda' => $agenda,
                    'user_submissions' => $userSubmissions
                ]
            ];
    
            Log::info('Sending response:', $response);
            return response()->json($response);
    
        } catch (\Exception $e) {
            Log::error('Error getting agenda detail:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
    
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail agenda: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadDokumentasi($id)
    {
        try {
            $agenda = AgendaModel::findOrFail($id);
            $dokumentasi = DokumentasiModel::where('agenda_id', $id)->get();

            if ($dokumentasi->isEmpty()) {
                throw new \Exception('Tidak ada file dokumentasi untuk diunduh');
            }

            // Single file download
            if ($dokumentasi->count() === 1) {
                $file = $dokumentasi->first();
                if (!Storage::exists($file->file_dokumentasi)) {
                    throw new \Exception('File tidak ditemukan');
                }
                
                return Storage::download(
                    $file->file_dokumentasi,
                    $file->nama_dokumentasi . '.' . pathinfo($file->file_dokumentasi, PATHINFO_EXTENSION)
                );
            }

            // Multiple files (ZIP)
            $zipFileName = Str::slug($agenda->nama_agenda) . '_dokumentasi.zip';
            $tempPath = storage_path('app/public/temp');
            $zipPath = $tempPath . '/' . $zipFileName;

            // Create temp directory if not exists
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
                    $filename = $file->user->nama . '_' . basename($file->file_dokumentasi);
                    $zip->addFile($originalFile, $filename);
                }
            }
            
            $zip->close();

            return response()->download($zipPath)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Error downloading dokumentasi:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunduh file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteProgress($id)
    {
        try {
            $dokumentasi = DokumentasiModel::findOrFail($id);
            
            if (Storage::exists($dokumentasi->file_dokumentasi)) {
                Storage::delete($dokumentasi->file_dokumentasi);
            }

            $agendaId = $dokumentasi->agenda_id;
            $dokumentasi->delete();
            
            $this->updateAgendaStatus($agendaId);

            return response()->json([
                'status' => 'success',
                'message' => 'Dokumentasi berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting progress:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus dokumentasi: ' . $e->getMessage()
            ], 500);
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