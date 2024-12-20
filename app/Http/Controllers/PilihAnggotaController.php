<?php

namespace App\Http\Controllers;

use App\Models\AgendaModel;
use App\Models\UserModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use App\Models\KegiatanLuarInstitusiModel;
use App\Models\KegiatanInstitusiModel;
use App\Models\AgendaUserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PilihAnggotaController extends Controller
{
    public function index()
    {
        $userId = session('user_id');
        $kegiatanJurusan = KegiatanJurusanModel::with(['surat'])
            ->where('user_id', $userId)
            ->where('status_kegiatan', 'berlangsung')
            ->first();
           
        $kegiatanProdi = KegiatanProgramStudiModel::with(['surat'])
            ->where('user_id', $userId)
            ->where('status_kegiatan', 'berlangsung')
            ->first();

        $kegiatanLuarInstitusi = KegiatanLuarInstitusiModel::with(['surat'])
            ->where('user_id', $userId)
            ->where('status_kegiatan', 'berlangsung')
            ->first();
            
        $kegiatanInstitusi = KegiatanInstitusiModel::with(['surat'])
            ->where('user_id', $userId)
            ->where('status_kegiatan', 'berlangsung')
            ->first();

        $agendas = AgendaModel::where(function($query) use ($kegiatanJurusan, $kegiatanProdi, $kegiatanLuarInstitusi, $kegiatanInstitusi) {
            if ($kegiatanJurusan) {
                $query->orWhere('kegiatan_jurusan_id', $kegiatanJurusan->kegiatan_jurusan_id);
            }
            if ($kegiatanProdi) {
                $query->orWhere('kegiatan_program_studi_id', $kegiatanProdi->kegiatan_program_studi_id);
            }
            if ($kegiatanLuarInstitusi) {
                $query->orWhere('kegiatan_luar_institusi_id', $kegiatanLuarInstitusi->kegiatan_luar_institusi_id);
            }
            if ($kegiatanInstitusi) {
                $query->orWhere('kegiatan_institusi_id', $kegiatanInstitusi->kegiatan_institusi_id);
            }
        })->get();

        // Modified to get all users with level 3 (dosen) and level 4 (PIC)
        $users = UserModel::whereIn('level_id', [3, 4])->get();
       
        return view('pic.pilih', [
            'kegiatanJurusan' => $kegiatanJurusan,
            'kegiatanProdi' => $kegiatanProdi,
            'kegiatanInstitusi' => $kegiatanInstitusi, // Fixed typo here
            'kegiatanLuarInstitusi' => $kegiatanLuarInstitusi,
            'agendas' => $agendas,
            'dosens' => $users, // Changed variable name to be more generic since it now includes PIC
            'breadcrumb' => (object)[
                'title' => 'Pilih Anggota',
                'list' => ['Home', 'Agenda', 'Pilih Anggota']
            ]
        ]);
    }

    public function getData()
    {
        try {
            $userId = session('user_id');
            $kegiatanJurusan = KegiatanJurusanModel::where('user_id', $userId)
                ->where('status_kegiatan', 'berlangsung')
                ->first();
               
            $kegiatanProdi = KegiatanProgramStudiModel::where('user_id', $userId)
                ->where('status_kegiatan', 'berlangsung')
                ->first();

            $kegiatanLuarInstitusi = KegiatanLuarInstitusiModel::where('user_id', $userId)
                ->where('status_kegiatan', 'berlangsung')
                ->first();

            $kegiatanInstitusi = KegiatanInstitusiModel::where('user_id', $userId)
                ->where('status_kegiatan', 'berlangsung')
                ->first();

            $query = DB::table('t_agenda_user as au')
                ->join('t_agenda as a', 'au.agenda_id', '=', 'a.agenda_id')
                ->join('m_user as u', 'au.user_id', '=', 'u.user_id')
                ->where(function($q) use ($kegiatanJurusan, $kegiatanProdi, $kegiatanLuarInstitusi, $kegiatanInstitusi) {
                    if ($kegiatanJurusan) {
                        $q->orWhere('a.kegiatan_jurusan_id', $kegiatanJurusan->kegiatan_jurusan_id);
                    }
                    if ($kegiatanProdi) {
                        $q->orWhere('a.kegiatan_program_studi_id', $kegiatanProdi->kegiatan_program_studi_id);
                    }
                    if ($kegiatanLuarInstitusi) {
                        $q->orWhere('a.kegiatan_luar_institusi_id', $kegiatanLuarInstitusi->kegiatan_luar_institusi_id);
                    }
                    if ($kegiatanInstitusi) {
                        $q->orWhere('a.kegiatan_institusi_id', $kegiatanInstitusi->kegiatan_institusi_id);
                    }
                })
                ->select([
                    'au.id',
                    'a.agenda_id',
                    'a.nama_agenda',
                    'u.nama_lengkap',
                    'u.nidn',
                    'u.level_id' // Added level_id to identify user type
                ]);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('role', function($row) {
                    return $row->level_id == 3 ? 'Dosen' : 'PIC';
                })
                ->addColumn('action', function($row) {
                    return '<div class="btn-group">
                    <button type="button" class="btn btn-warning btn-sm" onclick="editData('.$row->id.')">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button type="button" class="btn btn-danger btn-sm ms-2" onclick="deleteData('.$row->id.')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);

        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'agenda_id' => 'required',
                'user_ids' => 'required|array|min:1',
                'user_ids.*' => 'required|exists:m_user,user_id'
            ]);
   
            $userId = session('user_id');
            $agenda = AgendaModel::findOrFail($request->agenda_id);
           
            $hasAccess = false;
            if ($agenda->kegiatan_jurusan_id) {
                $hasAccess = KegiatanJurusanModel::where('kegiatan_jurusan_id', $agenda->kegiatan_jurusan_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_program_studi_id) {
                $hasAccess = KegiatanProgramStudiModel::where('kegiatan_program_studi_id', $agenda->kegiatan_program_studi_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_luar_institusi_id) {
                $hasAccess = KegiatanLuarInstitusiModel::where('kegiatan_luar_institusi_id', $agenda->kegiatan_luar_institusi_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_institusi_id) {
                $hasAccess = KegiatanInstitusiModel::where('kegiatan_institusi_id', $agenda->kegiatan_institusi_id)
                    ->where('user_id', $userId)
                    ->exists();
            }
   
            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk agenda ini'
                ], 403);
            }
   
            // Cek duplikasi untuk semua user_id
            $existingUsers = AgendaUserModel::where('agenda_id', $request->agenda_id)
                ->whereIn('user_id', $request->user_ids)
                ->pluck('user_id')
                ->toArray();
               
            if (!empty($existingUsers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Beberapa dosen/PIC sudah ditambahkan ke agenda ini'
                ], 422);
            }
   
            // Simpan semua data anggota
            $dataToInsert = array_map(function($user_id) use ($request) {
                return [
                    'agenda_id' => $request->agenda_id,
                    'user_id' => $user_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }, $request->user_ids);
   
            AgendaUserModel::insert($dataToInsert);
   
            return response()->json([
                'success' => true,
                'message' => 'Berhasil menambahkan anggota ke agenda'
            ]);
   
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $userId = session('user_id');
            $agendaUser = AgendaUserModel::with('agenda')->findOrFail($id);
            $agenda = $agendaUser->agenda;

            $hasAccess = false;
            if ($agenda->kegiatan_jurusan_id) {
                $hasAccess = KegiatanJurusanModel::where('kegiatan_jurusan_id', $agenda->kegiatan_jurusan_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_program_studi_id) {
                $hasAccess = KegiatanProgramStudiModel::where('kegiatan_program_studi_id', $agenda->kegiatan_program_studi_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_luar_institusi_id) {
                $hasAccess = KegiatanLuarInstitusiModel::where('kegiatan_luar_institusi_id', $agenda->kegiatan_luar_institusi_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_institusi_id) {
                $hasAccess = KegiatanInstitusiModel::where('kegiatan_institusi_id', $agenda->kegiatan_institusi_id)
                    ->where('user_id', $userId)
                    ->exists();
            }

            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk agenda ini'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $agendaUser
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:m_user,user_id'
            ]);
   
            $userId = session('user_id');
            $agendaUser = AgendaUserModel::with('agenda')->findOrFail($id);
            $agenda = $agendaUser->agenda;
   
            $hasAccess = false;
            if ($agenda->kegiatan_jurusan_id) {
                $hasAccess = KegiatanJurusanModel::where('kegiatan_jurusan_id', $agenda->kegiatan_jurusan_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_program_studi_id) {
                $hasAccess = KegiatanProgramStudiModel::where('kegiatan_program_studi_id', $agenda->kegiatan_program_studi_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_luar_institusi_id) {
                $hasAccess = KegiatanLuarInstitusiModel::where('kegiatan_luar_institusi_id', $agenda->kegiatan_luar_institusi_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_institusi_id) {
                $hasAccess = KegiatanInstitusiModel::where('kegiatan_institusi_id', $agenda->kegiatan_institusi_id)
                    ->where('user_id', $userId)
                    ->exists();
            }
   
            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk agenda ini'
                ], 403);
            }
   
            // Cek duplikasi
            $exists = AgendaUserModel::where('agenda_id', $agenda->agenda_id)
                                   ->where('user_id', $request->user_id)
                                   ->where('id', '!=', $id)
                                   ->exists();
           
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anggota sudah ditambahkan ke agenda ini'
                ], 422);
            }
   
            // Update hanya user_id
            $agendaUser->user_id = $request->user_id;
            $agendaUser->save();
   
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil diperbarui'
            ]);
   
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $userId = session('user_id');
            $agendaUser = AgendaUserModel::with('agenda')->findOrFail($id);
            $agenda = $agendaUser->agenda;
   
            $hasAccess = false;
            if ($agenda->kegiatan_jurusan_id) {
                $hasAccess = KegiatanJurusanModel::where('kegiatan_jurusan_id', $agenda->kegiatan_jurusan_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_program_studi_id) {
                $hasAccess = KegiatanProgramStudiModel::where('kegiatan_program_studi_id', $agenda->kegiatan_program_studi_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_luar_institusi_id) {
                $hasAccess = KegiatanLuarInstitusiModel::where('kegiatan_luar_institusi_id', $agenda->kegiatan_luar_institusi_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_institusi_id) {
                $hasAccess = KegiatanInstitusiModel::where('kegiatan_institusi_id', $agenda->kegiatan_institusi_id)
                    ->where('user_id', $userId)
                    ->exists();
            }
   
            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk agenda ini'
                ], 403);
            }
   
            $agendaUser->delete();
   
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
   
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}