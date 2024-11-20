<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class MobileUserController extends Controller
{
    /**
     * Get user profile data
     */
    public function profile(Request $request)
    {
        try {
            $user = UserModel::with('level')->find($request->user()->user_id);
            
            return response()->json([
                'success' => true,
                'user' => [
                    'user_id' => $user->user_id,
                    'username' => $user->username,
                    'nama_lengkap' => $user->nama_lengkap,
                    'nidn' => $user->nidn,
                    'foto' => $user->foto,
                    'gelar_depan' => $user->gelar_depan,
                    'gelar_belakang' => $user->gelar_belakang,
                    'jenis_kelamin' => $user->jenis_kelamin,
                    'level_nama' => $user->level->level_nama,
                    'jabatan_fungsional' => $user->jabatan_fungsional,
                    'program_studi' => $user->program_studi,
                    'pendidikan_terakhir' => $user->pendidikan_terakhir,
                    'asal_perguruan_tinggi' => $user->asal_perguruan_tinggi,
                    'tempat_lahir' => $user->tempat_lahir,
                    'tanggal_lahir' => $user->tanggal_lahir,
                    'agama' => $user->agama,
                    'status_nikah' => $user->status_nikah,
                    'status_ikatan_kerja' => $user->status_ikatan_kerja,
                    'alamat' => $user->alamat,
                    'email' => $user->email,
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:20',
            'nama_lengkap' => 'required|string|max:100',
            'nidn' => 'required|string|max:18',
            'foto' => 'nullable|string', // Base64 string
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'jabatan_fungsional' => 'required|string|max:100',
            'program_studi' => 'required|string|max:100',
            'pendidikan_terakhir' => 'required|in:S1,S2,S3,Profesor',
            'asal_perguruan_tinggi' => 'required|string|max:100',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string|max:50',
            'status_nikah' => 'required|in:Menikah,Belum Menikah,Cerai',
            'status_ikatan_kerja' => 'required|string|max:100',
            'alamat' => 'required|string',
            'email' => 'required|email|max:100',
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6'
        ], [
            'username.required' => 'Username wajib diisi',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nidn.required' => 'NIDN wajib diisi',
            'jenis_kelamin.required' => 'Jenis kelamin wajib diisi',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P',
            'jabatan_fungsional.required' => 'Jabatan fungsional wajib diisi',
            'program_studi.required' => 'Program studi wajib diisi',
            'pendidikan_terakhir.required' => 'Pendidikan terakhir wajib diisi',
            'pendidikan_terakhir.in' => 'Pendidikan terakhir tidak valid',
            'asal_perguruan_tinggi.required' => 'Asal perguruan tinggi wajib diisi',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
            'tanggal_lahir.date' => 'Format tanggal lahir tidak valid',
            'agama.required' => 'Agama wajib diisi',
            'status_nikah.required' => 'Status nikah wajib diisi',
            'status_nikah.in' => 'Status nikah tidak valid',
            'status_ikatan_kerja.required' => 'Status ikatan kerja wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = UserModel::find($request->user()->user_id);
            $userData = $request->except(['foto', 'current_password', 'new_password']);

            // Validasi password jika ada
            if ($request->filled('current_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Password saat ini tidak sesuai'
                    ], 422);
                }
                
                if (empty($request->new_password)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Password baru tidak boleh kosong'
                    ], 422);
                }

                $userData['password'] = Hash::make($request->new_password);
            }

            // Handle foto
            if ($request->filled('foto')) {
                try {
                    // Proses base64 image
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $request->foto));
                    $img = Image::make($imageData);
                    
                    // Resize if needed
                    if ($img->width() > 800 || $img->height() > 800) {
                        $img->resize(800, 800, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }
                    
                    // Compress and convert back to base64
                    $img->encode('jpg', 80);
                    $userData['foto'] = 'data:image/jpeg;base64,' . base64_encode($img->__toString());
                    
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal memproses foto: ' . $e->getMessage()
                    ], 500);
                }
            }

            // Update data
            $userData['updated_by'] = $user->username;
            $user->update($userData);

            return response()->json([
                'success' => true,
                'message' => 'Profil berhasil diperbarui',
                'user' => $user->fresh()
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat update profil: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dosen dashboard data
     */
    public function dosenDashboard(Request $request)
    {
        try {
            $user = UserModel::find($request->user()->user_id);
            
            // Validasi role dosen
            if ($user->level->level_nama !== 'Dosen') {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses tidak diizinkan'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'profil' => [
                        'nama_lengkap' => $user->nama_lengkap,
                        'nidn' => $user->nidn,
                        'foto' => $user->foto,
                        'program_studi' => $user->program_studi
                    ],
                    'statistik' => [
                        'total_kegiatan' => 0, // Akan diimplementasikan nanti
                        'kegiatan_selesai' => 0,
                        'kegiatan_berjalan' => 0
                    ],
                    'agenda' => [] // Akan diimplementasikan nanti
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data dashboard: ' . $e->getMessage()
            ], 500);
        }
    }
}