<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MobileProgressKegiatanController extends Controller
{
    public function getProgress()
    {
        try {
            // Query untuk kegiatan jurusan
            $kegiatanJurusan = DB::table('t_kegiatan_jurusan as kj')
                ->select([
                    'kj.kegiatan_jurusan_id as kegiatan_id',
                    'kj.nama_kegiatan_jurusan as nama_kegiatan',
                    'kj.tanggal_mulai',
                    'kj.tanggal_selesai',
                    DB::raw("'kegiatan_jurusan' as jenis_kegiatan"),
                    DB::raw('COUNT(DISTINCT a.agenda_id) as total_agenda'),
                    DB::raw('COUNT(DISTINCT a.dokumentasi_id) as agenda_selesai'),
                    DB::raw('COUNT(DISTINCT pa.user_id) as jumlah_anggota')
                ])
                ->leftJoin('t_agenda as a', 'kj.kegiatan_jurusan_id', '=', 'a.kegiatan_jurusan_id')
                ->leftJoin('t_pilih_anggota as pa', 'kj.kegiatan_jurusan_id', '=', 'pa.kegiatan_jurusan_id')
                ->where('kj.user_id', Auth::id())
                ->orWhereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('t_pilih_anggota')
                        ->whereColumn('t_pilih_anggota.kegiatan_jurusan_id', 'kj.kegiatan_jurusan_id')
                        ->where('t_pilih_anggota.user_id', Auth::id());
                })
                ->groupBy(
                    'kj.kegiatan_jurusan_id',
                    'kj.nama_kegiatan_jurusan',
                    'kj.tanggal_mulai',
                    'kj.tanggal_selesai'
                );

            // Query untuk kegiatan prodi
            $kegiatanProdi = DB::table('t_kegiatan_program_studi as kp')
                ->select([
                    'kp.kegiatan_program_studi_id as kegiatan_id',
                    'kp.nama_kegiatan_program_studi as nama_kegiatan',
                    'kp.tanggal_mulai',
                    'kp.tanggal_selesai',
                    DB::raw("'kegiatan_prodi' as jenis_kegiatan"),
                    DB::raw('COUNT(DISTINCT a.agenda_id) as total_agenda'),
                    DB::raw('COUNT(DISTINCT a.dokumentasi_id) as agenda_selesai'),
                    DB::raw('COUNT(DISTINCT pa.user_id) as jumlah_anggota')
                ])
                ->leftJoin('t_agenda as a', 'kp.kegiatan_program_studi_id', '=', 'a.kegiatan_program_studi_id')
                ->leftJoin('t_pilih_anggota as pa', 'kp.kegiatan_program_studi_id', '=', 'pa.kegiatan_program_studi_id')
                ->where('kp.user_id', Auth::id())
                ->orWhereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('t_pilih_anggota')
                        ->whereColumn('t_pilih_anggota.kegiatan_program_studi_id', 'kp.kegiatan_program_studi_id')
                        ->where('t_pilih_anggota.user_id', Auth::id());
                })
                ->groupBy(
                    'kp.kegiatan_program_studi_id',
                    'kp.nama_kegiatan_program_studi',
                    'kp.tanggal_mulai',
                    'kp.tanggal_selesai'
                );

            // Gabungkan hasil query
            $kegiatan = $kegiatanJurusan->union($kegiatanProdi)->get();

            // Transformasi data untuk response
            $result = $kegiatan->map(function ($item) {
                $progressPercentage = $item->total_agenda > 0 
                    ? round(($item->agenda_selesai / $item->total_agenda) * 100)
                    : 0;

                return [
                    'kegiatan_id' => $item->kegiatan_id,
                    'nama_kegiatan' => $item->nama_kegiatan,
                    'jenis_kegiatan' => $item->jenis_kegiatan,
                    'tanggal_mulai' => $item->tanggal_mulai,
                    'tanggal_selesai' => $item->tanggal_selesai,
                    'total_agenda' => $item->total_agenda,
                    'agenda_selesai' => $item->agenda_selesai,
                    'progress_percentage' => $progressPercentage,
                    'jumlah_anggota' => $item->jumlah_anggota
                ];
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data progress kegiatan berhasil diambil',
                'data' => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDetailProgress($id, $type)
    {
        try {
            $table = $type === 'kegiatan_jurusan' ? 't_kegiatan_jurusan' : 't_kegiatan_program_studi';
            $idColumn = $type === 'kegiatan_jurusan' ? 'kegiatan_jurusan_id' : 'kegiatan_program_studi_id';
            
            // Query untuk mendapatkan detail agenda
            $agendas = DB::table('t_agenda as a')
                ->select([
                    'a.agenda_id',
                    'a.nama_agenda',
                    'a.tanggal_agenda',
                    'a.deskripsi',
                    DB::raw('CASE WHEN a.dokumentasi_id IS NOT NULL THEN true ELSE false END as is_completed')
                ])
                ->where("a.$type" . '_id', $id)
                ->orderBy('a.tanggal_agenda')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Detail progress berhasil diambil',
                'data' => $agendas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}