<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// routes/api.php
Route::post('/login', [AuthController::class, 'login']);
Route::get('/levels', [AuthController::class, 'getLevels']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);
    Route::post('/profile/photo', [AuthController::class, 'uploadPhoto']);
});