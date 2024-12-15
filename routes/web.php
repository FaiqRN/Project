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
use App\Http\Controllers\AdminAgendaController;
use App\Http\Controllers\UpdateProgressAgendaController;
use App\Http\Controllers\AdminPilihAnggotaController;
use App\Http\Controllers\AdminUpdateProgressAgendaController;
use App\Http\Controllers\ProgressKegiatanController;
use App\Http\Controllers\UnggahDokumenAkhirController;
use App\Http\Controllers\LihatKegiatanController;
use App\Http\Controllers\PembagianPoinController;
use App\Http\Controllers\AdminPembagianPoinController;
use App\Http\Controllers\AdminUnggahDokumenAkhirController;

// Guest Routes (untuk user yang belum login)
Route::middleware(['guest'])->group(function () {
    // Redirect ke login jika mengakses root URL
    Route::get('/', function () {
        return view('layouts.welcome');
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

        // Route untuk Update Progress Agenda
        Route::prefix('update-progress')->group(function () {
        Route::get('/', [UpdateProgressAgendaController::class, 'index'])->name('dosen.update-progress');
        Route::get('/dosen/update-progress/{id}/check-status', [UpdateProgressAgendaController::class, 'checkAgendaStatus']);
        Route::get('/{id}/detail', [UpdateProgressAgendaController::class, 'getDetailAgenda']);
        Route::post('/{id}/update', [UpdateProgressAgendaController::class, 'updateProgress']); 
    });

        Route::prefix('statuskegiatan')->group(function () {
            Route::get('/statuskegiatan', [ProgressKegiatanController::class, 'index'])->name('dosen.statuskegiatan');
            Route::get('/statuskegiatan/get-progress', [ProgressKegiatanController::class, 'getKegiatanProgress'])->name('dosen.statuskegiatan.get-progress');
        });

        // Route khusus PIC
        Route::middleware(['auth.role:PIC'])->group(function () {
            // Kegiatan Routes (tetap dipertahankan)
            Route::get('/kegiatan', [KegiatanController::class, 'showKegiatanPIC'])->name('pic.kegiatan');
            Route::get('/surat-tugas/download-file/{id}', [SuratTugasController::class, 'downloadSurat'])->name('surat-tugas.download-file');
            Route::post('/kegiatan/validate-tanggal-agenda', [KegiatanController::class, 'validateTanggalAgenda'])->name('kegiatan.validate.tanggal');
            
            // Agenda Routes (tambahkan ini)
            Route::prefix('agenda')->group(function () {
                Route::get('/', [AgendaController::class, 'index'])->name('pic.agenda.index');
                Route::post('/store', [AgendaController::class, 'store'])->name('pic.agenda.store');
                Route::put('/update/{id}', [AgendaController::class, 'update'])->name('pic.agenda.update');
                Route::delete('/delete/{id}', [AgendaController::class, 'destroy'])->name('pic.agenda.delete');
                Route::get('/download/{id}', [AgendaController::class, 'downloadDocument'])->name('pic.agenda.download');
                Route::get('/get-data', [AdminAgendaController::class, 'getAgendaList'])->name('get-data');
            });

            // Route untuk Pilih Anggota
            Route::prefix('pilih')->group(function () {
                Route::get('/', [PilihAnggotaController::class, 'index'])->name('pic.pilih');
                Route::get('/data', [PilihAnggotaController::class, 'getData'])->name('pic.pilih.data');
                Route::post('/store', [PilihAnggotaController::class, 'store'])->name('pic.pilih.store');
                Route::get('/edit/{id}', [PilihAnggotaController::class, 'edit'])->name('pic.pilih.edit');
                Route::put('/update/{id}', [PilihAnggotaController::class, 'update'])->name('pic.pilih.update');
                Route::delete('/delete/{id}', [PilihAnggotaController::class, 'destroy'])->name('pic.pilih.delete');
            });
             
            // route untuk dokumen
            Route::prefix('dokumen')->group(function () {
                Route::get('/', [UnggahDokumenAkhirController::class, 'index'])->name('pic.unggah-dokumen');
                Route::get('/list', [UnggahDokumenAkhirController::class, 'getKegiatanList'])->name('pic.unggah-dokumen.list');
                Route::post('/store', [UnggahDokumenAkhirController::class, 'store'])->name('pic.unggah-dokumen.store');
                Route::post('/update', [UnggahDokumenAkhirController::class, 'update'])->name('pic.unggah-dokumen.update');
                Route::get('/download/{id}/{type}', [UnggahDokumenAkhirController::class, 'download'])->name('pic.unggah-dokumen.download');
            });

            // route pembagian poin PIC 
            Route::prefix('pembagian-poin')->group(function () {
                Route::get('/', [PembagianPoinController::class, 'index'])->name('pic.pembagian-poin');
                Route::get('/data', [PembagianPoinController::class, 'getDataPoin'])->name('pic.pembagian-poin.data');
                Route::get('/ringkasan', [PembagianPoinController::class, 'getRingkasanPoin'])->name('pic.pembagian-poin.ringkasan');
                Route::post('/tambah', [PembagianPoinController::class, 'tambahPoin'])->name('pic.pembagian-poin.tambah');
            });
        });
    });
});

    // Kaprodi Routes
    Route::middleware(['auth.role:Kaprodi'])->prefix('kaprodi')->group(function () {

       
        Route::get('/dashboard', function () {
            return view('kaprodi.dashboard', [
                'breadcrumb' => (object)[
                    'title' => 'Dashboard Kaprodi',
                    'list' => ['Home', 'Dashboard']
                ]
            ]);
        })->name('kaprodi.dashboard');


        Route::get('/kegiatan', function () {
            return view('kaprodi.kegiatan', [
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

    // Route untuk Melihat Kegiatan
    Route::prefix('kaprodi/kegiatan')->group(function () {
        Route::get('/', [LihatKegiatanController::class, 'index'])->name('kaprodi.kegiatan');
        Route::get('/data', [LihatKegiatanController::class, 'getKegiatanData'])->name('kaprodi.kegiatan.data');
        Route::get('/download-dokumen/{jenis}/{id}', [LihatKegiatanController::class, 'downloadDokumenFinal'])
            ->name('kaprodi.kegiatan.download-dokumen');
    
        });
    });

    // Admin Routes
    Route::middleware(['auth.role:Admin'])->prefix('admin')->name('admin.')->group(function () {



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
            
            // Update Progress Routes
            Route::get('/update-progress', [AdminUpdateProgressAgendaController::class, 'index'])
                 ->name('update-progress');
            Route::get('/update-progress/{id}/detail', [AdminUpdateProgressAgendaController::class, 'getDetailAgenda'])
                 ->name('update-progress.detail');
            Route::delete('/update-progress/{id}/delete', [AdminUpdateProgressAgendaController::class, 'deleteProgress'])
                 ->name('update-progress.delete');
            Route::get('/update-progress/{id}/download', [AdminUpdateProgressAgendaController::class, 'downloadDokumentasi'])
                 ->name('update-progress.download');
            
            //route jabatan
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

                Route::get('/agenda-setting', [AdminAgendaController::class, 'index'])->name('agenda-setting');
                Route::get('/get-data', [AdminAgendaController::class, 'getAgendaList'])->name('get-data');
                Route::get('/get-kegiatan', [AdminAgendaController::class, 'getKegiatan'])->name('get-kegiatan');
                Route::get('/download/{id}', [AdminAgendaController::class, 'download'])->name('download');
                Route::post('/store', [AdminAgendaController::class, 'store'])->name('store');
                Route::put('/update/{id}', [AdminAgendaController::class, 'update'])->name('update');
                Route::delete('/delete/{id}', [AdminAgendaController::class, 'destroy'])->name('delete');

                // Route untuk Pilih Anggota Admin (perbaikan route)
                Route::prefix('pilih-anggota')->name('pilih-anggota.')->group(function () {
                    Route::controller(AdminPilihAnggotaController::class)->group(function () {
                        Route::get('/', 'index')->name('index');
                        Route::get('/data', 'getData')->name('data');
                        Route::get('/filtered-data', 'getFilteredData')->name('filtered-data');
                        Route::post('/store', 'store')->name('store');
                        Route::get('/edit/{id}', 'edit')->name('edit');
                        Route::put('/update/{id}', 'update')->name('update');
                        Route::delete('/delete/{id}', 'destroy')->name('delete');
                    });
                });


                Route::prefix('persetujuan-poin')->name('persetujuan-poin.')->group(function () {
                    Route::get('/', [AdminPembagianPoinController::class, 'index'])->name('index');
                    Route::get('/data', [AdminPembagianPoinController::class, 'getDataPoin'])->name('data');
                    Route::post('/update-status', [AdminPembagianPoinController::class, 'updateStatus'])->name('update-status');
                });


                Route::controller(AdminUnggahDokumenAkhirController::class)->group(function () {
                    Route::get('/unggah-dokumen', 'index')->name('unggah-dokumen');
                    Route::get('/unggah-dokumen/list', 'getKegiatanList')->name('unggah-dokumen.list');
                    Route::post('/unggah-dokumen/store', 'store')->name('unggah-dokumen.store');
                    Route::post('/unggah-dokumen/update', 'update')->name('unggah-dokumen.update');
                    Route::delete('/unggah-dokumen/destroy/{id}/{type}', 'destroy')->name('unggah-dokumen.destroy');
                    Route::get('/unggah-dokumen/download/{id}/{type}', 'download')->name('unggah-dokumen.download');
                });;
            });

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


