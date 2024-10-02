<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SocialLinkController;

Route::middleware('auth:sanctum')->group(function () {
    // Profil
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'store']);

    // Liens sociaux
    Route::post('/profile/social-links', [SocialLinkController::class, 'store']);
    Route::delete('/profile/social-links/{id}', [SocialLinkController::class, 'destroy']);

    // Services
    Route::post('/profile/services', [ServiceController::class, 'store']);
    Route::delete('/profile/services/{id}', [ServiceController::class, 'destroy']);
});


// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
