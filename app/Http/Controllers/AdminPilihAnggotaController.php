<?php

namespace App\Http\Controllers;

use App\Models\AgendaModel;
use App\Models\UserModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
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

    public function getData()
    {
        try {
            // Query untuk mengambil semua data agenda dan user terkait
            $query = DB::table('t_agenda as a')
                ->leftJoin('m_user as u', 'a.user_id', '=', 'u.user_id')
                ->leftJoin('t_kegiatan_jurusan as kj', 'a.kegiatan_jurusan_id', '=', 'kj.kegiatan_jurusan_id')
                ->leftJoin('t_kegiatan_program_studi as kp', 'a.kegiatan_program_studi_id', '=', 'kp.kegiatan_program_studi_id')
                ->select([
                    'a.agenda_id',
                    'a.nama_agenda',
                    'u.nama_lengkap',
                    'u.nidn',
                    'kj.nama_kegiatan_jurusan',
                    'kp.nama_kegiatan_program_studi',
                    DB::raw('COALESCE(kj.nama_kegiatan_jurusan, kp.nama_kegiatan_program_studi) as nama_kegiatan')
                ]);

            return DataTables::of($query)
                ->addIndexColumn()
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
                ->addColumn('status_anggota', function($row) {
                    return $row->nama_lengkap ? 
                        '<span class="badge badge-success">Sudah dipilih</span>' : 
                        '<span class="badge badge-warning">Belum dipilih</span>';
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

            $agenda = AgendaModel::findOrFail($request->agenda_id);
            $agenda->user_id = $request->user_id;
            $agenda->save();

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
            $agenda = AgendaModel::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $agenda
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

            $agenda = AgendaModel::findOrFail($id);
            $agenda->user_id = $request->user_id;
            $agenda->save();

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
            $agenda = AgendaModel::findOrFail($id);
            // Set user_id menjadi null
            $agenda->user_id = null;
            $agenda->save();

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

    // Method tambahan untuk filter data
    public function getFilteredData(Request $request)
    {
        try {
            $query = DB::table('t_agenda as a')
                ->leftJoin('m_user as u', 'a.user_id', '=', 'u.user_id')
                ->leftJoin('t_kegiatan_jurusan as kj', 'a.kegiatan_jurusan_id', '=', 'kj.kegiatan_jurusan_id')
                ->leftJoin('t_kegiatan_program_studi as kp', 'a.kegiatan_program_studi_id', '=', 'kp.kegiatan_program_studi_id');

            // Filter berdasarkan kegiatan jika ada
            if ($request->kegiatan_type && $request->kegiatan_id) {
                if ($request->kegiatan_type === 'jurusan') {
                    $query->where('a.kegiatan_jurusan_id', $request->kegiatan_id);
                } else if ($request->kegiatan_type === 'prodi') {
                    $query->where('a.kegiatan_program_studi_id', $request->kegiatan_id);
                }
            }

            // Filter berdasarkan status anggota
            if ($request->has('status_anggota')) {
                if ($request->status_anggota === 'assigned') {
                    $query->whereNotNull('a.user_id');
                } else if ($request->status_anggota === 'unassigned') {
                    $query->whereNull('a.user_id');
                }
            }

            $query->select([
                'a.agenda_id',
                'a.nama_agenda',
                'u.nama_lengkap',
                'u.nidn',
                'kj.nama_kegiatan_jurusan',
                'kp.nama_kegiatan_program_studi'
            ]);

            return DataTables::of($query)->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}