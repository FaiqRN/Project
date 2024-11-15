<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show login form
     */
    public function showLoginForm()
    {
        $levels = LevelModel::all();
        return view('auth.login', compact('levels'));
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $request->validate([
            'level_id' => 'required|exists:m_level,level_id',
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'level_id.required' => 'Silahkan pilih user level',
            'level_id.exists' => 'User level tidak valid',
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        try {
            // Get user with level relationship
            $user = UserModel::with('level')
                        ->where('username', $request->username)
                        ->where('level_id', $request->level_id)
                        ->first();

            if ($user && Hash::check($request->password, $user->password)) {
                // Set complete session data for user profile
                session([
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
                    'last_activity' => time()
                ]);

                // Update last login time if you have this column
                $user->update([
                    'last_login' => now(),
                    'updated_by' => $user->username
                ]);

                // Redirect based on user level
                switch ($user->level->level_nama) {
                    case 'Admin':
                        return redirect()->route('admin.dashboard')
                            ->with('success', 'Selamat datang, ' . $user->nama_lengkap);
                    case 'Kaprodi':
                        return redirect()->route('kaprodi.dashboard')
                            ->with('success', 'Selamat datang, ' . $user->nama_lengkap);
                    case 'Dosen':
                        return redirect()->route('dosen.dashboard')
                            ->with('success', 'Selamat datang, ' . $user->nama_lengkap);
                    default:
                        return redirect()->route('login')
                            ->with('error', 'Level pengguna tidak valid');
                }
            }

            return back()
                ->withInput($request->only('username', 'level_id'))
                ->withErrors(['login' => 'Username atau password salah']);

        } catch (\Exception $e) {
            return back()
                ->withInput($request->only('username', 'level_id'))
                ->withErrors(['error' => 'Terjadi kesalahan saat login: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        // Update last activity before logout if needed
        if (session('user_id')) {
            UserModel::where('user_id', session('user_id'))
                    ->update([
                        'last_activity' => now(),
                        'updated_by' => session('username')
                    ]);
        }

        Session::flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        $response = redirect()->route('login')
            ->with('success', 'Berhasil logout');
            
        // Tambahkan header no-cache
        return $response->header('Cache-Control','no-store, no-cache, must-revalidate, post-check=0, pre-check=0')
            ->header('Pragma','no-cache')
            ->header('Expires','Sat, 01 Jan 2000 00:00:00 GMT');
    }

    /**
     * Check session timeout
     */
    private function checkSessionTimeout()
    {
        $lastActivity = session('last_activity');
        $timeout = config('session.lifetime') * 60; // Convert minutes to seconds

        if ($lastActivity && time() - $lastActivity > $timeout) {
            Session::flush();
            return true;
        }

        session(['last_activity' => time()]);
        return false;
    }
}