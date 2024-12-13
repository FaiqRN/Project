<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
            $data = DB::table('t_agenda as a')
                ->select([
                    'j.jabatan_id',
                    'j.jabatan',
                    'u.nama_lengkap as nama_user',
                    DB::raw('CASE 
                        WHEN a.kegiatan_jurusan_id IS NOT NULL THEN "Jurusan"
                        WHEN a.kegiatan_program_studi_id IS NOT NULL THEN "Program Studi"
                        WHEN a.kegiatan_institusi_id IS NOT NULL THEN "Institusi"
                        WHEN a.kegiatan_luar_institusi_id IS NOT NULL THEN "Luar Institusi"
                    END as jenis_kegiatan'),
                    DB::raw('CASE 
                        WHEN a.kegiatan_jurusan_id IS NOT NULL THEN kj.nama_kegiatan_jurusan
                        WHEN a.kegiatan_program_studi_id IS NOT NULL THEN kp.nama_kegiatan_program_studi
                        WHEN a.kegiatan_institusi_id IS NOT NULL THEN ki.nama_kegiatan_institusi
                        WHEN a.kegiatan_luar_institusi_id IS NOT NULL THEN kl.nama_kegiatan_luar_institusi
                    END as nama_kegiatan'),
                    'a.status_agenda',
                    DB::raw('COALESCE(pj.poin_jurusan_id, pp.poin_program_studi_id, pi.poin_institusi_id, pl.poin_luar_institusi_id) as poin_id'),
                    DB::raw('COALESCE(pj.poin_tambahan, pp.poin_tambahan, pi.poin_tambahan, pl.poin_tambahan, 0) as poin_tambahan'),
                    DB::raw('COALESCE(pj.status_poin_tambahan, pp.status_poin_tambahan, pi.status_poin_tambahan, pl.status_poin_tambahan) as status_poin_tambahan'),
                    DB::raw('COALESCE(pj.keterangan_tambahan, pp.keterangan_tambahan, pi.keterangan_tambahan, pl.keterangan_tambahan) as keterangan_tambahan')
                ])
                ->join('t_jabatan as j', function($join) {
                    $join->on('j.kegiatan_jurusan_id', '=', 'a.kegiatan_jurusan_id')
                        ->orOn('j.kegiatan_program_studi_id', '=', 'a.kegiatan_program_studi_id')
                        ->orOn('j.kegiatan_institusi_id', '=', 'a.kegiatan_institusi_id')
                        ->orOn('j.kegiatan_luar_institusi_id', '=', 'a.kegiatan_luar_institusi_id');
                })
                ->join('m_user as u', 'j.user_id', '=', 'u.user_id')
                ->leftJoin('t_kegiatan_jurusan as kj', 'a.kegiatan_jurusan_id', '=', 'kj.kegiatan_jurusan_id')
                ->leftJoin('t_kegiatan_program_studi as kp', 'a.kegiatan_program_studi_id', '=', 'kp.kegiatan_program_studi_id')
                ->leftJoin('t_kegiatan_institusi as ki', 'a.kegiatan_institusi_id', '=', 'ki.kegiatan_institusi_id')
                ->leftJoin('t_kegiatan_luar_institusi as kl', 'a.kegiatan_luar_institusi_id', '=', 'kl.kegiatan_luar_institusi_id')
                ->leftJoin('t_poin_jurusan as pj', 'j.jabatan_id', '=', 'pj.jabatan_id')
                ->leftJoin('t_poin_program_studi as pp', 'j.jabatan_id', '=', 'pp.jabatan_id')
                ->leftJoin('t_poin_institusi as pi', 'j.jabatan_id', '=', 'pi.jabatan_id')
                ->leftJoin('t_poin_luar_institusi as pl', 'j.jabatan_id', '=', 'pl.jabatan_id')
                ->where('a.status_agenda', '=', 'selesai')
                ->get();

            $formattedData = $data->map(function($item) {
                $poinDasar = $this->getPoinDasar($item->jabatan, $item->jenis_kegiatan);
                
                return [
                    'id' => $item->poin_id,
                    'jenis' => $item->jenis_kegiatan,
                    'nama_user' => $item->nama_user,
                    'nama_kegiatan' => $item->nama_kegiatan,
                    'jabatan' => ucwords(str_replace('_', ' ', $item->jabatan)),
                    'status_kegiatan' => $item->status_agenda,
                    'poin_dasar' => $poinDasar,
                    'poin_tambahan' => $item->poin_tambahan,
                    'total_poin' => $poinDasar + $item->poin_tambahan,
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

    private function getPoinDasar($jabatan, $jenisKegiatan)
    {
        $poinConfig = [
            'Luar Institusi' => [
                'ketua_pelaksana' => 5,
                'sekertaris' => 4,
                'bendahara' => 4,
                'anggota' => 3
            ],
            'Institusi' => [
                'ketua_pelaksana' => 4,
                'sekertaris' => 3.5,
                'bendahara' => 3.5,
                'anggota' => 3
            ],
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

        return $poinConfig[$jenisKegiatan][$jabatan] ?? 0;
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
    
            // Menentukan tabel yang akan diupdate
            $tableName = '';
            $idColumn = '';
            switch ($validated['jenis']) {
                case 'Jurusan':
                    $tableName = 't_poin_jurusan';
                    $idColumn = 'poin_jurusan_id';
                    break;
                case 'Program Studi':
                    $tableName = 't_poin_program_studi';
                    $idColumn = 'poin_program_studi_id';
                    break;
                case 'Institusi':
                    $tableName = 't_poin_institusi';
                    $idColumn = 'poin_institusi_id';
                    break;
                case 'Luar Institusi':
                    $tableName = 't_poin_luar_institusi';
                    $idColumn = 'poin_luar_institusi_id';
                    break;
            }
    
            // Ambil data poin yang sudah ada
            $existingPoin = DB::table($tableName)
                ->where($idColumn, $validated['id'])
                ->first();
    
            if (!$existingPoin) {
                throw new \Exception('Data poin tidak ditemukan');
            }
    
            // Update data dengan poin tambahan dan status pending
            DB::table($tableName)
                ->where($idColumn, $validated['id'])
                ->update([
                    'poin_tambahan' => $validated['poin_tambahan'],
                    'keterangan_tambahan' => $validated['keterangan_tambahan'],
                    'status_poin_tambahan' => 'pending',
                    // Total poin tetap menggunakan poin dasar karena status masih pending
                    'total_poin' => $existingPoin->{"poin_" . $existingPoin->jabatan},
                    'updated_at' => now()
                ]);
    
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Poin berhasil ditambahkan dan menunggu persetujuan admin'
            ]);
    
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


}