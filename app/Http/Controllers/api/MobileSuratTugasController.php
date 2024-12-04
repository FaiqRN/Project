<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuratModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MobileSuratTugasController extends Controller
{
    public function index()
    {
        try {
            $suratTugas = SuratModel::orderBy('created_at', 'desc')->get();
            return response()->json([
                'status' => 200,
                'data' => $suratTugas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Internal server error'
            ], 500);
        }
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
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
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
                'status' => 201,
                'message' => 'Surat Tugas created successfully',
                'data' => $surat
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Internal server error'
            ], 500);
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
                'message' => 'Surat Tugas not found'
            ], 404);
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
                'status' => 422,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $surat = SuratModel::findOrFail($id);
            
            if ($request->hasFile('file_surat')) {
                if ($surat->file_surat && Storage::disk('public')->exists($surat->file_surat)) {
                    Storage::disk('public')->delete($surat->file_surat);
                }
                
                $file = $request->file('file_surat');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('surat-tugas', $fileName, 'public');
                $surat->file_surat = $filePath;
            }

            $surat->update([
                'nomer_surat' => $request->nomer_surat,
                'judul_surat' => $request->judul_surat,
                'tanggal_surat' => $request->tanggal_surat
            ]);

            return response()->json([
                'status' => 200,
                'message' => 'Surat Tugas updated successfully',
                'data' => $surat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $surat = SuratModel::findOrFail($id);
            if ($surat->file_surat && Storage::disk('public')->exists($surat->file_surat)) {
                Storage::disk('public')->delete($surat->file_surat);
            }
            $surat->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Surat Tugas deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Internal server error'
            ], 500);
        }
    }

    public function download($id)
    {
        try {
            $surat = SuratModel::findOrFail($id);
            $filePath = storage_path('app/public/' . $surat->file_surat);
            
            if (!file_exists($filePath)) {
                return response()->json([
                    'status' => 404,
                    'message' => 'File not found'
                ], 404);
            }
            
            return response()->download($filePath);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Internal server error'
            ], 500);
        }
    }
}
