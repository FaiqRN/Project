<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SuratTugasController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\PilihAnggotaController;

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
    Route::get('/profile/edit', [UserController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [UserController::class, 'update'])->name('profile.update');

    // Dosen Routes
    Route::middleware(['auth.role:Dosen,PIC'])->group(function () {
        Route::prefix('dosen')->group(function () {
            Route::get('/', function () {
                return redirect()->route('dosen.dashboard');
            });
            
            Route::get('/dashboard', function () {
                return view('dosen.dashboard', [
                    'breadcrumb' => (object)[
                        'title' => 'Dashboard ' . session('level_nama'),
                        'list' => ['Home', 'Dashboard']
                    ]
                ]);
            })->name('dosen.dashboard');

            Route::get('/agenda', function () {
                return view('dosen.agenda.index', [
                    'breadcrumb' => (object)[
                        'title' => 'Agenda',
                        'list' => ['Home', 'Agenda']
                    ]
                ]);
            })->name('dosen.agenda');

            // Route khusus PIC
            Route::middleware(['auth.role:PIC'])->group(function () {
                // Kegiatan Routes
                Route::get('/kegiatan', [KegiatanController::class, 'showKegiatanPIC'])->name('pic.kegiatan');
                Route::get('/surat-tugas/download-file/{id}', [SuratTugasController::class, 'downloadSurat'])->name('surat-tugas.download-file');
                Route::post('/kegiatan/validate-tanggal-agenda', [KegiatanController::class, 'validateTanggalAgenda'])->name('kegiatan.validate.tanggal');

                // Agenda Routes
                Route::prefix('dosen/kegiatan')->group(function () {
                    Route::get('/agenda', [AgendaController::class, 'index'])->name('dosen.agenda.index');
                    Route::get('/agenda/{type}/{id}', [AgendaController::class, 'getAgendaList'])->name('dosen.agenda.list');
                    Route::post('/agenda/store', [AgendaController::class, 'store'])->name('agenda.store');
                    Route::put('/agenda/update/{id}', [AgendaController::class, 'update'])->name('dosen.agenda.update');
                    Route::delete('/agenda/delete/{id}', [AgendaController::class, 'destroy'])->name('dosen.agenda.delete');
                    Route::get('/kegiatan/create', [AgendaController::class, 'create'])->name('kegiatan.create');

                });

                // Route untuk Pilih Anggota
                Route::prefix('pilih-anggota')->name('pic.pilih-anggota.')->group(function () {
                    Route::get('/', [PilihAnggotaController::class, 'index'])->name('index');
                    Route::get('/data', [PilihAnggotaController::class, 'getAnggota'])->name('data');
                    Route::post('/store', [PilihAnggotaController::class, 'store'])->name('store');
                    Route::get('/edit/{id}', [PilihAnggotaController::class, 'edit'])->name('edit');
                    Route::put('/update/{id}', [PilihAnggotaController::class, 'update'])->name('update');
                    Route::delete('/delete/{id}', [PilihAnggotaController::class, 'destroy'])->name('delete');
                });
            });
        });
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

        Route::get('/kegiatan', function () {
            return view('kaprodi.kegiatan.index', [
                'breadcrumb' => (object)[
                    'title' => 'Daftar Kegiatan',
                    'list' => ['Home', 'Kegiatan']
                ]
            ]);
        })->name('kaprodi.kegiatan');

        // Route untuk surat tugas
        Route::prefix('surat-tugas')->group(function () {
            Route::get('/download', [SuratTugasController::class, 'download'])
                ->name('kaprodi.surat-tugas.download');
            Route::get('/download-file/{id}', [SuratTugasController::class, 'downloadSurat'])
                ->name('kaprodi.surat-tugas.download-file');
            Route::get('/{id}', [SuratTugasController::class, 'showkaprodi'])
                ->name('kaprodi.surat-tugas.showkaprodi');
        });
    });

    // Admin Routes
    Route::middleware(['auth.role:Admin'])->prefix('admin')->name('admin.')->group(function () {
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
        })->name('dashboard');

        // User Management
        Route::controller(UserManagementController::class)->group(function () {
            Route::get('/users', 'index')->name('users.index');
            Route::post('/users', 'store')->name('users.store');
            Route::get('/users/{id}', 'show')->name('users.show');
            Route::put('/users/{id}', 'update')->name('users.update');  
            Route::delete('/users/{id}', 'destroy')->name('users.destroy');
        });

        // Dosen Management Routes
        Route::prefix('dosen')->name('dosen.')->group(function () {
            // Agenda Routes
            Route::prefix('agenda')->name('agenda.')->group(function () {
                Route::controller(JabatanController::class)->group(function () {
                    Route::get('/jabatan/get-user-level/{userId}', 'getUserLevel')->name('jabatan.getUserLevel');
                    Route::get('/jabatan', 'index')->name('jabatan');
                    Route::post('/jabatan', 'store')->name('jabatan.store');
                    Route::get('/jabatan/{id}/edit', 'edit')->name('jabatan.edit');
                    Route::put('/jabatan/{id}', 'update')->name('jabatan.update');
                    Route::delete('/jabatan/{id}', 'destroy')->name('jabatan.destroy');
                    Route::get('/jabatan/get-kegiatan', 'getKegiatan')->name('jabatan.getKegiatan');
                });

                Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan');

                // Kegiatan Jurusan Routes
                Route::prefix('jurusan')->group(function () {
                    Route::get('/get-data', [KegiatanController::class, 'getKegiatanJurusan'])->name('jurusan.data');
                    Route::post('/store', [KegiatanController::class, 'storeKegiatanJurusan'])->name('jurusan.store');
                    Route::get('/{id}', [KegiatanController::class, 'showKegiatanJurusan'])->name('jurusan.show');
                    Route::put('/update/{id}', [KegiatanController::class, 'updateKegiatanJurusan'])->name('jurusan.update');
                    Route::delete('/delete/{id}', [KegiatanController::class, 'destroyKegiatanJurusan'])->name('jurusan.destroy');
                });

                // Kegiatan Prodi Routes
                Route::prefix('prodi')->group(function () {
                    Route::get('/get-data', [KegiatanController::class, 'getKegiatanProdi'])->name('prodi.data');
                    Route::post('/store', [KegiatanController::class, 'storeKegiatanProdi'])->name('prodi.store');
                    Route::get('/{id}', [KegiatanController::class, 'showKegiatanProdi'])->name('prodi.show');
                    Route::put('/update/{id}', [KegiatanController::class, 'updateKegiatanProdi'])->name('prodi.update');
                    Route::delete('/delete/{id}', [KegiatanController::class, 'destroyKegiatanProdi'])->name('prodi.destroy');
                });

                // Route untuk Pilih Anggota Admin (perbaikan route)
                Route::prefix('pilih-anggota')->name('pilih-anggota.')->group(function () {
                    Route::get('/', [PilihAnggotaController::class, 'indexAdmin'])->name('index');
                    Route::get('/data', [PilihAnggotaController::class, 'getAnggotaAdmin'])->name('data');
                    Route::get('/edit/{id}', [PilihAnggotaController::class, 'edit'])->name('edit');
                    Route::put('/update/{id}', [PilihAnggotaController::class, 'update'])->name('update');
                    Route::delete('/delete/{id}', [PilihAnggotaController::class, 'destroy'])->name('delete');
                });

                Route::view('/persetujuan-poin', 'admin.dosen.agenda.persetujuan-poin', [
                    'breadcrumb' => (object)[
                        'title' => 'Persetujuan Poin',
                        'list' => ['Home', 'Dosen', 'Agenda', 'Persetujuan Poin']
                    ]
                ])->name('persetujuan-poin');

                Route::view('/unggah-dokumen', 'admin.dosen.agenda.unggah-dokumen', [
                    'breadcrumb' => (object)[
                        'title' => 'Unggah Dokumen',
                        'list' => ['Home', 'Dosen', 'Agenda', 'Unggah Dokumen']
                    ]
                ])->name('unggah-dokumen');
            });

            Route::view('/progress-kegiatan', 'admin.dosen.progress-kegiatan', [
                'breadcrumb' => (object)[
                    'title' => 'Progress Kegiatan',
                    'list' => ['Home', 'Dosen', 'Progress Kegiatan']
                ]
            ])->name('progress-kegiatan');

            Route::view('/update-progress', 'admin.dosen.update-progress', [
                'breadcrumb' => (object)[
                    'title' => 'Update Progress',
                    'list' => ['Home', 'Dosen', 'Update Progress']
                ]
            ])->name('update-progress');

            Route::view('/kegiatan-non-jti', 'admin.dosen.kegiatan-non-jti', [
                'breadcrumb' => (object)[
                    'title' => 'Kegiatan Non-JTI',
                    'list' => ['Home', 'Dosen', 'Kegiatan Non-JTI']
                ]
            ])->name('kegiatan-non-jti');
        });

        // Kaprodi Management Routes
        Route::prefix('kaprodi')->name('kaprodi.')->group(function () {
            Route::controller(SuratTugasController::class)->group(function () {
                Route::get('/surat-tugas', 'index')->name('surat-tugas');
                Route::post('/surat-tugas', 'store')->name('surat-tugas.store');
                Route::get('/surat-tugas/{id}', 'show')->name('surat-tugas.show');
                Route::put('/surat-tugas/{id}', 'update')->name('surat-tugas.update');
                Route::delete('/surat-tugas/{id}', 'destroy')->name('surat-tugas.destroy');
            });
        });
    });

    // Default Route setelah login (redirect berdasarkan role)
    Route::get('/home', function () {
        $levelNama = strtolower(session('level_nama'));
        switch ($levelNama) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'kaprodi':
                return redirect()->route('kaprodi.dashboard');
            case 'dosen':
            case 'pic':
                return redirect()->route('dosen.dashboard');
            default:
                return redirect()->route('login')
                    ->with('error', 'Level pengguna tidak valid');
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