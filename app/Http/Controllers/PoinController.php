<?php

namespace App\Http\Controllers;

use App\Models\JabatanModel;
use App\Models\PoinJurusanModel;
use App\Models\PoinProgramStudiModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PoinController extends Controller
{
    public function index()
    {
        return view('dosen.poin.index', [
            'breadcrumb' => (object)[
                'title' => 'Penambahan Poin',
                'list' => ['Home', 'Penambahan Poin']
            ]
        ]);
    }

    public function getPoin()
    {
        // Ambil data kegiatan jurusan yang sudah selesai
        $poinJurusan = DB::table('t_poin_jurusan as pj')
            ->join('t_jabatan as j', 'pj.jabatan_id', '=', 'j.jabatan_id')
            ->join('m_user as u', 'j.user_id', '=', 'u.user_id')
            ->join('t_kegiatan_jurusan as kj', 'j.kegiatan_jurusan_id', '=', 'kj.kegiatan_jurusan_id')
            ->where('kj.status_kegiatan', 'selesai')
            ->select([
                'u.nidn',
                'u.nama_lengkap',
                'kj.nama_kegiatan_jurusan as nama_kegiatan',
                'j.jabatan',
                DB::raw('CASE 
                    WHEN j.jabatan = "ketua_pelaksana" THEN pj.poin_ketua_pelaksana
                    WHEN j.jabatan = "sekertaris" THEN pj.poin_sekertaris
                    WHEN j.jabatan = "bendahara" THEN pj.poin_bendahara
                    ELSE pj.poin_anggota
                END as poin'),
                'pj.poin_tambahan',
                'pj.keterangan_tambahan',
                'pj.status_poin_tambahan as status_poin',
                'pj.total_poin',
                DB::raw("'jurusan' as jenis"),
                'pj.poin_jurusan_id as id'
            ]);

        // Ambil data kegiatan prodi yang sudah selesai
        $poinProdi = DB::table('t_poin_program_studi as pp')
            ->join('t_jabatan as j', 'pp.jabatan_id', '=', 'j.jabatan_id')
            ->join('m_user as u', 'j.user_id', '=', 'u.user_id')
            ->join('t_kegiatan_program_studi as kp', 'j.kegiatan_program_studi_id', '=', 'kp.kegiatan_program_studi_id')
            ->where('kp.status_kegiatan', 'selesai')
            ->select([
                'u.nidn',
                'u.nama_lengkap',
                'kp.nama_kegiatan_program_studi as nama_kegiatan',
                'j.jabatan',
                DB::raw('CASE 
                    WHEN j.jabatan = "ketua_pelaksana" THEN pp.poin_ketua_pelaksana
                    WHEN j.jabatan = "sekertaris" THEN pp.poin_sekertaris
                    WHEN j.jabatan = "bendahara" THEN pp.poin_bendahara
                    ELSE pp.poin_anggota
                END as poin'),
                'pp.poin_tambahan',
                'pp.keterangan_tambahan',
                'pp.status_poin_tambahan as status_poin',
                'pp.total_poin',
                DB::raw("'prodi' as jenis"),
                'pp.poin_program_studi_id as id'
            ]);

        $allPoin = $poinJurusan->union($poinProdi);

        return DataTables::of($allPoin)
            ->addColumn('action', function ($row) {
                if ($row->poin_tambahan === null || $row->poin_tambahan == 0) {
                    return '<button type="button" class="btn btn-primary btn-sm tambah-poin" 
                        data-id="'.$row->id.'" data-jenis="'.$row->jenis.'">
                        <i class="fas fa-plus"></i> Tambah Poin
                    </button>';
                } else {
                    return '<button type="button" class="btn btn-danger btn-sm hapus-poin" 
                        data-id="'.$row->id.'" data-jenis="'.$row->jenis.'">
                        <i class="fas fa-trash"></i> Hapus
                    </button>';
                }
            })
            ->editColumn('jabatan', function ($row) {
                return ucwords(str_replace('_', ' ', $row->jabatan));
            })
            ->editColumn('status_poin', function ($row) {
                if (!$row->status_poin) return '-';
                return '<span class="badge badge-'.
                    ($row->status_poin == 'pending' ? 'warning' : 
                    ($row->status_poin == 'disetujui' ? 'success' : 'danger'))
                    .'">'.$row->status_poin.'</span>';
            })
            ->rawColumns(['action', 'status_poin'])
            ->make(true);
    }

    public function tambahPoin(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'jenis' => 'required|in:jurusan,prodi',
            'poin_tambahan' => 'required|numeric|min:0',
            'keterangan_tambahan' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            if ($request->jenis === 'jurusan') {
                $poin = PoinJurusanModel::findOrFail($request->id);
            } else {
                $poin = PoinProgramStudiModel::findOrFail($request->id);
            }

            $poin->poin_tambahan = $request->poin_tambahan;
            $poin->keterangan_tambahan = $request->keterangan_tambahan;
            $poin->status_poin_tambahan = 'pending';
            $poin->total_poin = $poin->hitungTotalPoin();
            $poin->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Poin tambahan berhasil disimpan'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function hapusPoin(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'jenis' => 'required|in:jurusan,prodi'
        ]);

        try {
            DB::beginTransaction();

            if ($request->jenis === 'jurusan') {
                $poin = PoinJurusanModel::findOrFail($request->id);
            } else {
                $poin = PoinProgramStudiModel::findOrFail($request->id);
            }

            $poin->poin_tambahan = null;
            $poin->keterangan_tambahan = null;
            $poin->status_poin_tambahan = null;
            $poin->total_poin = $poin->hitungTotalPoin();
            $poin->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Poin tambahan berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
