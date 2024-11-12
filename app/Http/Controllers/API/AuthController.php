<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\UserModel;
use App\Models\LevelModel;
class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'level_id' => 'required|exists:m_level,level_id',
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = UserModel::where('username', $request->username)
                        ->where('level_id', $request->level_id)
                        ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'user_id' => $user->user_id,
                'username' => $user->username,
                'level_id' => $user->level_id,
                'level_nama' => $user->level->level_nama,
                'nama_lengkap' => $user->nama_lengkap,
                'token' => $token
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Username atau password salah'
        ], 401);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'user' => $request->user()->load('level')
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'nidn' => 'required|string|max:18|unique:m_user,nidn,'.$user->user_id.',user_id',
            'email' => 'required|email|max:100|unique:m_user,email,'.$user->user_id.',user_id',
            // ... tambahkan validasi lainnya
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->fresh()->load('level')
        ]);
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:1024'
        ]);

        $user = $request->user();

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/profile-photos', $filename);

            $user->update(['foto' => $filename]);
        }

        return response()->json([
            'message' => 'Photo uploaded successfully',
            'user' => $user->fresh()
        ]);
    }

    public function getLevels()
    {
        $levels = LevelModel::all(['level_id', 'level_nama']);
        return response()->json($levels);
    }
}