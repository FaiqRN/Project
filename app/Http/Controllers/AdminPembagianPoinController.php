<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PoinJurusanModel;
use App\Models\PoinProgramStudiModel;
use App\Models\PoinInstitusiModel;
use App\Models\PoinLuarInstitusiModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminPembagianPoinController extends Controller
{
    public function index()
    {
        return view('admin.dosen.agenda.persetujuan-poin', [
            'breadcrumb' => (object)[
                'title' => 'Persetujuan Poin',
                'list' => ['Home', 'Dosen', 'Agenda', 'Persetujuan Poin']
            ]
        ]);
    }

    public function getDataPoin()
    {
        try {
            $data = [];

            // Ambil data poin jurusan yang pending
            $poinJurusan = PoinJurusanModel::with(['jabatan.user', 'jabatan.kegiatanJurusan'])
                ->whereNotNull('poin_tambahan')
                ->where('status_poin_tambahan', 'pending')
                ->get();

            // Ambil data poin prodi yang pending
            $poinProdi = PoinProgramStudiModel::with(['jabatan.user', 'jabatan.kegiatanProgramStudi'])
                ->whereNotNull('poin_tambahan')
                ->where('status_poin_tambahan', 'pending')
                ->get();

            // Ambil data poin institusi yang pending
            $poinInstitusi = PoinInstitusiModel::with(['jabatan.user', 'jabatan.kegiatanInstitusi'])
                ->whereNotNull('poin_tambahan')
                ->where('status_poin_tambahan', 'pending')
                ->get();

            // Ambil data poin luar institusi yang pending
            $poinLuarInstitusi = PoinLuarInstitusiModel::with(['jabatan.user', 'jabatan.kegiatanLuarInstitusi'])
                ->whereNotNull('poin_tambahan')
                ->where('status_poin_tambahan', 'pending')
                ->get();

            // Format data poin jurusan
            foreach ($poinJurusan as $poin) {
                $data[] = [
                    'id' => $poin->poin_jurusan_id,
                    'tipe_poin' => 'jurusan',
                    'nama_kegiatan' => $poin->jabatan->kegiatanJurusan->nama_kegiatan_jurusan,
                    'nama_anggota' => $poin->jabatan->user->nama_lengkap,
                    'jabatan' => ucwords(str_replace('_', ' ', $poin->jabatan->jabatan)),
                    'poin_dasar' => $this->getPoinDasar($poin, $poin->jabatan->jabatan),
                    'poin_tambahan' => $poin->poin_tambahan,
                    'total_poin' => $poin->total_poin,
                    'keterangan' => $poin->keterangan_tambahan,
                    'status' => $poin->status_poin_tambahan
                ];
            }

            // Format data poin prodi
            foreach ($poinProdi as $poin) {
                $data[] = [
                    'id' => $poin->poin_program_studi_id,
                    'tipe_poin' => 'prodi',
                    'nama_kegiatan' => $poin->jabatan->kegiatanProgramStudi->nama_kegiatan_program_studi,
                    'nama_anggota' => $poin->jabatan->user->nama_lengkap,
                    'jabatan' => ucwords(str_replace('_', ' ', $poin->jabatan->jabatan)),
                    'poin_dasar' => $this->getPoinDasar($poin, $poin->jabatan->jabatan),
                    'poin_tambahan' => $poin->poin_tambahan,
                    'total_poin' => $poin->total_poin,
                    'keterangan' => $poin->keterangan_tambahan,
                    'status' => $poin->status_poin_tambahan
                ];
            }

            // Format data poin institusi
            foreach ($poinInstitusi as $poin) {
                $data[] = [
                    'id' => $poin->poin_institusi_id,
                    'tipe_poin' => 'institusi',
                    'nama_kegiatan' => $poin->jabatan->kegiatanInstitusi->nama_kegiatan_institusi,
                    'nama_anggota' => $poin->jabatan->user->nama_lengkap,
                    'jabatan' => ucwords(str_replace('_', ' ', $poin->jabatan->jabatan)),
                    'poin_dasar' => $this->getPoinDasar($poin, $poin->jabatan->jabatan),
                    'poin_tambahan' => $poin->poin_tambahan,
                    'total_poin' => $poin->total_poin,
                    'keterangan' => $poin->keterangan_tambahan,
                    'status' => $poin->status_poin_tambahan
                ];
            }

            // Format data poin luar institusi
            foreach ($poinLuarInstitusi as $poin) {
                $data[] = [
                    'id' => $poin->poin_luar_institusi_id,
                    'tipe_poin' => 'luar_institusi',
                    'nama_kegiatan' => $poin->jabatan->kegiatanLuarInstitusi->nama_kegiatan_luar_institusi,
                    'nama_anggota' => $poin->jabatan->user->nama_lengkap,
                    'jabatan' => ucwords(str_replace('_', ' ', $poin->jabatan->jabatan)),
                    'poin_dasar' => $this->getPoinDasar($poin, $poin->jabatan->jabatan),
                    'poin_tambahan' => $poin->poin_tambahan,
                    'total_poin' => $poin->total_poin,
                    'keterangan' => $poin->keterangan_tambahan,
                    'status' => $poin->status_poin_tambahan
                ];
            }

            return response()->json(['data' => $data]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function getPoinDasar($poin, $jabatan)
    {
        switch ($jabatan) {
            case 'ketua_pelaksana':
                return $poin->poin_ketua_pelaksana;
            case 'sekertaris':
                return $poin->poin_sekertaris;
            case 'bendahara':
                return $poin->poin_bendahara;
            case 'anggota':
                return $poin->poin_anggota;
            default:
                return 0;
        }
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required',
            'tipe_poin' => 'required|in:jurusan,prodi,institusi,luar_institusi',
            'status' => 'required|in:disetujui,ditolak'
        ]);

        try {
            DB::beginTransaction();

            $poinModel = match($request->tipe_poin) {
                'jurusan' => PoinJurusanModel::class,
                'prodi' => PoinProgramStudiModel::class,
                'institusi' => PoinInstitusiModel::class,
                'luar_institusi' => PoinLuarInstitusiModel::class,
            };

            $poin = $poinModel::findOrFail($request->id);
            $poin->status_poin_tambahan = $request->status;
            $poin->approved_by = auth()->id();
            $poin->approved_at = now();

            // Update total poin hanya jika disetujui
            if ($request->status === 'disetujui') {
                $poin->total_poin = $poin->hitungTotalPoin();
            } else {
                // Jika ditolak, kembalikan ke poin dasar
                $poin->total_poin = $this->getPoinDasar($poin, $poin->jabatan->jabatan);
                $poin->poin_tambahan = 0;
            }

            $poin->save();

            DB::commit();

            return response()->json([
                'message' => 'Status poin berhasil diperbarui',
                'status' => 'success'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
                'status' => 'error'
            ], 500);
        }
    }
}