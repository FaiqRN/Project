<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AgendaModel;
use App\Models\DokumentasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminUpdateProgressAgendaController extends Controller
{
    public function index()
    {
        // Ambil semua agenda dengan eager loading
        $agendas = AgendaModel::with([
            'kegiatanJurusan',
            'kegiatanProgramStudi',
            'dokumentasi',
            'users'
        ])->get();
    
        // Modifikasi collection untuk menambahkan display_status dan progress info
        $agendas = $agendas->map(function($agenda) {
            // Hitung total user dan yang sudah upload
            $totalUsers = $agenda->users()->count();
            $uploadedUsers = DokumentasiModel::where('agenda_id', $agenda->agenda_id)
                                           ->distinct('user_id')
                                           ->count('user_id');
            
            // Calculate progress percentage
            $progressPercentage = $totalUsers > 0 ? 
                                round(($uploadedUsers / $totalUsers) * 100, 2) : 0;
            
            // Tetap gunakan model Eloquent dan tambahkan properti tambahan
            $agenda->setAttribute('progress', [
                'total_users' => $totalUsers,
                'uploaded_users' => $uploadedUsers,
                'percentage' => $progressPercentage
            ]);
            
            // Set display status berdasarkan progress
            $agenda->setAttribute('display_status', $this->determineStatus($uploadedUsers, $totalUsers));
            
            return $agenda; // Mengembalikan model asli dengan properti tambahan
        });
    
        return view('admin.dosen.update-progress', [
            'agendas' => $agendas,
            'breadcrumb' => (object)[
                'title' => 'Update Progress Agenda',
                'list' => ['Home', 'Dosen', 'Update Progress Agenda']
            ]
        ]);
    }

    private function determineStatus($uploadedUsers, $totalUsers)
    {
        if ($uploadedUsers === 0) return 'berlangsung';
        if ($uploadedUsers === $totalUsers) return 'selesai';
        return 'tahap penyelesaian';
    }

    public function getDetailAgenda($id)
    {
        try {
            $agenda = AgendaModel::with([
                'kegiatanJurusan',
                'kegiatanProgramStudi',
                'dokumentasi.user', // Include user information for each dokumentasi
                'users'
            ])->findOrFail($id);

            // Get submission status for each user
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
                    'dokumentasi' => $dokumentasi
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
                'message' => 'Agenda tidak ditemukan'
            ], 404);
        }
    }

    public function updateProgress(Request $request, $id)
    {
        try {
            // Validasi input
            $request->validate([
                'user_id' => 'required|exists:m_user,user_id',
                'nama_dokumentasi' => 'required|string|max:200',
                'deskripsi_dokumentasi' => 'required|string',
                'file_dokumentasi' => 'required|file|mimes:jpeg,png,pdf,doc,docx|max:10240',
                'tanggal' => 'required|date'
            ], [
                'user_id.required' => 'User ID harus diisi',
                'user_id.exists' => 'User tidak ditemukan',
                'nama_dokumentasi.required' => 'Nama dokumentasi harus diisi',
                'nama_dokumentasi.max' => 'Nama dokumentasi maksimal 200 karakter',
                'deskripsi_dokumentasi.required' => 'Deskripsi dokumentasi harus diisi',
                'file_dokumentasi.required' => 'File dokumentasi harus diupload',
                'file_dokumentasi.mimes' => 'File harus berupa jpeg, png, pdf, doc, atau docx',
                'file_dokumentasi.max' => 'Ukuran file maksimal 10MB',
                'tanggal.required' => 'Tanggal harus diisi',
                'tanggal.date' => 'Format tanggal tidak valid'
            ]);
    
            // Ambil data agenda
            $agenda = AgendaModel::findOrFail($id);
    
            // Cek apakah user sudah pernah upload untuk agenda ini
            $existingDoc = DokumentasiModel::where('agenda_id', $id)
                                         ->where('user_id', $request->user_id)
                                         ->first();
                                         
            if ($existingDoc) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User sudah mengupload dokumentasi untuk agenda ini'
                ], 400);
            }
    
            // Upload file dokumentasi
            $file = $request->file('file_dokumentasi');
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::slug($request->nama_dokumentasi) . '_' . time() . '_' . $request->user_id . '.' . $extension;
            
            try {
                $path = $file->storeAs('public/dokumentasi', $fileName);
    
                // Buat dokumentasi baru
                $dokumentasi = DokumentasiModel::create([
                    'nama_dokumentasi' => $request->nama_dokumentasi,
                    'deskripsi_dokumentasi' => $request->deskripsi_dokumentasi,
                    'file_dokumentasi' => $path,
                    'tanggal' => $request->tanggal,
                    'user_id' => $request->user_id,
                    'agenda_id' => $id
                ]);
    
                // Update status agenda
                $this->updateAgendaStatus($agenda);
    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Progress agenda berhasil diupdate',
                    'data' => [
                        'dokumentasi' => $dokumentasi,
                        'file_url' => asset('storage/dokumentasi/' . $fileName),
                        'agenda_status' => $agenda->status_agenda
                    ]
                ]);
    
            } catch (\Exception $e) {
                // Hapus file yang sudah diupload jika ada error
                if (isset($path) && Storage::exists($path)) {
                    Storage::delete($path);
                }
                throw $e;
            }
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengupdate progress: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateAgendaStatus($agenda)
    {
        $totalUsers = $agenda->users()->count();
        $uploadedUsers = DokumentasiModel::where('agenda_id', $agenda->agenda_id)
                                       ->distinct('user_id')
                                       ->count('user_id');

        if ($uploadedUsers == 0) {
            $agenda->status_agenda = 'berlangsung';
        } elseif ($uploadedUsers == $totalUsers) {
            $agenda->status_agenda = 'selesai';
        } else {
            $agenda->status_agenda = 'tahap penyelesaian';
        }

        $agenda->save();
    }

    public function deleteProgress($agendaId, $userId)
    {
        try {
            $dokumentasi = DokumentasiModel::where('agenda_id', $agendaId)
                                         ->where('user_id', $userId)
                                         ->firstOrFail();

            // Delete file from storage
            if (Storage::exists($dokumentasi->file_dokumentasi)) {
                Storage::delete($dokumentasi->file_dokumentasi);
            }

            // Delete record from database
            $dokumentasi->delete();

            // Update agenda status
            $agenda = AgendaModel::findOrFail($agendaId);
            $this->updateAgendaStatus($agenda);

            return response()->json([
                'status' => 'success',
                'message' => 'Dokumentasi berhasil dihapus',
                'agenda_status' => $agenda->status_agenda
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus dokumentasi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkAgendaStatus($id)
    {
        try {
            $agenda = AgendaModel::with(['users', 'dokumentasi.user'])->findOrFail($id);
            
            $totalUsers = $agenda->users()->count();
            $uploadedUsers = DokumentasiModel::where('agenda_id', $id)
                ->select('user_id')
                ->groupBy('user_id')
                ->get()
                ->count();

            $submissions = DokumentasiModel::where('agenda_id', $id)
                ->with('user:user_id,nama')
                ->get()
                ->map(function($doc) {
                    return [
                        'user_name' => $doc->user->nama,
                        'upload_date' => $doc->tanggal,
                        'file_name' => $doc->nama_dokumentasi,
                        'description' => $doc->deskripsi_dokumentasi
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'agenda_id' => $id,
                    'current_status' => $agenda->status_agenda,
                    'total_users' => $totalUsers,
                    'users_submitted' => $uploadedUsers,
                    'progress_percentage' => $totalUsers > 0 ? 
                        round(($uploadedUsers / $totalUsers) * 100, 2) : 0,
                    'submissions' => $submissions
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil status agenda: ' . $e->getMessage()
            ], 500);
        }
    }
}