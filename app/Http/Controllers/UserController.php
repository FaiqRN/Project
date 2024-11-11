<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserModel;

class UserController extends Controller
{
    public function profile()
    {
        // Data breadcrumb
        $breadcrumb = (object) [
            'title' => 'Profil',
            'list' => ['Beranda', 'Profil']
        ];

        // Ambil data user yang sedang login beserta relasi level
        $user = UserModel::with('level')->find(session('user_id'));

        return view('user.profile', compact('breadcrumb', 'user'));
    }
}