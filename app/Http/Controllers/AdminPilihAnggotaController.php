<?php

namespace App\Http\Controllers;

use App\Models\AgendaModel;
use App\Models\UserModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use App\Models\AgendaUserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AdminPilihAnggotaController extends Controller
{
    public function index()
    {
        // Ambil semua data kegiatan yang sedang berlangsung
        $kegiatanJurusan = KegiatanJurusanModel::with(['surat'])
            ->where('status_kegiatan', 'berlangsung')
            ->get();
            
        $kegiatanProdi = KegiatanProgramStudiModel::with(['surat'])
            ->where('status_kegiatan', 'berlangsung')
            ->get();

        // Ambil semua data agenda
        $agendas = AgendaModel::all();

        // Ambil daftar semua dosen
        $dosens = UserModel::where('level_id', 3)->get();

        return view('admin.dosen.agenda.pilih-anggota', [
            'kegiatanJurusan' => $kegiatanJurusan,
            'kegiatanProdi' => $kegiatanProdi,
            'agendas' => $agendas,
            'dosens' => $dosens,
            'breadcrumb' => (object)[
                'title' => 'Pilih Anggota',
                'list' => ['Home', 'Dosen', 'Agenda', 'Pilih Anggota']
            ]
        ]);
    }

    public function getData(Request $request)
    {
        try {
            $query = DB::table('t_agenda as a')
                ->leftJoin('t_agenda_user as au', 'a.agenda_id', '=', 'au.agenda_id')
                ->leftJoin('m_user as u', 'au.user_id', '=', 'u.user_id')
                ->leftJoin('t_kegiatan_jurusan as kj', 'a.kegiatan_jurusan_id', '=', 'kj.kegiatan_jurusan_id')
                ->leftJoin('t_kegiatan_program_studi as kp', 'a.kegiatan_program_studi_id', '=', 'kp.kegiatan_program_studi_id');
    
            // Filter berdasarkan kegiatan jurusan
            if ($request->kegiatan_jurusan) {
                $query->where('a.kegiatan_jurusan_id', $request->kegiatan_jurusan);
            }
    
            // Filter berdasarkan kegiatan prodi
            if ($request->kegiatan_prodi) {
                $query->where('a.kegiatan_program_studi_id', $request->kegiatan_prodi);
            }
    
            // Filter berdasarkan status anggota
            if ($request->status_anggota) {
                if ($request->status_anggota === 'assigned') {
                    $query->whereNotNull('au.id');
                } else if ($request->status_anggota === 'unassigned') {
                    $query->whereNull('au.id');
                }
            }
    
            $query->select([
                'a.agenda_id',
                'a.nama_agenda',
                'u.nama_lengkap',
                'u.nidn',
                DB::raw('COALESCE(kj.nama_kegiatan_jurusan, kp.nama_kegiatan_program_studi) as nama_kegiatan'),
                DB::raw('CASE WHEN au.id IS NULL THEN "Belum dipilih" ELSE "Sudah dipilih" END as status')
            ])
            ->groupBy('a.agenda_id', 'a.nama_agenda', 'u.nama_lengkap', 'u.nidn', 'nama_kegiatan', 'status');
    
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status_anggota', function($row) {
                    return $row->status === 'Belum dipilih' 
                        ? '<span class="badge badge-warning">Belum dipilih</span>'
                        : '<span class="badge badge-success">Sudah dipilih</span>';
                })
                ->addColumn('action', function($row) {
                    return '<div class="btn-group">
                        <button type="button" class="btn btn-warning btn-sm" onclick="editData('.$row->agenda_id.')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteData('.$row->agenda_id.')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>';
                })
                ->rawColumns(['action', 'status_anggota'])
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
                'user_id' => 'required'
            ]);

            // Cek apakah relasi sudah ada
            $exists = AgendaUserModel::where('agenda_id', $request->agenda_id)
                                   ->where('user_id', $request->user_id)
                                   ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dosen sudah ditambahkan ke agenda ini'
                ], 422);
            }

            // Buat relasi baru
            AgendaUserModel::create([
                'agenda_id' => $request->agenda_id,
                'user_id' => $request->user_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil menambahkan dosen ke agenda'
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
            $agendaUser = AgendaUserModel::findOrFail($id);
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
                'user_id' => 'required'
            ]);

            $agendaUser = AgendaUserModel::findOrFail($id);

            // Cek duplikasi
            $exists = AgendaUserModel::where('agenda_id', $agendaUser->agenda_id)
                                   ->where('user_id', $request->user_id)
                                   ->where('id', '!=', $id)
                                   ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Dosen sudah ditambahkan ke agenda ini'
                ], 422);
            }

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
            $agendaUser = AgendaUserModel::findOrFail($id);
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

    public function getFilteredData(Request $request)
    {
        try {
            $query = DB::table('t_agenda_user as au')
                ->join('t_agenda as a', 'au.agenda_id', '=', 'a.agenda_id')
                ->join('m_user as u', 'au.user_id', '=', 'u.user_id')
                ->leftJoin('t_kegiatan_jurusan as kj', 'a.kegiatan_jurusan_id', '=', 'kj.kegiatan_jurusan_id')
                ->leftJoin('t_kegiatan_program_studi as kp', 'a.kegiatan_program_studi_id', '=', 'kp.kegiatan_program_studi_id');
    
            // Filter berdasarkan kegiatan jurusan
            if ($request->kegiatan_jurusan) {
                $query->where('a.kegiatan_jurusan_id', $request->kegiatan_jurusan);
            }
    
            // Filter berdasarkan kegiatan prodi
            if ($request->kegiatan_prodi) {
                $query->where('a.kegiatan_program_studi_id', $request->kegiatan_prodi);
            }
    
            // Filter berdasarkan status anggota
            if ($request->status_anggota) {
                if ($request->status_anggota === 'assigned') {
                    $query->whereNotNull('au.id');
                } else if ($request->status_anggota === 'unassigned') {
                    $query->whereNull('au.id');
                }
            }
    
            $query->select([
                'au.id',
                'a.agenda_id',
                'a.nama_agenda',
                'u.nama_lengkap',
                'u.nidn',
                DB::raw('COALESCE(kj.nama_kegiatan_jurusan, kp.nama_kegiatan_program_studi) as nama_kegiatan')
            ]);
    
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status_anggota', function($row) {
                    return '<span class="badge badge-success">Sudah dipilih</span>';
                })
                ->addColumn('action', function($row) {
                    return '<div class="btn-group">
                        <button type="button" class="btn btn-warning btn-sm" onclick="editData('.$row->id.')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="deleteData('.$row->id.')">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>';
                })
                ->rawColumns(['action', 'status_anggota'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    
}