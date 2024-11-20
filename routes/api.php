<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobileLoginController;
use App\Http\Controllers\Api\MobileUserController;

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
        });
    });
});