<?php

namespace App\Http\Controllers;

use App\Models\PoinModel;
use Illuminate\Http\Request;

class PoinController extends Controller
{
    public function bebanKerja()
    {
        $breadcrumb = (object) [
            'title' => 'Beban Kerja',
            'list' => ['Beranda', 'Beban Kerja']
        ];
        $poinData = PoinModel::all();
        return view('kaprodi.beban-kerja', compact('breadcrumb', 'poinData'));
    }

    public function hasil()
    {
        $breadcrumb = (object) [
            'title' => 'Statistik',
            'list' => ['Beranda', 'Statistik']
        ];
        $poinData = PoinModel::all();
        return view('kaprodi.hasil', compact('breadcrumb', 'poinData'));
    }
}
