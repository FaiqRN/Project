<?php

namespace App\Http\Controllers;

use App\Models\AgendaModel;
use App\Models\DokumentasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class AdminUpdateProgressAgendaController extends Controller
{
    public function index()
    {
        try {
            // Ambil semua agenda dengan eager loading dan ordering
            $agendas = AgendaModel::with([
                'kegiatanJurusan',
                'kegiatanProgramStudi',
                'kegiatanInstitusi',   
                'kegiatanLuarInstitusi',
                'dokumentasi',
                'users.dokumentasi'
            ])
            ->orderBy('tanggal_agenda', 'asc')  // Urutkan berdasarkan tanggal
            ->orderBy('agenda_id', 'asc')       // Kemudian berdasarkan ID
            ->get();

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
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function getDetailAgenda($id)
    {
        try {
            // Ambil agenda dengan eager loading
            $agenda = AgendaModel::with([
                'kegiatanJurusan',
                'kegiatanProgramStudi',
                'kegiatanInstitusi',   
                'kegiatanLuarInstitusi',
                'users' => function($query) {
                    $query->with(['dokumentasi' => function($q) {
                        $q->orderBy('created_at', 'desc');
                    }]);
                }
            ])->findOrFail($id);

            // Ambil dokumentasi terkait agenda ini
            $dokumentasi = DokumentasiModel::where('agenda_id', $id)
                                         ->with('user')
                                         ->get()
                                         ->keyBy('user_id');

            // Map user submissions dengan informasi lengkap
            $userSubmissions = $agenda->users->map(function ($user) use ($dokumentasi) {
                $dokUser = $dokumentasi->get($user->user_id);
                
                return [
                    'user_id' => $user->user_id,
                    'nama_dosen' => $user->nama_lengkap,
                    'has_submitted' => !is_null($dokUser),
                    'dokumentasi' => $dokUser ? [
                        'id' => $dokUser->dokumentasi_id,
                        'nama_file' => $dokUser->nama_dokumentasi
                    ] : null
                ];
            });

            $kegiatanNama = '';
            if ($agenda->kegiatanJurusan) {
                $kegiatanNama = $agenda->kegiatanJurusan->nama_kegiatan_jurusan;
            } elseif ($agenda->kegiatanProgramStudi) {
                $kegiatanNama = $agenda->kegiatanProgramStudi->nama_kegiatan_program_studi;
            } elseif ($agenda->kegiatanInstitusi) {
                $kegiatanNama = $agenda->kegiatanInstitusi->nama_kegiatan_institusi;
            } elseif ($agenda->kegiatanLuarInstitusi) {
                $kegiatanNama = $agenda->kegiatanLuarInstitusi->nama_kegiatan_luar_institusi;
            }

            return response()->json([
                'status' => 'success',
                'data' => [
                    'agenda' => [
                        'nama_agenda' => $agenda->nama_agenda,
                        'jenis_kegiatan' => $kegiatanNama,
                        'tanggal' => $agenda->tanggal_agenda
                    ],
                    'user_submissions' => $userSubmissions
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteProgress($id)
    {
        try {
            DB::beginTransaction();

            $dokumentasi = DokumentasiModel::findOrFail($id);
            
            if (Storage::exists($dokumentasi->file_dokumentasi)) {
                Storage::delete($dokumentasi->file_dokumentasi);
            }

            $agendaId = $dokumentasi->agenda_id;
            $dokumentasi->delete();
            
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
                    $fileName = $file->user->nama_lengkap . '_' . basename($file->file_dokumentasi);
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

        // Update status kegiatan jika diperlukan
        if($agenda->kegiatanJurusan) {
            $agenda->kegiatanJurusan->checkStatus();
        } elseif($agenda->kegiatanProgramStudi) {
            $agenda->kegiatanProgramStudi->checkStatus();
        } elseif($agenda->kegiatanInstitusi) {
            $agenda->kegiatanInstitusi->checkStatus();
        } elseif($agenda->kegiatanLuarInstitusi) {
            $agenda->kegiatanLuarInstitusi->checkStatus();
        }
    }
}