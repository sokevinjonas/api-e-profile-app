<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SocialLinkController;

Route::middleware('jwt')->group(function () {
    // Deconnexion
    Route::delete('/logout', [AuthController::class, 'logout']);

    // Profil
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'store']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);

    // Liens sociaux
    Route::post('/profile/social-links', [SocialLinkController::class, 'store']);
    Route::delete('/profile/social-links/{id}', [SocialLinkController::class, 'destroy']);

    // Services
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/services', [ServiceController::class, 'index']);
    Route::delete('/services/{id}', [ServiceController::class, 'destroy']);
});


// Routes publiques
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
