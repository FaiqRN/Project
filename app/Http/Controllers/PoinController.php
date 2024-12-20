<?php

namespace App\Http\Controllers;

use App\Models\JabatanModel;
use App\Models\PoinJurusanModel;
use App\Models\PoinProgramStudiModel;
use App\Models\PoinInstitusiModel;
use App\Models\PoinLuarInstitusiModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use App\Models\KegiatanInstitusiModel;
use App\Models\KegiatanLuarInstitusiModel;
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

        // Ambil data kegiatan institusi yang sudah selesai
        $poinInstitusi = DB::table('t_poin_institusi as pi')
            ->join('t_jabatan as j', 'pi.jabatan_id', '=', 'j.jabatan_id')
            ->join('m_user as u', 'j.user_id', '=', 'u.user_id')
            ->join('t_kegiatan_institusi as ki', 'j.kegiatan_institusi_id', '=', 'ki.kegiatan_institusi_id')
            ->where('ki.status_kegiatan', 'selesai')
            ->select([
                'u.nidn',
                'u.nama_lengkap',
                'ki.nama_kegiatan_institusi as nama_kegiatan',
                'j.jabatan',
                DB::raw('CASE 
                    WHEN j.jabatan = "ketua_pelaksana" THEN pi.poin_ketua_pelaksana
                    WHEN j.jabatan = "sekertaris" THEN pi.poin_sekertaris
                    WHEN j.jabatan = "bendahara" THEN pi.poin_bendahara
                    ELSE pi.poin_anggota
                END as poin'),
                'pi.poin_tambahan',
                'pi.keterangan_tambahan',
                'pi.status_poin_tambahan as status_poin',
                'pi.total_poin',
                DB::raw("'institusi' as jenis"),
                'pi.poin_institusi_id as id'
            ]);

        // Ambil data kegiatan luar institusi yang sudah selesai
        $poinLuarInstitusi = DB::table('t_poin_luar_institusi as pli')
            ->join('t_jabatan as j', 'pli.jabatan_id', '=', 'j.jabatan_id')
            ->join('m_user as u', 'j.user_id', '=', 'u.user_id')
            ->join('t_kegiatan_luar_institusi as kli', 'j.kegiatan_luar_institusi_id', '=', 'kli.kegiatan_luar_institusi_id')
            ->where('kli.status_kegiatan', 'selesai')
            ->select([
                'u.nidn',
                'u.nama_lengkap',
                'kli.nama_kegiatan_luar_institusi as nama_kegiatan',
                'j.jabatan',
                DB::raw('CASE 
                    WHEN j.jabatan = "ketua_pelaksana" THEN pli.poin_ketua_pelaksana
                    WHEN j.jabatan = "sekertaris" THEN pli.poin_sekertaris
                    WHEN j.jabatan = "bendahara" THEN pli.poin_bendahara
                    ELSE pli.poin_anggota
                END as poin'),
                'pli.poin_tambahan',
                'pli.keterangan_tambahan',
                'pli.status_poin_tambahan as status_poin',
                'pli.total_poin',
                DB::raw("'luar_institusi' as jenis"),
                'pli.poin_luar_institusi_id as id'
            ]);

        $allPoin = $poinJurusan
            ->union($poinProdi)
            ->union($poinInstitusi)
            ->union($poinLuarInstitusi);

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
            'jenis' => 'required|in:jurusan,prodi,institusi,luar_institusi',
            'poin_tambahan' => 'required|numeric|min:0',
            'keterangan_tambahan' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            if ($request->jenis === 'jurusan') {
                $poin = PoinJurusanModel::findOrFail($request->id);
            } elseif ($request->jenis === 'prodi') {
                $poin = PoinProgramStudiModel::findOrFail($request->id);
            } elseif ($request->jenis === 'institusi') {
                $poin = PoinInstitusiModel::findOrFail($request->id);
            } else {
                $poin = PoinLuarInstitusiModel::findOrFail($request->id);
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
            'jenis' => 'required|in:jurusan,prodi,institusi,luar_institusi'
        ]);

        try {
            DB::beginTransaction();

            if ($request->jenis === 'jurusan') {
                $poin = PoinJurusanModel::findOrFail($request->id);
            } elseif ($request->jenis === 'prodi') {
                $poin = PoinProgramStudiModel::findOrFail($request->id);
            } elseif ($request->jenis === 'institusi') {
                $poin = PoinInstitusiModel::findOrFail($request->id);
            } else {
                $poin = PoinLuarInstitusiModel::findOrFail($request->id);
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