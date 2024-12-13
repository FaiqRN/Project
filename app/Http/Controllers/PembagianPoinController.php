<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use App\Models\PoinJurusanModel;
use App\Models\PoinProgramStudiModel;
use App\Models\JabatanModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PembagianPoinController extends Controller
{
    
    public function index()
    {
        return view('pic.poin', [
            'breadcrumb' => (object)[
                'title' => 'Penambahan Poin',
                'list' => ['Home', 'Agenda', 'Penambahan Poin']
            ]
        ]);
    }

    public function getDataPoin()
    {
        $userId = session('user_id');

        // Ambil kegiatan yang sudah selesai dimana user adalah PIC
        $kegiatanJurusan = KegiatanJurusanModel::where('user_id', $userId)
            ->where('status_kegiatan', 'selesai')
            ->get();
            
        $kegiatanProdi = KegiatanProgramStudiModel::where('user_id', $userId)
            ->where('status_kegiatan', 'selesai')
            ->get();

        $data = [];

        // Proses kegiatan jurusan
        foreach ($kegiatanJurusan as $kegiatan) {
            $anggota = JabatanModel::with('user')
                ->where('kegiatan_jurusan_id', $kegiatan->kegiatan_jurusan_id)
                ->get();

            foreach ($anggota as $jabatan) {
                $poin = PoinJurusanModel::where('jabatan_id', $jabatan->jabatan_id)->first();
                
                $data[] = [
                    'kegiatan_id' => $kegiatan->kegiatan_jurusan_id,
                    'tipe_kegiatan' => 'jurusan',
                    'nama_kegiatan' => $kegiatan->nama_kegiatan_jurusan,
                    'nama_anggota' => $jabatan->user->nama_lengkap,
                    'jabatan' => ucwords(str_replace('_', ' ', $jabatan->jabatan)),
                    'poin_dasar' => $this->getPoinDasar($poin, $jabatan->jabatan),
                    'poin_tambahan' => $poin ? $poin->poin_tambahan : 0,
                    'keterangan_tambahan' => $poin ? $poin->keterangan_tambahan : '',
                    'status_poin' => $poin ? $poin->status_poin_tambahan : null,
                    'jabatan_id' => $jabatan->jabatan_id,
                    'can_add_points' => !$poin || empty($poin->status_poin_tambahan)
                ];
            }
        }

        // Proses kegiatan program studi
        foreach ($kegiatanProdi as $kegiatan) {
            $anggota = JabatanModel::with('user')
                ->where('kegiatan_program_studi_id', $kegiatan->kegiatan_program_studi_id)
                ->get();

            foreach ($anggota as $jabatan) {
                $poin = PoinProgramStudiModel::where('jabatan_id', $jabatan->jabatan_id)->first();
                
                $data[] = [
                    'kegiatan_id' => $kegiatan->kegiatan_program_studi_id,
                    'tipe_kegiatan' => 'prodi',
                    'nama_kegiatan' => $kegiatan->nama_kegiatan_program_studi,
                    'nama_anggota' => $jabatan->user->nama_lengkap,
                    'jabatan' => ucwords(str_replace('_', ' ', $jabatan->jabatan)),
                    'poin_dasar' => $this->getPoinDasar($poin, $jabatan->jabatan),
                    'poin_tambahan' => $poin ? $poin->poin_tambahan : 0,
                    'keterangan_tambahan' => $poin ? $poin->keterangan_tambahan : '',
                    'status_poin' => $poin ? $poin->status_poin_tambahan : null,
                    'jabatan_id' => $jabatan->jabatan_id,
                    'can_add_points' => !$poin || empty($poin->status_poin_tambahan)
                ];
            }
        }

        return response()->json(['data' => $data]);
    }

    private function getPoinDasar($poin, $jabatan)
    {
        if (!$poin) return 0;
        
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

    public function tambahPoin(Request $request)
    {
        $request->validate([
            'jabatan_id' => 'required|exists:t_jabatan,jabatan_id',
            'poin_tambahan' => 'required|integer|min:1|max:3',
            'keterangan_tambahan' => 'required|string|max:255',
            'tipe_kegiatan' => 'required|in:jurusan,prodi'
        ]);

        try {
            DB::beginTransaction();

            $jabatan = JabatanModel::with(['kegiatanJurusan', 'kegiatanProgramStudi'])
                ->findOrFail($request->jabatan_id);

            if ($request->tipe_kegiatan === 'jurusan') {
                $poin = PoinJurusanModel::firstOrCreate(
                    ['jabatan_id' => $jabatan->jabatan_id],
                    [
                        'kegiatan_jurusan_id' => $jabatan->kegiatan_jurusan_id,
                        'poin_ketua_pelaksana' => 3,
                        'poin_sekertaris' => 2.5,
                        'poin_bendahara' => 2.5,
                        'poin_anggota' => 2,
                        'total_poin' => 0
                    ]
                );
            } else {
                $poin = PoinProgramStudiModel::firstOrCreate(
                    ['jabatan_id' => $jabatan->jabatan_id],
                    [
                        'kegiatan_program_studi_id' => $jabatan->kegiatan_program_studi_id,
                        'poin_ketua_pelaksana' => 3,
                        'poin_sekertaris' => 2.5,
                        'poin_bendahara' => 2.5,
                        'poin_anggota' => 2,
                        'total_poin' => 0
                    ]
                );
            }

            $poin->poin_tambahan = $request->poin_tambahan;
            $poin->keterangan_tambahan = $request->keterangan_tambahan;
            $poin->status_poin_tambahan = 'pending';
            $poin->total_poin = $poin->hitungTotalPoin();
            $poin->save();

            DB::commit();

            return response()->json([
                'message' => 'Poin tambahan berhasil disimpan',
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

    public function getRingkasanPoin()
    {
        $userId = session('user_id');
        
        // Query untuk mendapatkan total poin dari berbagai jenis kegiatan
        $totalPoinJurusan = DB::table('t_poin_jurusan as pj')
            ->join('t_jabatan as j', 'pj.jabatan_id', '=', 'j.jabatan_id')
            ->where('j.user_id', $userId)
            ->sum(DB::raw('pj.total_poin'));

        $totalPoinProdi = DB::table('t_poin_program_studi as pp')
            ->join('t_jabatan as j', 'pp.jabatan_id', '=', 'j.jabatan_id')
            ->where('j.user_id', $userId)
            ->sum(DB::raw('pp.total_poin'));

        return response()->json([
            'total_poin_jurusan' => $totalPoinJurusan,
            'total_poin_prodi' => $totalPoinProdi,
            'total_keseluruhan' => $totalPoinJurusan + $totalPoinProdi
        ]);
    }
}