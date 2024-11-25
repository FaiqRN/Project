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
        $validator = Validator::make($request->all(), [
            'nomer_surat' => 'required|unique:m_surat,nomer_surat,' . $id . ',surat_id',
            'judul_surat' => 'required',
            'file_surat' => 'nullable|mimes:pdf|max:2048',
            'tanggal_surat' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ]);
        }

        $surat = SuratModel::findOrFail($id);
        
        // Handle file upload if new file is provided
        if ($request->hasFile('file_surat')) {
            // Delete old file
            Storage::disk('public')->delete($surat->file_surat);
            
            // Store new file
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
}