<?php

use Illuminate\Support\Facades\Route;

// Beranda
Route::get('/', function () {
    return view('beranda.index', ['activemenu' => 'beranda']);
});

// Kegiatan Routes
Route::prefix('kegiatan')->group(function () {
    Route::get('/lihat', function () {
        return view('kegiatan.lihat', ['activemenu' => 'kegiatan']);
    })->name('kegiatan.lihat');
});

// Statistik Routes 
Route::prefix('statistik')->group(function () {
    Route::get('/beban-kerja', function () {
        return view('statistik.beban-kerja', ['activemenu' => 'statistik']);
    })->name('statistik.beban-kerja');

    Route::get('/hasil', function () {
        return view('statistik.hasil', ['activemenu' => 'statistik']); 
    })->name('statistik.hasil');
});

// Surat Tugas Routes
Route::prefix('surat-tugas')->group(function () {
    Route::get('/download', function () {
        return view('surat-tugas.download', ['activemenu' => 'surat-tugas']);
    })->name('surat-tugas.download');
});