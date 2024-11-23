<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = UserModel::with('level')
                ->whereIn('level_id', [2, 3,4]);

            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('level_nama', function($row) {  
                    return $row->level->level_nama;
                })
                ->addColumn('action', function($row){
                    return '
                    <div class="btn-group">
                        <button type="button" class="btn btn-warning btn-sm" onclick="showEditModal('.$row->user_id.')">
                            Edit
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('.$row->user_id.')">
                            Hapus
                        </button>
                    </div>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    
        $breadcrumb = (object)[
            'title' => 'Manajemen User',
            'list' => ['Home', 'Users']
        ];
    
        return view('admin.users.index', compact('breadcrumb'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'level_id' => 'required|in:2,3,4',
            'username' => 'required|string|max:20|unique:m_user',
            'password' => 'required|min:6',
            'nama_lengkap' => 'required|string|max:100',
            'nidn' => 'required|string|max:18|unique:m_user',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'jabatan_fungsional' => 'nullable|string|max:100',
            'program_studi' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userData = $request->only([
                'level_id',
                'username',
                'password',
                'nama_lengkap',
                'nidn',
                'gelar_depan',
                'gelar_belakang',
                'jabatan_fungsional',
                'program_studi'
            ]);
            $userData['password'] = Hash::make($request->password);
            


            if (session()->has('username')) {
                $userData['created_by'] = session('username');
            }

            if ($request->hasFile('foto')) {
                $image = $request->file('foto');
                $img = Image::make($image->getRealPath());
                
                // Resize jika ukuran melebihi maksimum
                if ($img->width() > 800 || $img->height() > 800) {
                    $img->resize(800, 800, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                
                // Kompres gambar
                $img->encode('jpg', 80);
                
                // Konversi ke base64
                $userData['foto'] = 'data:image/jpeg;base64,' . base64_encode($img->__toString());
            }

            UserModel::create($userData);

            return response()->json([
                'status' => 'success',
                'message' => 'User berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = UserModel::with('level')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $user = UserModel::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'level_id' => 'required|in:2,3,4',
            'username' => 'required|string|max:20|unique:m_user,username,' . $id . ',user_id',
            'password' => 'nullable|min:6',
            'nama_lengkap' => 'required|string|max:100',
            'nidn' => 'required|string|max:18|unique:m_user,nidn,' . $id . ',user_id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'jabatan_fungsional' => 'required|string|max:100',
            'program_studi' => 'required|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userData = [
                'level_id' => $request->level_id,
                'username' => $request->username,
                'nama_lengkap' => $request->nama_lengkap,
                'nidn' => $request->nidn,
                'gelar_depan' => $request->gelar_depan,
                'gelar_belakang' => $request->gelar_belakang,
                'jabatan_fungsional' => $request->jabatan_fungsional,
                'program_studi' => $request->program_studi,
                'updated_by' => session('username') ?? 'system',
                'updated_at' => now()
            ];
            
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('foto')) {
                $image = $request->file('foto');
                $img = Image::make($image->getRealPath());
                
                if ($img->width() > 800 || $img->height() > 800) {
                    $img->resize(800, 800, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }
                
                $img->encode('jpg', 80);
                $userData['foto'] = 'data:image/jpeg;base64,' . base64_encode($img->__toString());
            }

            $user->update($userData);

            return response()->json([
                'status' => 'success',
                'message' => 'Data user berhasil diperbarui'
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
            $user = UserModel::findOrFail($id);
            
            // Validasi untuk mencegah penghapusan diri sendiri
            if ($user->user_id === session('user_id')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak dapat menghapus akun Anda sendiri'
                ], 403);
            }
    
            // Validasi untuk mencegah penghapusan admin
            if ($user->level_id === 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Tidak dapat menghapus akun administrator'
                ], 403);
            }
            
            if($user->delete()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'User berhasil dihapus'
                ]);
            }
    
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus user'
            ], 500);
    
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting user: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menghapus data'
            ], 500);
        }
    }
}