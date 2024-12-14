<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use App\Models\KegiatanInstitusiModel;
use App\Models\KegiatanLuarInstitusiModel;
use App\Models\PoinJurusanModel;
use App\Models\PoinProgramStudiModel;
use App\Models\PoinInstitusiModel;
use App\Models\PoinLuarInstitusiModel;
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
        $data = [];

        try {
            DB::beginTransaction();

            // Kegiatan Jurusan
            $kegiatanJurusan = KegiatanJurusanModel::with(['jabatan.user', 'jabatan.poinJurusan'])
                ->where('user_id', $userId)
                ->where('status_kegiatan', 'selesai')
                ->get();

            // Kegiatan Prodi
            $kegiatanProdi = KegiatanProgramStudiModel::with(['jabatan.user', 'jabatan.poinProdi'])
                ->where('user_id', $userId)
                ->where('status_kegiatan', 'selesai')
                ->get();

            // Kegiatan Institusi
            $kegiatanInstitusi = KegiatanInstitusiModel::with(['jabatan.user'])
                ->where('user_id', $userId)
                ->where('status_kegiatan', 'selesai')
                ->get();

            // Kegiatan Luar Institusi
            $kegiatanLuarInstitusi = KegiatanLuarInstitusiModel::with(['jabatan.user'])
                ->where('user_id', $userId)
                ->where('status_kegiatan', 'selesai')
                ->get();

            // Proses kegiatan jurusan
            foreach ($kegiatanJurusan as $kegiatan) {
                foreach ($kegiatan->jabatan as $jabatan) {
                    $poin = PoinJurusanModel::firstOrCreate(
                        ['jabatan_id' => $jabatan->jabatan_id],
                        [
                            'kegiatan_jurusan_id' => $kegiatan->kegiatan_jurusan_id,
                            'poin_ketua_pelaksana' => 4,
                            'poin_sekertaris' => 2.5,
                            'poin_bendahara' => 2.5,
                            'poin_anggota' => 2,
                            'total_poin' => $this->hitungPoinDasar($jabatan->jabatan)
                        ]
                    );

                    $data[] = $this->formatDataPoin($kegiatan, $jabatan, $poin, 'jurusan');
                }
            }

            // Proses kegiatan prodi
            foreach ($kegiatanProdi as $kegiatan) {
                foreach ($kegiatan->jabatan as $jabatan) {
                    $poin = PoinProgramStudiModel::firstOrCreate(
                        ['jabatan_id' => $jabatan->jabatan_id],
                        [
                            'kegiatan_program_studi_id' => $kegiatan->kegiatan_program_studi_id,
                            'poin_ketua_pelaksana' => 4,
                            'poin_sekertaris' => 2.5,
                            'poin_bendahara' => 2.5,
                            'poin_anggota' => 2,
                            'total_poin' => $this->hitungPoinDasar($jabatan->jabatan)
                        ]
                    );

                    $data[] = $this->formatDataPoin($kegiatan, $jabatan, $poin, 'prodi');
                }
            }

            // Proses kegiatan institusi
            foreach ($kegiatanInstitusi as $kegiatan) {
                foreach ($kegiatan->jabatan as $jabatan) {
                    $poin = PoinInstitusiModel::firstOrCreate(
                        ['jabatan_id' => $jabatan->jabatan_id],
                        [
                            'kegiatan_institusi_id' => $kegiatan->kegiatan_institusi_id,
                            'poin_ketua_pelaksana' => 5,
                            'poin_sekertaris' => 3.5,
                            'poin_bendahara' => 3.5,
                            'poin_anggota' => 3,
                            'total_poin' => $this->hitungPoinDasar($jabatan->jabatan)
                        ]
                    );

                    $data[] = $this->formatDataPoin($kegiatan, $jabatan, $poin, 'institusi');
                }
            }

            // Proses kegiatan luar institusi
            foreach ($kegiatanLuarInstitusi as $kegiatan) {
                foreach ($kegiatan->jabatan as $jabatan) {
                    $poin = PoinLuarInstitusiModel::firstOrCreate(
                        ['jabatan_id' => $jabatan->jabatan_id],
                        [
                            'kegiatan_luar_institusi_id' => $kegiatan->kegiatan_luar_institusi_id,
                            'poin_ketua_pelaksana' => 6,
                            'poin_sekertaris' => 4,
                            'poin_bendahara' => 4,
                            'poin_anggota' => 3,
                            'total_poin' => $this->hitungPoinDasar($jabatan->jabatan)
                        ]
                    );

                    $data[] = $this->formatDataPoin($kegiatan, $jabatan, $poin, 'luar_institusi');
                }
            }

            DB::commit();
            return response()->json(['data' => $data]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function formatDataPoin($kegiatan, $jabatan, $poin, $type)
    {
        $poinDasar = $this->getPoinDasar($poin, $jabatan->jabatan);
        $totalPoin = $poin->hitungTotalPoin();

        $namaKegiatan = match($type) {
            'jurusan' => $kegiatan->nama_kegiatan_jurusan,
            'prodi' => $kegiatan->nama_kegiatan_program_studi,
            'institusi' => $kegiatan->nama_kegiatan_institusi,
            'luar_institusi' => $kegiatan->nama_kegiatan_luar_institusi,
        };

        return [
            'kegiatan_id' => $kegiatan->getKey(),
            'tipe_kegiatan' => $type,
            'nama_kegiatan' => $namaKegiatan,
            'nama_anggota' => $jabatan->user->nama_lengkap,
            'jabatan' => ucwords(str_replace('_', ' ', $jabatan->jabatan)),
            'poin_dasar' => $poinDasar,
            'poin_tambahan' => $poin->poin_tambahan ?? 0,
            'total_poin' => $totalPoin,
            'keterangan_tambahan' => $poin->keterangan_tambahan ?? '',
            'status_poin' => $poin->status_poin_tambahan ?? 'belum_ada',
            'jabatan_id' => $jabatan->jabatan_id,
            'can_add_points' => empty($poin->status_poin_tambahan) || $poin->status_poin_tambahan === 'belum_ada'
        ];
    }

    private function hitungPoinDasar($jabatan, $tipeKegiatan = null)
    {
        switch ($tipeKegiatan) {
            case 'jurusan':
                switch ($jabatan) {
                    case 'ketua_pelaksana': return 4;
                    case 'sekertaris': return 2.5;
                    case 'bendahara': return 2.5;
                    case 'anggota': return 2;
                }
                break;
    
            case 'prodi':
                switch ($jabatan) {
                    case 'ketua_pelaksana': return 4;
                    case 'sekertaris': return 2.5;
                    case 'bendahara': return 2.5;
                    case 'anggota': return 2;
                }
                break;
    
            case 'institusi':
                switch ($jabatan) {
                    case 'ketua_pelaksana': return 5;
                    case 'sekertaris': return 3.5;
                    case 'bendahara': return 3.5;
                    case 'anggota': return 3;
                }
                break;
    
            case 'luar_institusi':
                switch ($jabatan) {
                    case 'ketua_pelaksana': return 6;
                    case 'sekertaris': return 4;
                    case 'bendahara': return 4;
                    case 'anggota': return 3;
                }
                break;
    
            default:
                return 0;
        }
        return 0;
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

    public function tambahPoin(Request $request)
    {
        $request->validate([
            'jabatan_id' => 'required|exists:t_jabatan,jabatan_id',
            'poin_tambahan' => 'required|integer|min:1|max:3',
            'keterangan_tambahan' => 'required|string|max:255',
            'tipe_kegiatan' => 'required|in:jurusan,prodi,institusi,luar_institusi'
        ]);

        try {
            DB::beginTransaction();

            $jabatan = JabatanModel::findOrFail($request->jabatan_id);
            $poin = null;

            switch($request->tipe_kegiatan) {
                case 'jurusan':
                    $poin = PoinJurusanModel::firstOrCreate(
                        ['jabatan_id' => $jabatan->jabatan_id],
                        [
                            'kegiatan_jurusan_id' => $jabatan->kegiatan_jurusan_id,
                            'poin_ketua_pelaksana' => 4,
                            'poin_sekertaris' => 2.5,
                            'poin_bendahara' => 2.5,
                            'poin_anggota' => 2,
                            'total_poin' => 0
                        ]
                    );
                    break;
                    
                case 'prodi':
                    $poin = PoinProgramStudiModel::firstOrCreate(
                        ['jabatan_id' => $jabatan->jabatan_id],
                        [
                            'kegiatan_program_studi_id' => $jabatan->kegiatan_program_studi_id,
                            'poin_ketua_pelaksana' => 4,
                            'poin_sekertaris' => 2.5,
                            'poin_bendahara' => 2.5,
                            'poin_anggota' => 2,
                            'total_poin' => 0
                        ]
                    );
                    break;

                case 'institusi':
                    $poin = PoinInstitusiModel::firstOrCreate(
                        ['jabatan_id' => $jabatan->jabatan_id],
                        [
                            'kegiatan_institusi_id' => $jabatan->kegiatan_institusi_id,
                            'poin_ketua_pelaksana' => 5,
                            'poin_sekertaris' => 3.5,
                            'poin_bendahara' => 3.5,
                            'poin_anggota' => 3,
                            'total_poin' => 0
                        ]
                    );
                    break;

                case 'luar_institusi':
                    $poin = PoinLuarInstitusiModel::firstOrCreate(
                        ['jabatan_id' => $jabatan->jabatan_id],
                        [
                            'kegiatan_luar_institusi_id' => $jabatan->kegiatan_luar_institusi_id,
                            'poin_ketua_pelaksana' => 6,
                            'poin_sekertaris' => 4,
                            'poin_bendahara' => 4,
                            'poin_anggota' => 3,
                            'total_poin' => 0
                        ]
                    );
                    break;
            }

            if ($poin) {
                $poin->poin_tambahan = $request->poin_tambahan;
                $poin->keterangan_tambahan = $request->keterangan_tambahan;
                $poin->status_poin_tambahan = 'pending';
                $poin->total_poin = $poin->hitungTotalPoin();
                $poin->save();
            }

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
        
        try {
            $totalPoinJurusan = DB::table('t_poin_jurusan as pj')
                ->join('t_jabatan as j', 'pj.jabatan_id', '=', 'j.jabatan_id')
                ->where('j.user_id', $userId)
                ->sum(DB::raw('pj.total_poin'));

            $totalPoinProdi = DB::table('t_poin_program_studi as pp')
                ->join('t_jabatan as j', 'pp.jabatan_id', '=', 'j.jabatan_id')
                ->where('j.user_id', $userId)
                ->sum(DB::raw('pp.total_poin'));

            $totalPoinInstitusi = DB::table('t_poin_institusi as pi')
                ->join('t_jabatan as j', 'pi.jabatan_id', '=', 'j.jabatan_id')
                ->where('j.user_id', $userId)
                ->sum(DB::raw('pi.total_poin'));

            $totalPoinLuarInstitusi = DB::table('t_poin_luar_institusi as pl')
                ->join('t_jabatan as j', 'pl.jabatan_id', '=', 'j.jabatan_id')
                ->where('j.user_id', $userId)
                ->sum(DB::raw('pl.total_poin'));

            return response()->json([
                'total_poin_jurusan' => $totalPoinJurusan,
                'total_poin_prodi' => $totalPoinProdi,
                'total_poin_institusi' => $totalPoinInstitusi,
                'total_poin_luar_institusi' => $totalPoinLuarInstitusi,
                'total_keseluruhan' => $totalPoinJurusan + $totalPoinProdi + 
                                     $totalPoinInstitusi + $totalPoinLuarInstitusi
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}