<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Lawyer\LawyerAuthController;
use App\Http\Controllers\API\lawyer\LawyerProfileController;

Route::post('/login', [LawyerAuthController::class, 'login']);

Route::middleware('auth:lawyer')->group(function () {
    Route::post('/logout', [LawyerAuthController::class, 'logout']);

     Route::get ('/profile', [LawyerProfileController::class, 'show']);
    Route::put('/profile', [LawyerProfileController::class, 'update']);
});
