<?php

namespace App\Http\Controllers;

use App\Models\AgendaModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DataTables;

class PilihAnggotaController extends Controller
{
    // Method untuk PIC
    public function index()
    {
        try {
            return view('pic.pilih-anggota', [
                'breadcrumb' => (object)[
                    'title' => 'Pilih Anggota',
                    'list' => ['Home', 'Agenda', 'Pilih Anggota']
                ],
                'activemenu' => 'agenda'
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method untuk Admin
    public function indexAdmin()
    {
        return view('admin.dosen.agenda.pilih-anggota', [
            'breadcrumb' => (object)[
                'title' => 'Data Anggota',
                'list' => ['Home', 'Dosen', 'Agenda', 'Pilih Anggota']
            ],
            'activemenu' => 'agenda'
        ]);
    }

    public function getAnggota()
    {
        try {
            $anggota = AgendaModel::where('user_id', session('user_id'))
                                 ->select('agenda_id', 'nidn', 'nama_anggota', 'nama_agenda', 'tanggal_agenda')
                                 ->orderBy('tanggal_agenda', 'desc')
                                 ->get();

            return response()->json([
                'status' => 'success',
                'data' => $anggota
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAnggotaAdmin()
    {
        try {
            $anggota = AgendaModel::select('agenda_id', 'nidn', 'nama_anggota', 'nama_agenda', 'tanggal_agenda')
                                 ->orderBy('tanggal_agenda', 'desc')
                                 ->get();

            return response()->json([
                'status' => 'success',
                'data' => $anggota
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nidn' => 'required|string|max:18',
                'nama_anggota' => 'required|string|max:100',
                'nama_agenda' => 'required|string|max:200',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $agenda = new AgendaModel();
            $agenda->nidn = $request->nidn;
            $agenda->nama_anggota = $request->nama_anggota;
            $agenda->nama_agenda = $request->nama_agenda;
            $agenda->tanggal_agenda = now();
            $agenda->user_id = session('user_id');
            $agenda->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Anggota berhasil ditambahkan',
                'data' => $agenda
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $agenda = AgendaModel::findOrFail($id);
            return response()->json($agenda);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nidn' => 'required|string|max:18',
                'nama_anggota' => 'required|string|max:100',
                'nama_agenda' => 'required|string|max:200',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            $agenda = AgendaModel::findOrFail($id);

            // Cek role dan akses
            if (session('level_nama') == 'PIC' && $agenda->user_id != session('user_id')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk mengubah data ini'
                ], 403);
            }

            $agenda->nidn = $request->nidn;
            $agenda->nama_anggota = $request->nama_anggota;
            $agenda->nama_agenda = $request->nama_agenda;
            $agenda->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data anggota berhasil diperbarui',
                'data' => $agenda
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $agenda = AgendaModel::findOrFail($id);

            // Cek role dan akses
            if (session('level_nama') == 'PIC' && $agenda->user_id != session('user_id')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses untuk menghapus data ini'
                ], 403);
            }

            $agenda->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Data anggota berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
