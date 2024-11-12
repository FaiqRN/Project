<?php

namespace App\Http\Controllers;

use App\Models\SuratModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SuratTugasController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Surat Tugas',
            'list' => ['Beranda', 'Surat Tugas']
        ];
        $surats = SuratModel::all();
        return view('kaprodi.surat-tugas', compact('breadcrumb','surats'));
    }
    
}
