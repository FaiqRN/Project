<?php

namespace App\Http\Controllers;

use App\Models\AgendaModel;
use App\Models\UserModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;




class PilihAnggotaController extends Controller
{
    public function index()
    {
        // Ambil data PIC yang login
        $userId = session('user_id');




        // Cek kegiatan yang ditangani PIC
        $kegiatanJurusan = KegiatanJurusanModel::with(['surat'])
            ->where('user_id', $userId)
            ->where('status_kegiatan', 'berlangsung')
            ->first();




        $kegiatanProdi = KegiatanProgramStudiModel::with(['surat'])
            ->where('user_id', $userId)
            ->where('status_kegiatan', 'berlangsung')
            ->first();




        // Ambil data agenda berdasarkan kegiatan
        $agendas = AgendaModel::where(function($query) use ($kegiatanJurusan, $kegiatanProdi) {
            if ($kegiatanJurusan) {
                $query->orWhere('kegiatan_jurusan_id', $kegiatanJurusan->kegiatan_jurusan_id);
            }
            if ($kegiatanProdi) {
                $query->orWhere('kegiatan_program_studi_id', $kegiatanProdi->kegiatan_program_studi_id);
            }
        })->get();




        // Ambil daftar dosen yang bisa dipilih
        $dosens = UserModel::where('level_id', 3)->get();




        return view('pic.pilih', [
            'kegiatanJurusan' => $kegiatanJurusan,
            'kegiatanProdi' => $kegiatanProdi,
            'agendas' => $agendas,
            'dosens' => $dosens,
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




            $query = DB::table('t_agenda as a')
                ->join('m_user as u', 'a.user_id', '=', 'u.user_id')
                ->where(function($q) use ($kegiatanJurusan, $kegiatanProdi) {
                    if ($kegiatanJurusan) {
                        $q->orWhere('a.kegiatan_jurusan_id', $kegiatanJurusan->kegiatan_jurusan_id);
                    }
                    if ($kegiatanProdi) {
                        $q->orWhere('a.kegiatan_program_studi_id', $kegiatanProdi->kegiatan_program_studi_id);
                    }
                })
                ->select([
                    'a.agenda_id',
                    'a.nama_agenda',
                    'u.nama_lengkap',
                    'u.nidn'
                ]);




            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    return '<div class="btn-group">
                        <button type="button" class="btn btn-warning btn-sm edit-btn" data-id="'.$row->agenda_id.'">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="'.$row->agenda_id.'">
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
                'user_id' => 'required'
            ]);




            $userId = session('user_id');




            // Verifikasi agenda milik PIC yang login
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
            }




            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk agenda ini'
                ], 403);
            }




            // Update user_id pada agenda
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
            $userId = session('user_id');
            $agenda = AgendaModel::findOrFail($id);




            // Verifikasi akses
            $hasAccess = false;
            if ($agenda->kegiatan_jurusan_id) {
                $hasAccess = KegiatanJurusanModel::where('kegiatan_jurusan_id', $agenda->kegiatan_jurusan_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_program_studi_id) {
                $hasAccess = KegiatanProgramStudiModel::where('kegiatan_program_studi_id', $agenda->kegiatan_program_studi_id)
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




            $userId = session('user_id');
            $agenda = AgendaModel::findOrFail($id);




            // Verifikasi akses
            $hasAccess = false;
            if ($agenda->kegiatan_jurusan_id) {
                $hasAccess = KegiatanJurusanModel::where('kegiatan_jurusan_id', $agenda->kegiatan_jurusan_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_program_studi_id) {
                $hasAccess = KegiatanProgramStudiModel::where('kegiatan_program_studi_id', $agenda->kegiatan_program_studi_id)
                    ->where('user_id', $userId)
                    ->exists();
            }




            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk agenda ini'
                ], 403);
            }




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
            $userId = session('user_id');
            $agenda = AgendaModel::findOrFail($id);

            // Verifikasi akses
            $hasAccess = false;
            if ($agenda->kegiatan_jurusan_id) {
                $hasAccess = KegiatanJurusanModel::where('kegiatan_jurusan_id', $agenda->kegiatan_jurusan_id)
                    ->where('user_id', $userId)
                    ->exists();
            } else if ($agenda->kegiatan_program_studi_id) {
                $hasAccess = KegiatanProgramStudiModel::where('kegiatan_program_studi_id', $agenda->kegiatan_program_studi_id)
                    ->where('user_id', $userId)
                    ->exists();
            }

            if (!$hasAccess) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk agenda ini'
                ], 403);
            }
            $agenda->user_id = null;
            $agenda->save();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}





