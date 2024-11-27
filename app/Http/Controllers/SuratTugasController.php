<?php

namespace App\Http\Controllers;

use App\Models\SuratModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SuratTugasController extends Controller
{
    public function index()
    {
        $breadcrumb = (object)[
            'title' => 'Surat Tugas',
            'list' => ['Home', 'Kaprodi', 'Surat Tugas']
        ];
    
        $suratTugas = SuratModel::orderBy('created_at', 'desc')->get();
        return view('admin.kaprodi.surat-tugas.index', compact('breadcrumb', 'suratTugas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomer_surat' => 'required|unique:m_surat,nomer_surat',
            'judul_surat' => 'required',
            'file_surat' => 'required|mimes:pdf|max:2048',
            'tanggal_surat' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ]);
        }

        // Handle file upload
        $file = $request->file('file_surat');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('surat-tugas', $fileName, 'public');

        $surat = SuratModel::create([
            'nomer_surat' => $request->nomer_surat,
            'judul_surat' => $request->judul_surat,
            'file_surat' => $filePath,
            'tanggal_surat' => $request->tanggal_surat
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Surat Tugas berhasil ditambahkan'
        ]);
    }

    public function showkaprodi($id)
    {
        try {
            $surat = SuratModel::findOrFail($id);
            $filePath = storage_path('app/public/' . $surat->file_surat);
            
            if (file_exists($filePath)) {
                $headers = [
                    'Content-Type' => 'application/pdf',
                ];
                
                return response()->file($filePath, $headers);
            }
            
            return response()->json([
                'status' => 404,
                'message' => 'File tidak ditemukan'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat membuka file'
            ]);
        }
    }


    public function show($id)
    {
        try {
            $surat = SuratModel::findOrFail($id);
            return response()->json([
                'status' => 200,
                'data' => $surat,
                'file_url' => asset('storage/' . $surat->file_surat)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 404,
                'message' => 'Data tidak ditemukan'
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nomer_surat' => 'required|unique:m_surat,nomer_surat,' . $id . ',surat_id',
                'judul_surat' => 'required',
                'file_surat' => 'nullable|mimes:pdf|max:2048',
                'tanggal_surat' => 'required|date'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 422,
                    'errors' => $validator->errors()
                ], 422);
            }
    
            $surat = SuratModel::findOrFail($id);
            
            // Handle file upload jika ada file baru
            if ($request->hasFile('file_surat')) {
                // Hapus file lama jika ada
                if ($surat->file_surat && Storage::disk('public')->exists($surat->file_surat)) {
                    Storage::disk('public')->delete($surat->file_surat);
                }
                
                $file = $request->file('file_surat');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('surat-tugas', $fileName, 'public');
                
                $surat->file_surat = $filePath;
            }
    
            $surat->nomer_surat = $request->nomer_surat;
            $surat->judul_surat = $request->judul_surat;
            $surat->tanggal_surat = $request->tanggal_surat;
            $surat->save();
    
            return response()->json([
                'status' => 200,
                'message' => 'Surat Tugas berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan pada server'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $surat = SuratModel::findOrFail($id);
        Storage::disk('public')->delete($surat->file_surat);
        $surat->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Surat Tugas berhasil dihapus'
        ]);
    }

    public function download()
    {
        try {
            $breadcrumb = (object)[
                'title' => 'Download Dokumen Surat Tugas',
                'list' => ['Home', 'Surat Tugas', 'Download Dokumen']
            ];

            $suratTugas = SuratModel::orderBy('tanggal_surat', 'desc')->paginate(10);
            return view('kaprodi.surat-tugas.download', compact('breadcrumb', 'suratTugas'));
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengambil data');
        }
    }

    public function downloadSurat($id)
    {
        try {
            $surat = SuratModel::findOrFail($id);
            $filePath = storage_path('app/public/' . $surat->file_surat);
            
            if (file_exists($filePath)) {
                return response()->download($filePath)->deleteFileAfterSend(false);
            }
            
            return back()->with('error', 'File tidak ditemukan');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengunduh file');
        }
    }
}