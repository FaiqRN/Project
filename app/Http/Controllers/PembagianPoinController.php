<?php

namespace App\Http\Controllers;

use App\Models\KegiatanInstitusiModel;
use App\Models\KegiatanJurusanModel;
use App\Models\KegiatanLuarInstitusiModel;
use App\Models\KegiatanProgramStudiModel;
use App\Models\PoinInstitusiModel;
use App\Models\PoinJurusanModel;
use App\Models\PoinLuarInstitusiModel;
use App\Models\PoinProgramStudiModel;
use App\Models\JabatanModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembagianPoinController extends Controller
{
    private $modelConfigs = [
        'LuarInstitusi' => [
            'kegiatan' => KegiatanLuarInstitusiModel::class,
            'poin' => PoinLuarInstitusiModel::class,
            'relation' => 'kegiatan_luar_institusi_id',
            'nama_field' => 'nama_kegiatan_luar_institusi'
        ],
        'Institusi' => [
            'kegiatan' => KegiatanInstitusiModel::class,
            'poin' => PoinInstitusiModel::class,
            'relation' => 'kegiatan_institusi_id',
            'nama_field' => 'nama_kegiatan_institusi'
        ],
        'Jurusan' => [
            'kegiatan' => KegiatanJurusanModel::class,
            'poin' => PoinJurusanModel::class,
            'relation' => 'kegiatan_jurusan_id',
            'nama_field' => 'nama_kegiatan_jurusan'
        ],
        'ProgramStudi' => [
            'kegiatan' => KegiatanProgramStudiModel::class,
            'poin' => PoinProgramStudiModel::class,
            'relation' => 'kegiatan_program_studi_id',
            'nama_field' => 'nama_kegiatan_program_studi'
        ]
    ];

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
            $formattedData = [];

            foreach ($this->modelConfigs as $type => $config) {
                $kegiatanClass = $config['kegiatan'];
                $poinClass = $config['poin'];
                $relationField = $config['relation'];
                $namaField = $config['nama_field'];

                $completedActivities = $kegiatanClass::where('status_kegiatan', 'selesai')->get();
                
                foreach ($completedActivities as $activity) {
                    $jabatanRecords = JabatanModel::where($relationField, $activity->getKey())
                        ->with(['user'])
                        ->get();

                    foreach ($jabatanRecords as $jabatan) {
                        $poin = $poinClass::firstOrCreate(
                            [
                                'jabatan_id' => $jabatan->jabatan_id,
                                $relationField => $activity->getKey()
                            ]
                        );

                        $formattedData[] = [
                            'id' => $poin->getKey(),
                            'jenis' => str_replace('_', ' ', $type),
                            'nama_user' => $jabatan->user->nama_lengkap,
                            'nama_kegiatan' => $activity->$namaField,
                            'jabatan' => ucwords(str_replace('_', ' ', $jabatan->jabatan)),
                            'status_kegiatan' => $activity->status_kegiatan,
                            'poin_dasar' => $poin->{"poin_" . $jabatan->jabatan},
                            'poin_tambahan' => $poin->poin_tambahan ?? 0,
                            'total_poin' => $poin->total_poin,
                            'status_poin_tambahan' => $poin->status_poin_tambahan ?? '-',
                            'keterangan_tambahan' => $poin->keterangan_tambahan ?? '-',
                            'can_add_points' => empty($poin->status_poin_tambahan)
                        ];
                    }
                }
            }

            return response()->json([
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            Log::error('Error dalam getDataPoin:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Terjadi kesalahan dalam memproses data'], 500);
        }
    }

    public function tambahPoin(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'id' => 'required',
                'jenis' => 'required|in:Luar Institusi,Institusi,Jurusan,Program Studi',
                'poin_tambahan' => 'required|integer|min:1|max:3',
                'keterangan_tambahan' => 'required|string|min:10'
            ]);

            $type = str_replace(' ', '', $validated['jenis']);
            $poinClass = $this->modelConfigs[$type]['poin'];
            
            $poin = $poinClass::findOrFail($validated['id']);

            if (!empty($poin->status_poin_tambahan)) {
                throw new \Exception('Poin tambahan sudah pernah diberikan');
            }

            $poin->update([
                'poin_tambahan' => $validated['poin_tambahan'],
                'keterangan_tambahan' => $validated['keterangan_tambahan'],
                'status_poin_tambahan' => 'pending',
                'total_poin' => $poin->{"poin_" . $poin->jabatan->jabatan} + $validated['poin_tambahan']
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Poin berhasil ditambahkan']);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}