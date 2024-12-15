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
        $agendas = AgendaModel::with(['kegiatanJurusan', 'kegiatanProgramStudi', 'users'])->get();

        $agendas = $agendas->map(function ($agenda) {
            $totalUsers = $agenda->users()->count();
            $uploadedUsers = DokumentasiModel::where('agenda_id', $agenda->agenda_id)
                ->distinct('user_id')
                ->count('user_id');

            $progressPercentage = $totalUsers > 0 ? round(($uploadedUsers / $totalUsers) * 100, 2) : 0;

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
            // Ambil agenda dengan semua relasi yang dibutuhkan
            $agenda = AgendaModel::with([
                'kegiatanJurusan',
                'kegiatanProgramStudi',
                'users' // Relasi dengan user (dosen)
            ])->findOrFail($id);
    
            // Ambil semua dokumentasi yang terkait dengan agenda ini
            $dokumentasi = DokumentasiModel::where('agenda_id', $id)
                ->with('user') // Relasi dengan user untuk mendapatkan nama dosen
                ->get();
    
            // Siapkan data user submissions
            $userSubmissions = [];
            foreach ($agenda->users as $user) {
                // Cari dokumentasi untuk user ini
                $dokUser = $dokumentasi->where('user_id', $user->user_id)->first();
                
                $userSubmissions[] = [
                    'user_id' => $user->user_id,
                    'nama_dosen' => $user->nama_lengkap, // Sesuaikan dengan field nama di UserModel
                    'has_submitted' => !is_null($dokUser),
                    'dokumentasi' => $dokUser ? [
                        'id' => $dokUser->dokumentasi_id,
                        'nama_file' => $dokUser->nama_dokumentasi,
                        'deskripsi' => $dokUser->deskripsi_dokumentasi,
                        'tanggal_upload' => $dokUser->tanggal
                    ] : null
                ];
            }
    
            return response()->json([
                'status' => 'success',
                'data' => [
                    'agenda' => [
                        'id' => $agenda->agenda_id,
                        'nama' => $agenda->nama_agenda,
                        'kegiatan' => $agenda->kegiatanJurusan ? 
                                     $agenda->kegiatanJurusan->nama_kegiatan_jurusan : 
                                     $agenda->kegiatanProgramStudi->nama_kegiatan_program_studi,
                        'tanggal' => $agenda->tanggal_agenda
                    ],
                    'user_submissions' => $userSubmissions
                ]
            ]);
    
        } catch (\Exception $e) {
            Log::error('Error in getDetailAgenda: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memuat detail agenda: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function deleteProgress($id)
    {
        try {
            $dokumentasi = DokumentasiModel::findOrFail($id);
    
            // Hapus file dari storage
            if (Storage::exists($dokumentasi->file_dokumentasi)) {
                Storage::delete($dokumentasi->file_dokumentasi);
            }
    
            // Hapus data dari database
            $dokumentasi->delete();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Dokumentasi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
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

}