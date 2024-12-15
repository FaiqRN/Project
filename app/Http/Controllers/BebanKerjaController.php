<?php

namespace App\Http\Controllers;

use App\Models\TotalPoinDosenModel;
use App\Models\UserModel;
use App\Models\PoinJurusanModel;
use App\Models\PoinProgramStudiModel;
use App\Models\PoinInstitusiModel;
use App\Models\PoinLuarInstitusiModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Maatwebsite\Excel\Facades\Excel;
class BebanKerjaController extends Controller
{
    public function index()
    {
        return view('kaprodi.beban-kerja.statistik', [
            'breadcrumb' => (object)[
                'title' => 'Statistik Beban Kerja',
                'list' => ['Home', 'Statistik', 'Beban Kerja']
            ]
        ]);
    }

    public function getStatistikData(Request $request)
    {
        try {
            // Parse tanggal dari request dengan validasi
            $startDate = $request->has('start_date') ? 
                        Carbon::parse($request->start_date)->startOfDay() : 
                        Carbon::now()->subMonths(6)->startOfDay();
            $endDate = $request->has('end_date') ? 
                      Carbon::parse($request->end_date)->endOfDay() : 
                      Carbon::now()->endOfDay();

            // Ambil semua user dosen dan PIC
            $dosen = UserModel::whereHas('level', function($q) {
                $q->whereIn('level_nama', ['Dosen', 'PIC']);
            })->get();

            $data = [];
            $maxPoin = 0;

            foreach($dosen as $d) {
                // Hitung poin untuk setiap jenis kegiatan
                $poinData = $this->calculateUserPoints($d->user_id, $startDate, $endDate);
                $totalPoin = array_sum($poinData);
                
                if($totalPoin > $maxPoin) {
                    $maxPoin = $totalPoin;
                }

                $data[] = array_merge([
                    'user_id' => $d->user_id,
                    'nama' => $d->nama_lengkap,
                    'nidn' => $d->nidn,
                    'program_studi' => $d->program_studi
                ], $poinData);
            }

            // Normalisasi dengan SAW (nilai lebih kecil = prioritas lebih tinggi)
            $normalizedData = $this->normalizeDataSAW($data, $maxPoin);

            return response()->json([
                'status' => 'success',
                'data' => $normalizedData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function detailPoin()
    {
        return view('kaprodi.beban-kerja.detail-poin', [
            'breadcrumb' => (object)[
                'title' => 'Detail Poin Beban Kerja',
                'list' => ['Home', 'Statistik', 'Detail Poin']
            ]
        ]);
    }

    public function getDetailData(Request $request)
    {
        try {
            $startDate = $request->has('start_date') ? 
                        Carbon::parse($request->start_date)->startOfDay() : 
                        Carbon::now()->subMonths(6)->startOfDay();
            $endDate = $request->has('end_date') ? 
                      Carbon::parse($request->end_date)->endOfDay() : 
                      Carbon::now()->endOfDay();

            $detailData = $this->getCompleteDetailData($startDate, $endDate);

            return response()->json([
                'status' => 'success',
                'data' => $detailData
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadPDF(Request $request)
    {
        try {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
    
            $data = $this->getCompleteDetailData($startDate, $endDate);
    
            // Generate PDF content
            $html = view('kaprodi.beban-kerja.pdf', [
                'data' => $data,
                'periode' => [
                    'start' => $startDate->format('d M Y'),
                    'end' => $endDate->format('d M Y')
                ]
            ])->render();
    
            // Create PDF using DomPDF
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
    
            // Output PDF for download
            return $dompdf->stream('laporan-beban-kerja.pdf', [
                "Attachment" => true
            ]);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunduh PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    public function downloadExcel(Request $request)
    {
        try {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
    
            // Ambil data
            $data = $this->getCompleteDetailData($startDate, $endDate);
    
            // Buat spreadsheet baru
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
    
            // Set header
            $sheet->setCellValue('A1', 'Nama');
            $sheet->setCellValue('B1', 'NIDN');
            $sheet->setCellValue('C1', 'Program Studi');
            $sheet->setCellValue('D1', 'Poin Jurusan');
            $sheet->setCellValue('E1', 'Poin Prodi');
            $sheet->setCellValue('F1', 'Poin Institusi');
            $sheet->setCellValue('G1', 'Poin Luar Institusi');
            $sheet->setCellValue('H1', 'Total Poin');
            $sheet->setCellValue('I1', 'Ranking');
    
            // Isi data
            $row = 2;
            foreach($data as $item) {
                $sheet->setCellValue('A'.$row, $item['nama']);
                $sheet->setCellValue('B'.$row, $item['nidn']);
                $sheet->setCellValue('C'.$row, $item['program_studi']);
                $sheet->setCellValue('D'.$row, $item['total_poin']['jurusan']);
                $sheet->setCellValue('E'.$row, $item['total_poin']['prodi']);
                $sheet->setCellValue('F'.$row, $item['total_poin']['institusi']);
                $sheet->setCellValue('G'.$row, $item['total_poin']['luar_institusi']);
                $sheet->setCellValue('H'.$row, $item['total_keseluruhan']);
                $sheet->setCellValue('I'.$row, $row-1);
                $row++;
            }
    
            // Auto-size columns
            foreach(range('A','I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
    
            // Create Excel file
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            
            // Set header untuk download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="laporan-beban-kerja.xlsx"');
            header('Cache-Control: max-age=0');
    
            $writer->save('php://output');
            exit;
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengunduh Excel: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculateUserPoints($userId, $startDate, $endDate)
    {
        // Hitung poin jurusan
        $poinJurusan = PoinJurusanModel::whereHas('jabatan', function($q) use($userId) {
            $q->where('user_id', $userId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('total_poin');

        // Hitung poin prodi
        $poinProdi = PoinProgramStudiModel::whereHas('jabatan', function($q) use($userId) {
            $q->where('user_id', $userId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('total_poin');

        // Hitung poin institusi
        $poinInstitusi = PoinInstitusiModel::whereHas('jabatan', function($q) use($userId) {
            $q->where('user_id', $userId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('total_poin');

        // Hitung poin luar institusi
        $poinLuar = PoinLuarInstitusiModel::whereHas('jabatan', function($q) use($userId) {
            $q->where('user_id', $userId);
        })
        ->whereBetween('created_at', [$startDate, $endDate])
        ->sum('total_poin');

        return [
            'poin_jurusan' => $poinJurusan,
            'poin_prodi' => $poinProdi,
            'poin_institusi' => $poinInstitusi,
            'poin_luar' => $poinLuar,
            'total_poin' => $poinJurusan + $poinProdi + $poinInstitusi + $poinLuar
        ];
    }

    private function normalizeDataSAW($data, $maxPoin)
    {
        foreach($data as &$item) {
            // Inverse normalization karena semakin kecil poin, semakin tinggi prioritas
            $item['nilai_saw'] = $maxPoin > 0 ? (($maxPoin - $item['total_poin']) / $maxPoin) : 0;
        }

        // Urutkan berdasarkan nilai SAW (descending)
        usort($data, function($a, $b) {
            return $b['nilai_saw'] <=> $a['nilai_saw'];
        });

        // Tambahkan ranking
        foreach($data as $index => &$item) {
            $item['ranking'] = $index + 1;
        }

        return $data;
    }

    private function getCompleteDetailData($startDate, $endDate)
    {
        $detailData = [];

        // Ambil semua user dosen dan PIC
        $users = UserModel::whereHas('level', function($q) {
            $q->whereIn('level_nama', ['Dosen', 'PIC']);
        })->get();

        foreach($users as $user) {
            // Ambil semua kegiatan jurusan
            $kegiatanJurusan = PoinJurusanModel::with(['jabatan.kegiatanJurusan'])
                ->whereHas('jabatan', function($q) use($user) {
                    $q->where('user_id', $user->user_id);
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Ambil semua kegiatan prodi
            $kegiatanProdi = PoinProgramStudiModel::with(['jabatan.kegiatanProgramStudi'])
                ->whereHas('jabatan', function($q) use($user) {
                    $q->where('user_id', $user->user_id);
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Ambil semua kegiatan institusi
            $kegiatanInstitusi = PoinInstitusiModel::with(['jabatan.kegiatanInstitusi'])
                ->whereHas('jabatan', function($q) use($user) {
                    $q->where('user_id', $user->user_id);
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Ambil semua kegiatan luar institusi
            $kegiatanLuar = PoinLuarInstitusiModel::with(['jabatan.kegiatanLuarInstitusi'])
                ->whereHas('jabatan', function($q) use($user) {
                    $q->where('user_id', $user->user_id);
                })
                ->whereBetween('created_at', [$startDate, $endDate])
                ->get();

            // Hitung total poin
            $totalPoin = [
                'jurusan' => $kegiatanJurusan->sum('total_poin'),
                'prodi' => $kegiatanProdi->sum('total_poin'),
                'institusi' => $kegiatanInstitusi->sum('total_poin'),
                'luar_institusi' => $kegiatanLuar->sum('total_poin')
            ];

            $detailData[] = [
                'user_id' => $user->user_id,
                'nama' => $user->nama_lengkap,
                'nidn' => $user->nidn,
                'program_studi' => $user->program_studi,
                'jabatan_fungsional' => $user->jabatan_fungsional,
                'kegiatan_jurusan' => $kegiatanJurusan,
                'kegiatan_prodi' => $kegiatanProdi,
                'kegiatan_institusi' => $kegiatanInstitusi,
                'kegiatan_luar' => $kegiatanLuar,
                'total_poin' => $totalPoin,
                'total_keseluruhan' => array_sum($totalPoin)
            ];
        }

        return $detailData;
    }
}