<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserModel;
use App\Models\LevelModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller{
    
    public function __construct(){
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm(){
        $levels = LevelModel::all();
        return view('auth.login', compact('levels'));
    }

    public function login(Request $request){
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        try {
            // mengambil relasi level
            $user = UserModel::with('level')
                        ->where('username', $request->username)
                        ->first();

            if ($user && Hash::check($request->password, $user->password)) {
                // menetapkan data pengguna
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

                // Update last login 
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
                ->withInput($request->only('username'))
                ->withErrors(['login' => 'Username atau password salah']);

        } catch (\Exception $e) {
            return back()
                ->withInput($request->only('username'))
                ->withErrors(['error' => 'Terjadi kesalahan saat login: ' . $e->getMessage()]);
        }
    }

    public function logout(Request $request){
        // Update last activity sebelum logout 
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

    private function checkSessionTimeout(){
        $lastActivity = session('last_activity');
        $timeout = config('session.lifetime') * 1800;

        if ($lastActivity && time() - $lastActivity > $timeout) {
            Session::flush();
            return true;
        }

        session(['last_activity' => time()]);
        return false;
    }
}