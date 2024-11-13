<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Guest Routes (untuk user yang belum login)
Route::middleware(['guest'])->group(function () {
    // Redirect ke login jika mengakses root URL
    Route::get('/', function () {
        return redirect('/login');
    });

    // Route Login
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'showLoginForm')->name('login');
        Route::post('/login', 'login')->name('login.post');
    });
});

// Protected Routes (harus login untuk mengakses)
Route::middleware(['auth.check'])->group(function () {
    // Logout Route
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

    // Profile Route
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [UserController::class, 'update'])->name('profile.update');

    // Dosen Routes
    Route::middleware(['auth.role:Dosen'])->prefix('dosen')->group(function () {
        Route::get('/', function () {
            return redirect()->route('dosen.dashboard');
        });
        
        Route::get('/dashboard', function () {
            return view('dosen.dashboard', [
                'breadcrumb' => (object)[
                    'title' => 'Dashboard Dosen',
                    'list' => ['Home', 'Dashboard']
                ]
            ]);
        })->name('dosen.dashboard');

        // Tambahkan route Dosen lainnya di sini
        Route::get('/agenda', function () {
            return view('dosen.agenda.index', [
                'breadcrumb' => (object)[
                    'title' => 'Agenda',
                    'list' => ['Home', 'Agenda']
                ]
            ]);
        })->name('dosen.agenda');
    });

    // Kaprodi Routes
    Route::middleware(['auth.role:Kaprodi'])->prefix('kaprodi')->group(function () {
        Route::get('/', function () {
            return redirect()->route('kaprodi.dashboard');
        });
        
        Route::get('/dashboard', function () {
            return view('kaprodi.dashboard', [
                'breadcrumb' => (object)[
                    'title' => 'Dashboard Kaprodi',
                    'list' => ['Home', 'Dashboard']
                ]
            ]);
        })->name('kaprodi.dashboard');

        // Tambahkan route Kaprodi lainnya di sini
        Route::get('/kegiatan', function () {
            return view('kaprodi.kegiatan.index', [
                'breadcrumb' => (object)[
                    'title' => 'Daftar Kegiatan',
                    'list' => ['Home', 'Kegiatan']
                ]
            ]);
        })->name('kaprodi.kegiatan');
    });

    // Admin Routes
    Route::middleware(['auth.role:Admin'])->prefix('admin')->group(function () {
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        });
        
        Route::get('/dashboard', function () {
            return view('admin.dashboard', [
                'breadcrumb' => (object)[
                    'title' => 'Dashboard Admin',
                    'list' => ['Home', 'Dashboard']
                ]
            ]);
        })->name('admin.dashboard');

        // Tambahkan route Admin lainnya di sini
        Route::get('/users', function () {
            return view('admin.users.index', [
                'breadcrumb' => (object)[
                    'title' => 'Manajemen User',
                    'list' => ['Home', 'Users']
                ]
            ]);
        })->name('admin.users');
    });

    // Default Route setelah login (redirect berdasarkan role)
    Route::get('/home', function () {
        switch (session('level_nama')) {
            case 'Admin':
                return redirect()->route('admin.dashboard');
            case 'Kaprodi':
                return redirect()->route('kaprodi.dashboard');
            default:
                return redirect()->route('dosen.dashboard');
        }
    })->name('home');
});

// Fallback route untuk URL yang tidak ditemukan
Route::fallback(function () {
    if (session()->has('user_id')) {
        return redirect()->route('home');
    }
    return redirect()->route('login');
});