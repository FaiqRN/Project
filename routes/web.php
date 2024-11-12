<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SuratTugasController;
use App\Http\Controllers\PoinController;

// Guest Routes (untuk user yang belum login)
Route::middleware(['guest'])->group(function () {
    Route::get('/', function () {
        return redirect('/login');
    });
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Protected Routes (harus login untuk mengakses)
Route::middleware(['auth.check'])->group(function () {
    // Route untuk logout
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Route Default setelah login (redirect ke dashboard sesuai role/level pengguna)
    Route::get('/', function () {
        $levelNama = session('level_nama');
        switch ($levelNama) {
            case 'Admin':
                return redirect()->route('admin.dashboard');
            case 'Kaprodi':
                return redirect()->route('kaprodi.dashboard');
            case 'Dosen':
                return redirect()->route('dosen.dashboard');
            default:
                return redirect()->route('login');
        }
    })->name('home');

    // Route Profile (harus login)
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');

    // Dosen Routes
    Route::group(['prefix' => 'dosen', 'middleware' => 'auth.role:Dosen'], function () {
        Route::get('/', function () {
            return redirect()->route('dosen.dashboard');
        });
        Route::get('/dashboard', function () {
            return view('dosen.dashboard', [
                'breadcrumb' => (object)[
                    'title' => 'Dashboard',
                    'list' => ['Home', 'Dashboard']
                ]
            ]);
        })->name('dosen.dashboard');
    });

    // Kaprodi Routes (harus login)
    Route::group(['prefix' => 'kaprodi', 'middleware' => 'auth.role:Kaprodi'], function () {
        Route::get('/', function () {
            return redirect()->route('kaprodi.dashboard');
        });
        Route::get('/dashboard', function () {
            return view('kaprodi.dashboard', [
                'breadcrumb' => (object)[
                    'title' => 'Dashboard',
                    'list' => ['Home', 'Dashboard']
                ]
            ]);
        })->name('kaprodi.dashboard');
        Route::get('/surat', [SuratTugasController::class, 'index'])->name('surat.index');
        Route::get('/surat/{id}', [SuratTugasController::class, 'show'])->name('surat.show');
        Route::get('/surat/download/{id}', [SuratTugasController::class, 'download'])->name('surat.download');
        Route::get('/statistik/beban-kerja', [PoinController::class, 'bebanKerja'])->name('statistik.beban-kerja');
        Route::get('/statistik/hasil', [PoinController::class, 'hasil'])->name('statistik.hasil');

    });

    // Admin Routes (harus login)
    Route::group(['prefix' => 'admin', 'middleware' => 'auth.role:Admin'], function () {
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });
        Route::get('/dashboard', function () {
            return view('admin.dashboard', [
                'breadcrumb' => (object)[
                    'title' => 'Dashboard',
                    'list' => ['Home', 'Dashboard']
                ]
            ]);
        })->name('admin.dashboard');
    });
});

// Jika mencoba akses URL yang tidak ada
Route::fallback(function () {
    return redirect()->route('login');
});