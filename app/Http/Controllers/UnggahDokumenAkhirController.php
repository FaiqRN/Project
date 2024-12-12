<?php

namespace App\Http\Controllers;

use App\Models\AgendaModel;
use App\Models\FinalDocumentModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UnggahDokumenAkhirController extends Controller
{
    public function index()
    {
        return view('pic.unggah-dokumen', [
            'breadcrumb' => (object)[
                'title' => 'Unggah Dokumen Akhir',
                'list' => ['Home', 'Agenda', 'Unggah Dokumen Akhir']
            ]
        ]);
    }

    public function getKegiatanList()
    {
        $userId = session('user_id');
        
        // Get agenda dengan eager loading
        $agendas = AgendaModel::with([
                'kegiatanJurusan',
                'kegiatanProgramStudi', 
                'kegiatanInstitusi',
                'kegiatanLuarInstitusi'
            ])
            ->where('user_id', $userId)
            ->get();
    
        $kegiatanList = [];
    
        foreach($agendas as $agenda) {
            // Tentukan jenis kegiatan dan ambil datanya
            if($agenda->kegiatan_jurusan_id && $agenda->kegiatanJurusan) {
                $kegiatanList[] = [
                    'id' => $agenda->kegiatan_jurusan_id,
                    'nama_kegiatan' => $agenda->kegiatanJurusan->nama_kegiatan_jurusan,
                    'tanggal_mulai' => $agenda->kegiatanJurusan->tanggal_mulai,
                    'tanggal_selesai' => $agenda->kegiatanJurusan->tanggal_selesai,
                    'status' => $agenda->status_agenda,
                    'tipe_kegiatan' => 'jurusan'
                ];
            }
            elseif($agenda->kegiatan_program_studi_id && $agenda->kegiatanProgramStudi) {
                $kegiatanList[] = [
                    'id' => $agenda->kegiatan_program_studi_id,
                    'nama_kegiatan' => $agenda->kegiatanProgramStudi->nama_kegiatan_program_studi,
                    'tanggal_mulai' => $agenda->kegiatanProgramStudi->tanggal_mulai,
                    'tanggal_selesai' => $agenda->kegiatanProgramStudi->tanggal_selesai,
                    'status' => $agenda->status_agenda,
                    'tipe_kegiatan' => 'program_studi'
                ];
            }
            elseif($agenda->kegiatan_institusi_id && $agenda->kegiatanInstitusi) {
                $kegiatanList[] = [
                    'id' => $agenda->kegiatan_institusi_id,
                    'nama_kegiatan' => $agenda->kegiatanInstitusi->nama_kegiatan_institusi,
                    'tanggal_mulai' => $agenda->kegiatanInstitusi->tanggal_mulai,
                    'tanggal_selesai' => $agenda->kegiatanInstitusi->tanggal_selesai,
                    'status' => $agenda->status_agenda,
                    'tipe_kegiatan' => 'institusi'
                ];
            }
            elseif($agenda->kegiatan_luar_institusi_id && $agenda->kegiatanLuarInstitusi) {
                $kegiatanList[] = [
                    'id' => $agenda->kegiatan_luar_institusi_id,
                    'nama_kegiatan' => $agenda->kegiatanLuarInstitusi->nama_kegiatan_luar_institusi,
                    'tanggal_mulai' => $agenda->kegiatanLuarInstitusi->tanggal_mulai,
                    'tanggal_selesai' => $agenda->kegiatanLuarInstitusi->tanggal_selesai,
                    'status' => $agenda->status_agenda,
                    'tipe_kegiatan' => 'luar_institusi'
                ];
            }
        }
    
        return response()->json([
            'data' => $kegiatanList,
            'debug' => [
                'user_id' => $userId,
                'total_agenda' => $agendas->count()
            ]
        ]);
    }

    private function getTipeKegiatan($agenda)
    {
        if ($agenda->kegiatan_jurusan_id) return 'jurusan';
        if ($agenda->kegiatan_program_studi_id) return 'program_studi';
        if ($agenda->kegiatan_institusi_id) return 'institusi';
        if ($agenda->kegiatan_luar_institusi_id) return 'luar_institusi';
        return null;
    }

    public function store(Request $request)
    {
        $request->validate([
            'kegiatan_id' => 'required',
            'tipe_kegiatan' => 'required|in:jurusan,program_studi,institusi,luar_institusi',
            'file_akhir' => 'required|mimes:pdf|max:20480' // max 20MB
        ]);

        try {
            // Upload file
            $file = $request->file('file_akhir');
            $path = $file->store('public/dokumen-akhir');
            $filePath = str_replace('public/', '', $path);

            // Simpan atau update dokumen akhir
            FinalDocumentModel::updateOrCreate(
                [
                    $request->tipe_kegiatan . '_id' => $request->kegiatan_id
                ],
                [
                    'file_akhir' => $filePath
                ]
            );

            // Update status kegiatan
            $modelClass = $this->getModelClass($request->tipe_kegiatan);
            if($modelClass) {
                $kegiatan = $modelClass::find($request->kegiatan_id);
                if($kegiatan) {
                    $kegiatan->status_kegiatan = 'selesai';
                    $kegiatan->save();

                    // Update status agenda
                    $agenda = AgendaModel::where($request->tipe_kegiatan . '_id', $request->kegiatan_id)->first();
                    if($agenda) {
                        $agenda->status_agenda = 'selesai';
                        $agenda->save();
                    }
                }
            }

            return response()->json(['message' => 'Dokumen akhir berhasil diunggah']);
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal mengunggah dokumen akhir'], 500);
        }
    }

    private function getModelClass($tipe)
    {
        $models = [
            'jurusan' => 'App\Models\KegiatanJurusanModel',
            'program_studi' => 'App\Models\KegiatanProgramStudiModel',
            'institusi' => 'App\Models\KegiatanInstitusiModel',
            'luar_institusi' => 'App\Models\KegiatanLuarInstitusiModel'
        ];

        return isset($models[$tipe]) ? $models[$tipe] : null;
    }

    public function download($id, $tipe)
    {
        $document = FinalDocumentModel::where($tipe . '_id', $id)->first();
        
        if (!$document || !Storage::exists('public/' . $document->file_akhir)) {
            return response()->json(['message' => 'File tidak ditemukan'], 404);
        }

        return Storage::download('public/' . $document->file_akhir);
    }
}