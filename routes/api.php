<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileLoginController;
use App\Http\Controllers\Api\MobileUserController;
use App\Http\Controllers\Api\MobileDokumentasiController;
use App\Http\Controllers\Api\MobileProgressKegiatanController;
use App\Http\Controllers\Api\MobileSuratTugasController;
use App\Http\Controllers\Api\MobileSAWController;

Route::prefix('v1')->group(function () {
    // Auth Routes
    Route::post('/login', [MobileLoginController::class, 'login']);
    Route::get('/levels', [MobileLoginController::class, 'getLevels']);
   
    // Protected Routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [MobileLoginController::class, 'logout']);
       
        // Profile Routes (untuk semua user)
        Route::get('/profile', [MobileUserController::class, 'profile']);
        Route::post('/profile/update', [MobileUserController::class, 'updateProfile']);
       
        // Dosen Routes
        Route::prefix('dosen')->middleware(['role:Dosen'])->group(function () {
            Route::get('/dashboard', [MobileUserController::class, 'dosenDashboard']);
            Route::prefix('dokumentasi')->group(function () {
                Route::get('/agenda', [MobileDokumentasiController::class, 'getAgendaList']);
                Route::get('/agenda/{id}', [MobileDokumentasiController::class, 'getDokumentasi']);
                Route::post('/store', [MobileDokumentasiController::class, 'store']);
                Route::post('/update/{id}', [MobileDokumentasiController::class, 'update']); 
                Route::delete('/{id}', [MobileDokumentasiController::class, 'destroy']);
            });
            // Di dalam group middleware auth:sanctum dan role:Dosen
            Route::prefix('progress-kegiatan')->group(function () {
                Route::get('/', [MobileProgressKegiatanController::class, 'getProgress']);
                Route::get('/{id}/{type}', [MobileProgressKegiatanController::class, 'getDetailProgress']);
            });
        });
        Route::prefix('Kaprodi')->middleware(['role:Kaprodi'])->group(function () {
            Route::prefix('mobile/surat-tugas')->group(function () {
                Route::get('/', [MobileSuratTugasController::class, 'index']);
                Route::post('/', [MobileSuratTugasController::class, 'store']);
                Route::get('/{id}', [MobileSuratTugasController::class, 'show']);
                Route::post('/{id}', [MobileSuratTugasController::class, 'update']);
                Route::delete('/{id}', [MobileSuratTugasController::class, 'destroy']);
                Route::get('/{id}/download', [MobileSuratTugasController::class, 'download']);
            });
            Route::prefix('saw')->group(function () {
                Route::get('/latest', [MobileSAWController::class, 'getLatestEvaluation']);
                Route::get('/history', [MobileSAWController::class, 'getEvaluationHistory']);
                Route::get('/detail/{evaluasiId}', [MobileSAWController::class, 'getEvaluationDetail']);
            });
        });
    });
});



