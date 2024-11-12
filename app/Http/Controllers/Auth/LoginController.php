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

        $user = UserModel::where('username', $request->username)
                        ->where('level_id', $request->level_id)
                        ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            // Set session data
            session([
                'user_id' => $user->user_id,
                'username' => $user->username,
                'level_id' => $user->level_id,
                'level_nama' => $user->level->level_nama,
                'nama_lengkap' => $user->nama_lengkap,
                'last_activity' => time()
            ]);

            // Redirect based on user level
            switch ($user->level->level_nama) {
                case 'Admin':
                    return redirect()->route('admin.dashboard');
                case 'Kaprodi':
                    return redirect()->route('kaprodi.dashboard');
                case 'Dosen':
                    return redirect()->route('dosen.dashboard');
                default:
                    return redirect()->route('login')
                        ->with('error', 'Invalid user level');
            }
        }

        return back()
            ->withInput($request->only('username', 'level_id'))
            ->withErrors(['login' => 'Username atau password salah']);
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
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
}