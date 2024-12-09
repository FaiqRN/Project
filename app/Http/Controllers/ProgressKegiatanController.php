<?php

namespace App\Http\Controllers;

use App\Models\AgendaModel;
use App\Models\JabatanModel;
use Illuminate\Http\Request;

class ProgressKegiatanController extends Controller
{
    public function index()
    {
        $userId = session('user_id');

        $kegiatanJurusan = JabatanModel::where('user_id', $userId)
            ->whereNotNull('kegiatan_jurusan_id')
            ->with(['kegiatanJurusan', 'kegiatanJurusan.agendas'])
            ->get()
            ->map(function($jabatan) {
                return $this->calculateProgress($jabatan->kegiatanJurusan, 'kegiatan_jurusan_id');
            })->filter();

        $kegiatanProdi = JabatanModel::where('user_id', $userId)
            ->whereNotNull('kegiatan_program_studi_id')
            ->with(['kegiatanProgramStudi', 'kegiatanProgramStudi.agendas'])
            ->get()
            ->map(function($jabatan) {
                return $this->calculateProgress($jabatan->kegiatanProgramStudi, 'kegiatan_program_studi_id');
            })->filter();

        return view('dosen.progress-kegiatan.index', compact('kegiatanJurusan', 'kegiatanProdi'));
    }

    private function calculateProgress($kegiatan, $kegiatanType)
    {
        if (!$kegiatan) return null;

        $agendas = AgendaModel::where($kegiatanType, $kegiatan->getKey())
            ->withCount('users')
            ->get();

        $totalAgendas = $agendas->count();
        $completedAgendas = $agendas->where('status_agenda', 'selesai')->count();
        $progressPercentage = $totalAgendas > 0 ? ($completedAgendas / $totalAgendas) * 100 : 0;

        $kegiatan->progress = [
            'total_agendas' => $totalAgendas,
            'completed_agendas' => $completedAgendas,
            'percentage' => round($progressPercentage, 2),
            'total_members' => $agendas->sum('users_count')
        ];

        return $kegiatan;
    }
}