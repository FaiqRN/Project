<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EvaluasiSawModel;
use App\Models\HasilSawModel;
use App\Models\PoinJurusanModel;
use App\Models\PoinProgramStudiModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MobileSAWController extends Controller
{
    private $bobot = [
        'poin_dasar' => 0.50,
        'poin_tambahan' => 0.30,
        'status_poin' => 0.20
    ];

    private $nilai_status = [
        'disetujui' => 1.0,
        'pending' => 0.5,
        'ditolak' => 0
    ];

    // Mendapatkan evaluasi dan hasil SAW terbaru
    public function getLatestEvaluation()
    {
        try {
            // Ambil evaluasi terbaru dengan hasilnya
            $evaluasi = EvaluasiSawModel::with(['hasilSaw' => function($query) {
                $query->with('user:user_id,nidn,nama_lengkap')
                      ->orderBy('ranking', 'asc');
            }])
            ->latest('periode_selesai')
            ->first();

            if (!$evaluasi) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Belum ada evaluasi',
                    'data' => null
                ]);
            }

            // Format data untuk grafik (diurutkan dari nilai terendah)
            $chartData = $evaluasi->hasilSaw->map(function($hasil) {
                return [
                    'nama' => $hasil->user->nama_lengkap,
                    'nidn' => $hasil->user->nidn,
                    'nilai_saw' => round($hasil->nilai_akhir_saw, 4),
                    'ranking' => $hasil->ranking
                ];
            })->sortBy('nilai_saw')->values();

            return response()->json([
                'status' => 'success',
                'message' => 'Data evaluasi berhasil diambil',
                'data' => [
                    'periode' => [
                        'mulai' => Carbon::parse($evaluasi->periode_mulai)->format('Y-m-d'),
                        'selesai' => Carbon::parse($evaluasi->periode_selesai)->format('Y-m-d')
                    ],
                    'hasil' => $chartData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Mendapatkan riwayat semua evaluasi SAW
    public function getEvaluationHistory()
    {
        try {
            $evaluations = EvaluasiSawModel::select('evaluasi_id', 'periode_mulai', 'periode_selesai')
                ->orderBy('periode_selesai', 'desc')
                ->get()
                ->map(function($evaluasi) {
                    return [
                        'evaluasi_id' => $evaluasi->evaluasi_id,
                        'periode_mulai' => Carbon::parse($evaluasi->periode_mulai)->format('Y-m-d'),
                        'periode_selesai' => Carbon::parse($evaluasi->periode_selesai)->format('Y-m-d')
                    ];
                });

            return response()->json([
                'status' => 'success',
                'message' => 'Data history evaluasi berhasil diambil',
                'data' => $evaluations
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    // Mendapatkan detail hasil evaluasi SAW tertentu
    public function getEvaluationDetail($evaluasiId)
    {
        try {
            $evaluasi = EvaluasiSawModel::with(['hasilSaw' => function($query) {
                $query->with('user:user_id,nidn,nama_lengkap')
                      ->orderBy('ranking', 'asc');
            }])->findOrFail($evaluasiId);

            $detailData = $evaluasi->hasilSaw->map(function($hasil) {
                return [
                    'nidn' => $hasil->user->nidn,
                    'nama_lengkap' => $hasil->user->nama_lengkap,
                    'poin_dasar' => round($hasil->poin_dasar, 2),
                    'poin_tambahan' => $hasil->poin_tambahan ? round($hasil->poin_tambahan, 2) : null,
                    'status_poin' => $hasil->status_poin,
                    'nilai_saw' => round($hasil->nilai_akhir_saw, 4),
                    'ranking' => $hasil->ranking,
                    'normalisasi' => [
                        'dasar' => round($hasil->nilai_normalisasi_dasar, 4),
                        'tambahan' => round($hasil->nilai_normalisasi_tambahan, 4),
                        'status' => round($hasil->nilai_normalisasi_status, 4)
                    ]
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Detail evaluasi berhasil diambil',
                'data' => [
                    'periode' => [
                        'mulai' => Carbon::parse($evaluasi->periode_mulai)->format('Y-m-d'),
                        'selesai' => Carbon::parse($evaluasi->periode_selesai)->format('Y-m-d')
                    ],
                    'hasil' => $detailData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
