<?php

namespace App\Http\Controllers;

use App\Models\EvaluasiSawModel;
use App\Models\HasilSawModel;
use App\Models\PoinJurusanModel;
use App\Models\PoinProgramStudiModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerhitunganSAWController extends Controller
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

    public function index()
    {
        // Ambil evaluasi terbaru
        $evaluasi = EvaluasiSawModel::latest()->first();
        
        // Jika tidak ada evaluasi, tampilkan view kosong
        if (!$evaluasi) {
            return view('dosen.perhitungan-saw.index', [
                'hasil' => null,
                'chartData' => null,
                'periode' => null,
                'breadcrumb' => (object)[
                    'title' => 'Perhitungan SAW',
                    'list' => ['Home', 'SAW']
                ]
            ]);
        }

        // Ambil hasil SAW untuk periode ini
        $hasil = HasilSawModel::with('user')
            ->where('evaluasi_id', $evaluasi->evaluasi_id)
            ->orderBy('ranking', 'asc')
            ->get();

        // Format data untuk grafik
        $chartData = $hasil->map(function($item) {
            return [
                'name' => $item->user->nama_lengkap,
                'nilai' => $item->nilai_akhir_saw
            ];
        })->sortBy('nilai')->values();

        return view('dosen.perhitungan-saw.index', [
            'hasil' => $hasil,
            'chartData' => $chartData,
            'periode' => $evaluasi,
            'breadcrumb' => (object)[
                'title' => 'Perhitungan SAW',
                'list' => ['Home', 'SAW']
            ]
        ]);
    }

    public function hitungSAW(Request $request)
    {
        try {
            DB::beginTransaction();

            // Buat periode evaluasi baru kurun waktu 6 bulan
            $evaluasi = new EvaluasiSawModel();
            $evaluasi->periode_mulai = Carbon::now()->subMonths(6)->startOfDay();
            $evaluasi->periode_selesai = Carbon::now()->endOfDay();
            $evaluasi->save();

            // Ambil semua data poin dalam periode
            $poinData = $this->getPoinData($evaluasi->periode_mulai, $evaluasi->periode_selesai);

            // Hitung nilai maksimum untuk normalisasi
            $maxPoinDasar = 3.0; // Nilai tetap sesuai ketentuan parameter
            $maxPoinTambahan = 3.0; // Maksimal poin tambahan

            // Hitung dan simpan hasil SAW
            $hasilSAW = [];
            foreach ($poinData as $data) {
                $normalisasiDasar = $data->poin_dasar / $maxPoinDasar;
                $normalisasiTambahan = ($data->poin_tambahan ?? 0) / $maxPoinTambahan;
                $normalisasiStatus = $this->nilai_status[$data->status_poin ?? 'disetujui'];

                $nilaiAkhir = 
                    ($normalisasiDasar * $this->bobot['poin_dasar']) +
                    ($normalisasiTambahan * $this->bobot['poin_tambahan']) +
                    ($normalisasiStatus * $this->bobot['status_poin']);

                $hasilSAW[] = [
                    'evaluasi_id' => $evaluasi->evaluasi_id,
                    'user_id' => $data->user_id,
                    'poin_dasar' => $data->poin_dasar,
                    'poin_tambahan' => $data->poin_tambahan,
                    'status_poin' => $data->status_poin ?? 'disetujui',
                    'nilai_normalisasi_dasar' => $normalisasiDasar,
                    'nilai_normalisasi_tambahan' => $normalisasiTambahan,
                    'nilai_normalisasi_status' => $normalisasiStatus,
                    'nilai_akhir_saw' => $nilaiAkhir,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            // Urutkan berdasarkan nilai perhitangan SAW yang telah dilakukan
            usort($hasilSAW, function($a, $b) {
                return $b['nilai_akhir_saw'] <=> $a['nilai_akhir_saw'];
            });

            // Tambahkan ranking
            foreach ($hasilSAW as $key => $hasil) {
                $hasilSAW[$key]['ranking'] = $key + 1;
            }

            // Simpan semua hasil
            HasilSawModel::insert($hasilSAW);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Perhitungan SAW berhasil dilakukan'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getPoinData($startDate, $endDate)
    {
        // Ambil data poin dalam rentang waktu
        $poinJurusan = PoinJurusanModel::join('t_jabatan', 't_poin_jurusan.jabatan_id', '=', 't_jabatan.jabatan_id')
            ->whereBetween('t_poin_jurusan.created_at', [$startDate, $endDate])
            ->select(
                't_jabatan.user_id',
                't_poin_jurusan.poin_dasar',
                't_poin_jurusan.poin_tambahan',
                't_poin_jurusan.status_poin_tambahan as status_poin'
            );

        $poinProdi = PoinProgramStudiModel::join('t_jabatan', 't_poin_program_studi.jabatan_id', '=', 't_jabatan.jabatan_id')
            ->whereBetween('t_poin_program_studi.created_at', [$startDate, $endDate])
            ->select(
                't_jabatan.user_id',
                't_poin_program_studi.poin_dasar',
                't_poin_program_studi.poin_tambahan',
                't_poin_program_studi.status_poin_tambahan as status_poin'
            );

        return $poinJurusan->union($poinProdi)->get();
    }
}
