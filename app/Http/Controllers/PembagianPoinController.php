<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PembagianPoinController extends Controller
{
    public function index()
    {
        return view('pic.pembagian-poin', [
            'breadcrumb' => (object)[
                'title' => 'Penambahan Poin',
                'list' => ['Home', 'Agenda', 'Penambahan Poin']
            ]
        ]);
    }

    public function getDataPoin()
    {
        try {
            // Mendapatkan user_id PIC yang sedang login
            $picId = Auth::id();

            $data = DB::table('t_kegiatan_jurusan as kj')
                ->select([
                    'j.jabatan_id',
                    'j.jabatan',
                    'u.nama_lengkap as nama_user',
                    DB::raw('"Jurusan" as jenis'),
                    'kj.nama_kegiatan_jurusan as nama_kegiatan',
                    'kj.status_kegiatan',
                    'pj.poin_jurusan_id as id',
                    'pj.poin_tambahan',
                    'pj.keterangan_tambahan',
                    'pj.status_poin_tambahan'
                ])
                ->join('t_jabatan as j', 'kj.kegiatan_jurusan_id', '=', 'j.kegiatan_jurusan_id')
                ->join('m_user as u', 'j.user_id', '=', 'u.user_id')
                ->leftJoin('t_poin_jurusan as pj', 'j.jabatan_id', '=', 'pj.jabatan_id')
                ->where('kj.user_id', $picId)
                ->where('kj.status_kegiatan', 'selesai')
                ->union(
                    DB::table('t_kegiatan_program_studi as kp')
                    ->select([
                        'j.jabatan_id',
                        'j.jabatan',
                        'u.nama_lengkap as nama_user',
                        DB::raw('"Program Studi" as jenis'),
                        'kp.nama_kegiatan_program_studi as nama_kegiatan',
                        'kp.status_kegiatan',
                        'pp.poin_program_studi_id as id',
                        'pp.poin_tambahan',
                        'pp.keterangan_tambahan',
                        'pp.status_poin_tambahan'
                    ])
                    ->join('t_jabatan as j', 'kp.kegiatan_program_studi_id', '=', 'j.kegiatan_program_studi_id')
                    ->join('m_user as u', 'j.user_id', '=', 'u.user_id')
                    ->leftJoin('t_poin_program_studi as pp', 'j.jabatan_id', '=', 'pp.jabatan_id')
                    ->where('kp.user_id', $picId)
                    ->where('kp.status_kegiatan', 'selesai')
                )
                ->get();

            $formattedData = $data->map(function($item) {
                $poinDasar = $this->getPoinDasar($item->jabatan, $item->jenis);
                
                return [
                    'id' => $item->id,
                    'jenis' => $item->jenis,
                    'nama_user' => $item->nama_user,
                    'nama_kegiatan' => $item->nama_kegiatan,
                    'jabatan' => ucwords(str_replace('_', ' ', $item->jabatan)),
                    'status_kegiatan' => $item->status_kegiatan,
                    'poin_dasar' => $poinDasar,
                    'poin_tambahan' => $item->poin_tambahan ?? 0,
                    'total_poin' => $poinDasar + ($item->status_poin_tambahan === 'disetujui' ? ($item->poin_tambahan ?? 0) : 0),
                    'status_poin_tambahan' => $item->status_poin_tambahan ?? '-',
                    'keterangan_tambahan' => $item->keterangan_tambahan ?? '-',
                    'can_add_points' => empty($item->status_poin_tambahan)
                ];
            });

            return response()->json(['data' => $formattedData]);
        } catch (\Exception $e) {
            Log::error('Error dalam getDataPoin:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Terjadi kesalahan dalam memproses data'], 500);
        }
    }

    private function getPoinDasar($jabatan, $jenis)
    {
        $poinConfig = [
            'Jurusan' => [
                'ketua_pelaksana' => 3,
                'sekertaris' => 2.5,
                'bendahara' => 2.5,
                'anggota' => 2
            ],
            'Program Studi' => [
                'ketua_pelaksana' => 3,
                'sekertaris' => 2.5,
                'bendahara' => 2.5,
                'anggota' => 2
            ]
        ];

        return $poinConfig[$jenis][$jabatan] ?? 0;
    }

    public function tambahPoin(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'id' => 'required',
                'jenis' => 'required',
                'poin_tambahan' => 'required|integer|min:1|max:3',
                'keterangan_tambahan' => 'required|string|min:10'
            ]);

            $tableName = $validated['jenis'] === 'Jurusan' ? 't_poin_jurusan' : 't_poin_program_studi';
            $idColumn = $validated['jenis'] === 'Jurusan' ? 'poin_jurusan_id' : 'poin_program_studi_id';

            // Update poin
            DB::table($tableName)
                ->where($idColumn, $validated['id'])
                ->update([
                    'poin_tambahan' => $validated['poin_tambahan'],
                    'keterangan_tambahan' => $validated['keterangan_tambahan'],
                    'status_poin_tambahan' => 'pending',
                    'updated_at' => now()
                ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Poin berhasil ditambahkan dan menunggu persetujuan admin'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error saat menambah poin:', [
                'message' => $e->getMessage(),
                'data' => $request->all()
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}