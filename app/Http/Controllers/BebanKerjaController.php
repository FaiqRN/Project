<?php

namespace App\Http\Controllers;

use App\Models\TotalPoinDosenModel;
use App\Models\UserModel;
use App\Models\PoinJurusanModel;
use App\Models\PoinProgramStudiModel;
use App\Models\PoinInstitusiModel;
use App\Models\PoinLuarInstitusiModel;
use App\Models\JabatanModel;
use App\Models\KegiatanLuarInstitusiModel;
use App\Models\KegiatanInstitusiModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BebanKerjaController extends Controller
{
    public function index()
    {
        $currentMonth = Carbon::now();
        $totalDosen = $this->hitungTotalDosen();
        $totalKegiatan = $this->hitungTotalKegiatan($currentMonth);
        $totalPoin = $this->hitungTotalPoin($currentMonth);

        return view('kaprodi.beban-kerja.statistik', [
            'totalDosen' => $totalDosen,
            'totalKegiatan' => $totalKegiatan,
            'totalPoin' => $totalPoin,
            'currentMonth' => $currentMonth->format('F Y'),
            'breadcrumb' => (object)[
                'title' => 'Statistik Beban Kerja',
                'list' => ['Home', 'Beban Kerja', 'Statistik']
            ]
        ]);
    }

    // Fungsi menghitung total dosen
    private function hitungTotalDosen()
    {
        return DB::table('m_user')->where('level_id', 3)->count();
    }

    // Fungsi menghitung total kegiatan selesai
    private function hitungTotalKegiatan($currentMonth)
    {
        $startDate = $currentMonth->copy()->startOfMonth();
        $endDate = $currentMonth->copy()->endOfMonth();

        $tables = [
            't_kegiatan_jurusan',
            't_kegiatan_program_studi',
            't_kegiatan_institusi',
            't_kegiatan_luar_institusi',
        ];

        $total = 0;

        foreach ($tables as $table) {
            $total += DB::table($table)
                ->where('status_kegiatan', 'selesai')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();
        }

        return $total;
    }

    // Fungsi menghitung total poin
    private function hitungTotalPoin($currentMonth)
    {
        $startDate = $currentMonth->copy()->startOfMonth();
        $endDate = $currentMonth->copy()->endOfMonth();

        $queries = [
            ['t_poin_jurusan', 't_kegiatan_jurusan', 'kegiatan_jurusan_id'],
            ['t_poin_program_studi', 't_kegiatan_program_studi', 'kegiatan_program_studi_id'],
            ['t_poin_institusi', 't_kegiatan_institusi', 'kegiatan_institusi_id'],
            ['t_poin_luar_institusi', 't_kegiatan_luar_institusi', 'kegiatan_luar_institusi_id']
        ];

        $totalPoin = 0;

        foreach ($queries as $query) {
            [$poinTable, $kegiatanTable, $kegiatanId] = $query;

            $totalPoin += DB::table($poinTable)
                ->join('t_jabatan', "$poinTable.jabatan_id", '=', 't_jabatan.jabatan_id')
                ->join($kegiatanTable, "t_jabatan.$kegiatanId", '=', "$kegiatanTable.$kegiatanId")
                ->where("$kegiatanTable.status_kegiatan", 'selesai')
                ->whereBetween("$kegiatanTable.created_at", [$startDate, $endDate])
                ->sum("$poinTable.total_poin");
        }

        return $totalPoin;
    }

    public function getStatistikData(Request $request)
    {
        $bulan = $request->get('bulan', date('Y-m'));
        $startDate = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $endDate = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();

        $data = DB::table('m_user as u')
            ->select(
                'u.nama_lengkap',
                DB::raw('SUM(
                    COALESCE(pj.total_poin, 0) +
                    COALESCE(pp.total_poin, 0) +
                    COALESCE(pi.total_poin, 0) +
                    COALESCE(pl.total_poin, 0)
                ) as total_poin')
            )
            ->leftJoin('t_jabatan as j', 'u.user_id', '=', 'j.user_id')
            ->leftJoin('t_poin_jurusan as pj', 'j.jabatan_id', '=', 'pj.jabatan_id')
            ->leftJoin('t_poin_program_studi as pp', 'j.jabatan_id', '=', 'pp.jabatan_id')
            ->leftJoin('t_poin_institusi as pi', 'j.jabatan_id', '=', 'pi.jabatan_id')
            ->leftJoin('t_poin_luar_institusi as pl', 'j.jabatan_id', '=', 'pl.jabatan_id')
            ->where('u.level_id', 3)
            ->groupBy('u.user_id', 'u.nama_lengkap')
            ->havingRaw('SUM(
                COALESCE(pj.total_poin, 0) +
                COALESCE(pp.total_poin, 0) +
                COALESCE(pi.total_poin, 0) +
                COALESCE(pl.total_poin, 0)
            ) > 0')
            ->get();

        return response()->json([
            'labels' => $data->pluck('nama_lengkap'),
            'poin' => $data->pluck('total_poin')
        ]);
    }

    public function getDetailData(Request $request)
    {
        $data = DB::table('m_user as u')
            ->select(
                'u.nama_lengkap as nama_dosen',
                DB::raw("COALESCE(kj.nama_kegiatan_jurusan, kp.nama_kegiatan_program_studi, ki.nama_kegiatan_institusi, kl.nama_kegiatan_luar_institusi) as nama_kegiatan"),
                DB::raw("CASE 
                    WHEN kj.nama_kegiatan_jurusan IS NOT NULL THEN 'Kegiatan Jurusan' 
                    WHEN kp.nama_kegiatan_program_studi IS NOT NULL THEN 'Kegiatan Program Studi'
                    WHEN ki.nama_kegiatan_institusi IS NOT NULL THEN 'Kegiatan Institusi'
                    ELSE 'Kegiatan Luar Institusi'
                END as jenis_kegiatan"),
                DB::raw("COALESCE(kj.tanggal_mulai, kp.tanggal_mulai, ki.tanggal_mulai, kl.tanggal_mulai) as tanggal"),
                'kj.status_kegiatan as status',
                DB::raw('COALESCE(pj.total_poin, 0) as poin_jti'),
                DB::raw('COALESCE(pl.total_poin, 0) as poin_non_jti'),
                DB::raw('COALESCE(pj.total_poin, 0) + COALESCE(pl.total_poin, 0) as total_poin')
            )
            ->leftJoin('t_jabatan as j', 'u.user_id', '=', 'j.user_id')
            ->leftJoin('t_kegiatan_jurusan as kj', 'j.kegiatan_jurusan_id', '=', 'kj.kegiatan_jurusan_id')
            ->leftJoin('t_poin_jurusan as pj', 'j.jabatan_id', '=', 'pj.jabatan_id')
            ->leftJoin('t_kegiatan_program_studi as kp', 'j.kegiatan_program_studi_id', '=', 'kp.kegiatan_program_studi_id')
            ->leftJoin('t_kegiatan_institusi as ki', 'j.kegiatan_institusi_id', '=', 'ki.kegiatan_institusi_id')
            ->leftJoin('t_kegiatan_luar_institusi as kl', 'j.kegiatan_luar_institusi_id', '=', 'kl.kegiatan_luar_institusi_id')
            ->leftJoin('t_poin_luar_institusi as pl', 'j.jabatan_id', '=', 'pl.jabatan_id')
            ->where('u.level_id', 3)
            ->where('kj.status_kegiatan', 'selesai')
            ->orWhere('kp.status_kegiatan', 'selesai')
            ->orWhere('ki.status_kegiatan', 'selesai')
            ->orWhere('kl.status_kegiatan', 'selesai')
            ->get();
    
        return response()->json(['data' => $data]);
    }




}