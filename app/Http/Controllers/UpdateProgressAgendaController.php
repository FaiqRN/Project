<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AgendaModel;
use App\Models\DokumentasiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdateProgressAgendaController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        
        // Ambil semua agenda yang ditugaskan ke user
        $agendas = AgendaModel::join('t_agenda_user', 't_agenda.agenda_id', '=', 't_agenda_user.agenda_id')
            ->where('t_agenda_user.user_id', $userId)
            ->with([
                'kegiatanJurusan',
                'kegiatanProgramStudi',
                'dokumentasi',
                'users' => function($query) use ($userId) {
                    $query->where('m_user.user_id', $userId);
                }
            ])
            ->select('t_agenda.*')
            ->get();
    
        // Modifikasi collection untuk menambahkan display_status dan hasUploaded
        $agendas = $agendas->map(function($agenda) use ($userId) {
            // Cek apakah user sudah upload
            $userDokumentasi = DokumentasiModel::where('agenda_id', $agenda->agenda_id)
                                              ->where('user_id', $userId)
                                              ->first();
                                              
            $agenda->hasUploaded = !is_null($userDokumentasi);
            $agenda->display_status = $agenda->hasUploaded ? 'selesai' : 
                                    ($agenda->status_agenda === 'berlangsung' ? 'berlangsung' : 'tahap penyelesaian');
            
            return $agenda;
        });
    
        return view('dosen.update', [
            'agendas' => $agendas,
            'breadcrumb' => (object)[
                'title' => 'Update Progress Agenda',
                'list' => ['Home', 'Update Progress Agenda']
            ]
        ]);
    }

    public function getDetailAgenda($id)
    {
        try {
            $userId = session('user_id');
            
            $agenda = AgendaModel::with([
                'kegiatanJurusan',
                'kegiatanProgramStudi',
                'dokumentasi',
                'users' => function($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])->findOrFail($id);

            // Pastikan user memiliki akses ke agenda ini
            if (!$agenda->users->contains('user_id', $userId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke agenda ini'
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'data' => $agenda
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
                'nama_dokumentasi' => 'required|string|max:200',
                'deskripsi_dokumentasi' => 'required|string',
                'file_dokumentasi' => 'required|file|mimes:jpeg,png,pdf,doc,docx|max:10240',
                'tanggal' => 'required|date'
            ], [
                'nama_dokumentasi.required' => 'Nama dokumentasi harus diisi',
                'nama_dokumentasi.max' => 'Nama dokumentasi maksimal 200 karakter',
                'deskripsi_dokumentasi.required' => 'Deskripsi dokumentasi harus diisi',
                'file_dokumentasi.required' => 'File dokumentasi harus diupload',
                'file_dokumentasi.mimes' => 'File harus berupa jpeg, png, pdf, doc, atau docx',
                'file_dokumentasi.max' => 'Ukuran file maksimal 10MB',
                'tanggal.required' => 'Tanggal harus diisi',
                'tanggal.date' => 'Format tanggal tidak valid'
            ]);
    
            $userId = session('user_id');
            
            // Ambil data agenda dan pastikan exists
            $agenda = AgendaModel::findOrFail($id);
    
            // Cek akses user ke agenda
            if (!$agenda->users->contains('user_id', $userId)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke agenda ini'
                ], 403);
            }
    
            // Cek apakah user sudah pernah upload untuk agenda ini
            $existingDoc = DokumentasiModel::where('agenda_id', $id)
                                         ->where('user_id', $userId)
                                         ->first();
                                         
            if ($existingDoc) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda sudah mengupload dokumentasi untuk agenda ini'
                ], 400);
            }
    
            // Upload file dokumentasi
            $file = $request->file('file_dokumentasi');
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::slug($request->nama_dokumentasi) . '_' . time() . '_' . $userId . '.' . $extension;
            
            try {
                $path = $file->storeAs('public/dokumentasi', $fileName);
    
                // Buat dokumentasi baru
                $dokumentasi = DokumentasiModel::create([
                    'nama_dokumentasi' => $request->nama_dokumentasi,
                    'deskripsi_dokumentasi' => $request->deskripsi_dokumentasi,
                    'file_dokumentasi' => $path,
                    'tanggal' => $request->tanggal,
                    'user_id' => $userId,
                    'agenda_id' => $id
                ]);
    
                // Hitung total user yang ditugaskan dan yang sudah upload
                $totalUsers = $agenda->users()->count();
                $totalUploaded = DokumentasiModel::where('agenda_id', $id)
                                               ->distinct('user_id')
                                               ->count('user_id');
    
                // Update status agenda
                if ($totalUploaded == 1) {
                    // Jika ini upload pertama
                    $agenda->status_agenda = 'tahap penyelesaian';
                } elseif ($totalUploaded == $totalUsers) {
                    // Jika semua user sudah upload
                    $agenda->status_agenda = 'selesai';
                }
                // Jika tidak memenuhi kondisi di atas, status tetap 'tahap penyelesaian'
                
                $agenda->save();
    
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

    public function checkAgendaStatus($id)
    {
        $agenda = AgendaModel::with('users')->findOrFail($id);
        $totalUsers = $agenda->users()->count();
        $usersSubmitted = DokumentasiModel::where('agenda_id', $id)
            ->select('user_id')
            ->groupBy('user_id')
            ->get()
            ->count();

        return [
            'agenda_id' => $id,
            'current_status' => $agenda->status_agenda,
            'total_users' => $totalUsers,
            'users_submitted' => $usersSubmitted,
            'dokumentasi' => DokumentasiModel::where('agenda_id', $id)
                ->select('user_id', 'nama_dokumentasi')
                ->get()
        ];
    }
    
}