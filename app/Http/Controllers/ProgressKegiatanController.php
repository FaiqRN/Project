<?php


namespace App\Http\Controllers;


use App\Models\AgendaModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanProgramStudiModel;
use App\Models\KegiatanInstitusiModel;
use App\Models\KegiatanLuarInstitusiModel;
use Illuminate\Http\Request;


class ProgressKegiatanController extends Controller
{
    public function index()
    {
        return view('dosen.statuskegiatan', [
            'breadcrumb' => (object)[
                'title' => 'Progress Kegiatan',
                'list' => ['Home', 'Progress Kegiatan']
            ]
        ]);
    }


    public function getKegiatanProgress()
    {
        // Mengambil kegiatan jurusan dengan agenda
        $kegiatanJurusan = KegiatanJurusanModel::with(['agendas' => function($query) {
            $query->withCount('users');
        }])->get()->map(function($kegiatan) {
            $totalAgendas = $kegiatan->agendas->count();
            $completedAgendas = $kegiatan->agendas->where('status_agenda', 'selesai')->count();
            $progress = $totalAgendas > 0 ? ($completedAgendas / $totalAgendas) * 100 : 0;
           
            return [
                'nama_kegiatan' => $kegiatan->nama_kegiatan_jurusan,
                'jenis_kegiatan' => 'Kegiatan Jurusan',
                'jumlah_agenda' => $totalAgendas,
                'agenda_selesai' => $completedAgendas,
                'progress' => round($progress, 2),
                'jumlah_anggota' => $kegiatan->agendas->sum('users_count')
            ];
        });


        // Mengambil kegiatan prodi dengan agenda
        $kegiatanProdi = KegiatanProgramStudiModel::with(['agendas' => function($query) {
            $query->withCount('users');
        }])->get()->map(function($kegiatan) {
            $totalAgendas = $kegiatan->agendas->count();
            $completedAgendas = $kegiatan->agendas->where('status_agenda', 'selesai')->count();
            $progress = $totalAgendas > 0 ? ($completedAgendas / $totalAgendas) * 100 : 0;
           
            return [
                'nama_kegiatan' => $kegiatan->nama_kegiatan_program_studi,
                'jenis_kegiatan' => 'Kegiatan Prodi',
                'jumlah_agenda' => $totalAgendas,
                'agenda_selesai' => $completedAgendas,
                'progress' => round($progress, 2),
                'jumlah_anggota' => $kegiatan->agendas->sum('users_count')
            ];
        });

        $kegiatanInstitusi = KegiatanInstitusiModel::with(['agendas' => function($query) {
            $query->withCount('users');
        }])->get()->map(function($kegiatan) {
            $totalAgendas = $kegiatan->agendas->count();
            $completedAgendas = $kegiatan->agendas->where('status_agenda', 'selesai')->count();
            $progress = $totalAgendas > 0 ? ($completedAgendas / $totalAgendas) * 100 : 0;
           
            return [
                'nama_kegiatan' => $kegiatan->nama_kegiatan_institusi,
                'jenis_kegiatan' => 'Kegiatan Institusi',
                'jumlah_agenda' => $totalAgendas,
                'agenda_selesai' => $completedAgendas,
                'progress' => round($progress, 2),
                'jumlah_anggota' => $kegiatan->agendas->sum('users_count')
            ];
        });

        $kegiatanLuarInstitusi = KegiatanLuarInstitusiModel::with(['agendas' => function($query) {
            $query->withCount('users');
        }])->get()->map(function($kegiatan) {
            $totalAgendas = $kegiatan->agendas->count();
            $completedAgendas = $kegiatan->agendas->where('status_agenda', 'selesai')->count();
            $progress = $totalAgendas > 0 ? ($completedAgendas / $totalAgendas) * 100 : 0;
           
            return [
                'nama_kegiatan' => $kegiatan->nama_kegiatan_luar_institusi,
                'jenis_kegiatan' => 'Kegiatan Luar Institusi',
                'jumlah_agenda' => $totalAgendas,
                'agenda_selesai' => $completedAgendas,
                'progress' => round($progress, 2),
                'jumlah_anggota' => $kegiatan->agendas->sum('users_count')
            ];
        });

        $allKegiatan = $kegiatanJurusan->concat($kegiatanProdi)->concat($kegiatanInstitusi)->concat($kegiatanLuarInstitusi);
       
        return response()->json($allKegiatan);
    }
}