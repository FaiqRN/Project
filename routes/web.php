<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagementController;
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
Route::middleware(['auth.role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    
    Route::controller(UserManagementController::class)->group(function () {
        Route::get('/users', 'index')->name('users.index');
        Route::post('/users', 'store')->name('users.store');
        Route::get('/users/{id}', 'show')->name('users.show');
        Route::put('/users/{id}', 'update')->name('users.update');  
        Route::delete('/users/{id}', 'destroy')->name('users.destroy');
    });
    
    Route::get('/dashboard', function () {
        return view('admin.dashboard', [
            'breadcrumb' => (object)[
                'title' => 'Dashboard Admin',
                'list' => ['Home', 'Dashboard']
            ]
        ]);
    })->name('dashboard');

    // Dosen Management Routes
    Route::prefix('dosen')->name('dosen.')->group(function () {


        // Agenda Routes
        Route::prefix('agenda')->name('agenda.')->group(function () {
            Route::get('/kegiatan', function () {
                return view('admin.dosen.agenda.kegiatan', [
                    'breadcrumb' => (object)[
                        'title' => 'Kegiatan Dosen',
                        'list' => ['Home', 'Dosen', 'Agenda', 'Kegiatan']
                    ]
                ]);
            })->name('kegiatan');

            Route::get('/pilih-anggota', function () {
                return view('admin.dosen.agenda.pilih-anggota', [
                    'breadcrumb' => (object)[
                        'title' => 'Pilih Anggota',
                        'list' => ['Home', 'Dosen', 'Agenda', 'Pilih Anggota']
                    ]
                ]);
            })->name('pilih-anggota');

            Route::get('/pembagian-poin', function () {
                return view('admin.dosen.agenda.pembagian-poin', [
                    'breadcrumb' => (object)[
                        'title' => 'Pembagian Poin',
                        'list' => ['Home', 'Dosen', 'Agenda', 'Pembagian Poin']
                    ]
                ]);
            })->name('pembagian-poin');

            Route::get('/unggah-dokumen', function () {
                return view('admin.dosen.agenda.unggah-dokumen', [
                    'breadcrumb' => (object)[
                        'title' => 'Unggah Dokumen',
                        'list' => ['Home', 'Dosen', 'Agenda', 'Unggah Dokumen']
                    ]
                ]);
            })->name('unggah-dokumen');
        });

        // Progress Kegiatan
        Route::get('/progress-kegiatan', function () {
            return view('admin.dosen.progress-kegiatan', [
                'breadcrumb' => (object)[
                    'title' => 'Progress Kegiatan',
                    'list' => ['Home', 'Dosen', 'Progress Kegiatan']
                ]
            ]);
        })->name('progress-kegiatan');

        // Update Progress
        Route::get('/update-progress', function () {
            return view('admin.dosen.update-progress', [
                'breadcrumb' => (object)[
                    'title' => 'Update Progress',
                    'list' => ['Home', 'Dosen', 'Update Progress']
                ]
            ]);
        })->name('update-progress');

        // Kegiatan Non-JTI
        Route::get('/kegiatan-non-jti', function () {
            return view('admin.dosen.kegiatan-non-jti', [
                'breadcrumb' => (object)[
                    'title' => 'Kegiatan Non-JTI',
                    'list' => ['Home', 'Dosen', 'Kegiatan Non-JTI']
                ]
            ]);
        })->name('kegiatan-non-jti');
    });

    // Kaprodi Management Routes
    Route::prefix('kaprodi')->name('kaprodi.')->group(function () {
        // Surat Tugas
        Route::get('/surat-tugas', function () {
            return view('admin.kaprodi.surat-tugas', [
                'breadcrumb' => (object)[
                    'title' => 'Surat Tugas',
                    'list' => ['Home', 'Kaprodi', 'Surat Tugas']
                ]
            ]);
        })->name('surat-tugas');
    });

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