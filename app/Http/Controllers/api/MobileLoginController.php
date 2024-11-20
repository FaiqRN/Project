<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class MobileLoginController extends Controller
{
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'level_id' => 'required|exists:m_level,level_id',
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'level_id.required' => 'Silahkan pilih user level',
            'level_id.exists' => 'User level tidak valid',
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // mengambil relasi level
            $user = UserModel::with('level')
                        ->where('username', $request->username)
                        ->where('level_id', $request->level_id)
                        ->first();

            if ($user && Hash::check($request->password, $user->password)) {
                // Generate token untuk mobile
                $token = $user->createToken('mobile-auth')->plainTextToken;

                // Update last login 
                $user->update([
                    'last_login' => now(),
                    'updated_by' => $user->username
                ]);

                // Return user data dan token
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil',
                    'user' => [
                        'user_id' => $user->user_id,
                        'username' => $user->username,
                        'level_id' => $user->level_id,
                        'level_nama' => $user->level->level_nama,
                        'nama_lengkap' => $user->nama_lengkap,
                        'gelar_depan' => $user->gelar_depan,
                        'gelar_belakang' => $user->gelar_belakang,
                        'nidn' => $user->nidn,
                        'foto' => $user->foto,
                        'jenis_kelamin' => $user->jenis_kelamin,
                        'jabatan_fungsional' => $user->jabatan_fungsional,
                        'program_studi' => $user->program_studi,
                        'pendidikan_terakhir' => $user->pendidikan_terakhir,
                        'asal_perguruan_tinggi' => $user->asal_perguruan_tinggi,
                        'email' => $user->email,
                    ],
                    'token' => $token
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Username atau password salah'
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat login: ' . $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            // Update last activity sebelum logout
            $user = $request->user();
            $user->update([
                'last_activity' => now(),
                'updated_by' => $user->username
            ]);

            // Hapus token yang digunakan saat ini
            $user->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Berhasil logout'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat logout: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLevels()
    {
        Log::info('Attempting to fetch levels');
        
        try {
            // Test koneksi database
            try {
                DB::connection()->getPdo();
                Log::info('Database connection successful');
            } catch (\Exception $e) {
                Log::error('Database connection failed: ' . $e->getMessage());
                Log::error('Database config: ' . json_encode([
                    'host' => config('database.connections.mysql.host'),
                    'port' => config('database.connections.mysql.port'),
                    'database' => config('database.connections.mysql.database'),
                    'username' => config('database.connections.mysql.username'),
                ]));
                
                return response()->json([
                    'success' => false,
                    'message' => 'Database connection failed: ' . $e->getMessage()
                ], 500);
            }

            // Query levels
            Log::info('Querying levels table');
            $levels = LevelModel::select('level_id', 'level_nama')->get();
            Log::info('Found ' . $levels->count() . ' levels');

            return response()->json([
                'success' => true,
                'data' => $levels
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in getLevels: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}