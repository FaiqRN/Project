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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembagianPoinController extends Controller
{
    private $modelConfigs = [
        'LuarInstitusi' => [
            'kegiatan' => KegiatanLuarInstitusiModel::class,
            'poin' => PoinLuarInstitusiModel::class,
            'relation' => 'kegiatan_luar_institusi_id',
            'nama_field' => 'nama_kegiatan_luar_institusi',
            'poin_dasar' => [
                'ketua_pelaksana' => 5,
                'sekertaris' => 4,
                'bendahara' => 4,
                'anggota' => 3
            ]
        ],
        'Institusi' => [
            'kegiatan' => KegiatanInstitusiModel::class,
            'poin' => PoinInstitusiModel::class,
            'relation' => 'kegiatan_institusi_id',
            'nama_field' => 'nama_kegiatan_institusi',
            'poin_dasar' => [
                'ketua_pelaksana' => 4,
                'sekertaris' => 4,
                'bendahara' => 4,
                'anggota' => 3
            ]
        ],
        'Jurusan' => [
            'kegiatan' => KegiatanJurusanModel::class,
            'poin' => PoinJurusanModel::class,
            'relation' => 'kegiatan_jurusan_id',
            'nama_field' => 'nama_kegiatan_jurusan',
            'poin_dasar' => [
                'ketua_pelaksana' => 3,
                'sekertaris' => 3,
                'bendahara' => 3,
                'anggota' => 2
            ]
        ],
        'ProgramStudi' => [
            'kegiatan' => KegiatanProgramStudiModel::class,
            'poin' => PoinProgramStudiModel::class,
            'relation' => 'kegiatan_program_studi_id',
            'nama_field' => 'nama_kegiatan_program_studi',
            'poin_dasar' => [
                'ketua_pelaksana' => 3,
                'sekertaris' => 3,
                'bendahara' => 3,
                'anggota' => 2
            ]
        ]
    ];

    public function index()
    {
        try {
            return view('pic.pembagian-poin', [
                'breadcrumb' => (object)[
                    'title' => 'Penambahan Poin',
                    'list' => ['Home', 'Agenda', 'Penambahan Poin']
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada halaman pembagian poin:', [
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat halaman');
        }
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

                // Get completed activities
                $completedActivities = $kegiatanClass::where('status_kegiatan', 'selesai')->get();
                
                foreach ($completedActivities as $activity) {
                    // Get jabatan records
                    $jabatanRecords = JabatanModel::where($relationField, $activity->getKey())
                        ->with(['user'])
                        ->get();

                    foreach ($jabatanRecords as $jabatan) {
                        // Get or create poin record
                        $poin = $poinClass::firstOrCreate(
                            [
                                'jabatan_id' => $jabatan->jabatan_id,
                                $relationField => $activity->getKey()
                            ],
                            [
                                'poin_ketua_pelaksana' => $config['poin_dasar']['ketua_pelaksana'],
                                'poin_sekertaris' => $config['poin_dasar']['sekertaris'],
                                'poin_bendahara' => $config['poin_dasar']['bendahara'],
                                'poin_anggota' => $config['poin_dasar']['anggota'],
                                'total_poin' => $this->getBasePoints($jabatan->jabatan, $type)
                            ]
                        );

                        $formattedData[] = [
                            'id' => $poin->getKey(),
                            'jenis' => str_replace('_', ' ', $type),
                            'nama_user' => $jabatan->user->nama_lengkap,
                            'nama_kegiatan' => $activity->$namaField,
                            'jabatan' => ucwords(str_replace('_', ' ', $jabatan->jabatan)),
                            'status_kegiatan' => $activity->status_kegiatan,
                            'poin_dasar' => $this->getBasePoints($jabatan->jabatan, $type),
                            'poin_tambahan' => $poin->poin_tambahan ?? 0,
                            'total_poin' => $poin->total_poin,
                            'status_poin_tambahan' => $poin->status_poin_tambahan ?? 'belum ditambahkan',
                            'keterangan_tambahan' => $poin->keterangan_tambahan ?? '-',
                            'can_add_points' => empty($poin->status_poin_tambahan)
                        ];
                    }
                }
            }

            return response()->json([
                'draw' => request()->input('draw', 1),
                'recordsTotal' => count($formattedData),
                'recordsFiltered' => count($formattedData),
                'data' => $formattedData
            ]);

        } catch (\Exception $e) {
            Log::error('Error dalam getDataPoin', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan dalam memproses data'
            ], 500);
        }
    }

    private function getBasePoints($jabatan, $type)
    {
        return $this->modelConfigs[$type]['poin_dasar'][$jabatan] ?? 0;
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

            $poinDasar = $this->getBasePoints($poin->jabatan->jabatan, $type);
            
            $poin->update([
                'poin_tambahan' => $validated['poin_tambahan'],
                'keterangan_tambahan' => $validated['keterangan_tambahan'],
                'status_poin_tambahan' => 'pending',
                'total_poin' => $poinDasar + $validated['poin_tambahan']
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Poin berhasil ditambahkan'
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