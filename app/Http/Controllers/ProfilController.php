<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use App\Models\LevelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use stdClass;

class ProfilController extends Controller
{
    public function index()
    {
        // Setup breadcrumb
        $breadcrumb = new stdClass();
        $breadcrumb->title = "Profil Singkat Pimpinan";
        $breadcrumb->list = ['Home', 'Profil Pimpinan'];

        // Set active menu
        $activemenu = 'profil';

        try {
            // Ambil data pimpinan (level_id = 2 untuk Kaprodi)
            $data_pimpinan = UserModel::with('level')
                ->where('level_id', 2)
                ->first();

            return view('profil.index', compact('data_pimpinan', 'breadcrumb', 'activemenu'));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Method lainnya tetap sama
}