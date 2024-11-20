<?php

namespace App\Http\Controllers;

use App\Models\SuratModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class SuratController extends Controller
{
    /**
     * Display view based on user role
     */
    public function index($role = 'admin')
    {
        $viewPath = match($role) {
            'dosen' => 'dosen.surat-tugas.index',
            'kaprodi' => 'kaprodi.surat-tugas.index',
            default => 'admin.kaprodi.surat-tugas'
        };

        return view($viewPath, [
            'breadcrumb' => (object)[
                'title' => 'Manajemen Surat Tugas',
                'list' => ['Home', ucfirst($role), 'Surat Tugas']
            ],
            'activemenu' => 'surat-tugas'
        ]);
    }

    /**
     * Get data for datatables
     */
    public function getData()
    {
        try {
            $query = SuratModel::query()->orderBy('created_at', 'desc');
            
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row) {
                    return view('admin.kaprodi.surat-tugas.actions', compact('row'));
                })
                ->editColumn('tanggal_surat', function($row) {
                    return date('d-m-Y', strtotime($row->tanggal_surat));
                })
                ->rawColumns(['action'])
                ->make(true);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store new surat
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomer_surat' => 'required|unique:m_surat,nomer_surat',
            'judul_surat' => 'required',
            'tanggal_surat' => 'required|date',
            'file_surat' => 'required|mimes:pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $file = $request->file('file_surat');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('surat_tugas', $fileName, 'public');

            $surat = SuratModel::create([
                'nomer_surat' => $request->nomer_surat,
                'judul_surat' => $request->judul_surat,
                'tanggal_surat' => $request->tanggal_surat,
                'file_surat' => $filePath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Surat berhasil ditambahkan',
                'data' => $surat
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show specific surat
     */
    public function show($id)
    {
        try {
            $surat = SuratModel::findOrFail($id);
            return response()->json($surat);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    /**
     * Update surat
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nomer_surat' => 'required|unique:m_surat,nomer_surat,' . $id . ',surat_id',
            'judul_surat' => 'required',
            'tanggal_surat' => 'required|date',
            'file_surat' => 'nullable|mimes:pdf|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $surat = SuratModel::findOrFail($id);
            
            if ($request->hasFile('file_surat')) {
                Storage::disk('public')->delete($surat->file_surat);
                $file = $request->file('file_surat');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('surat_tugas', $fileName, 'public');
                $surat->file_surat = $filePath;
            }

            $surat->nomer_surat = $request->nomer_surat;
            $surat->judul_surat = $request->judul_surat;
            $surat->tanggal_surat = $request->tanggal_surat;
            $surat->save();

            return response()->json([
                'success' => true,
                'message' => 'Surat berhasil diupdate',
                'data' => $surat
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete surat
     */
    public function destroy($id)
    {
        try {
            $surat = SuratModel::findOrFail($id);
            Storage::disk('public')->delete($surat->file_surat);
            $surat->delete();

            return response()->json([
                'success' => true,
                'message' => 'Surat berhasil dihapus'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View PDF file
     */
    public function viewPdf($id)
    {
        try {
            $surat = SuratModel::findOrFail($id);
            $path = storage_path('app/public/' . $surat->file_surat);

            if (!file_exists($path)) {
                abort(404);
            }

            $file = file_get_contents($path);
            $type = mime_content_type($path);

            return Response::make($file, 200, [
                'Content-Type' => $type,
                'Content-Disposition' => 'inline; filename="' . basename($path) . '"'
            ]);
        } catch (Exception $e) {
            abort(404);
        }
    }
}