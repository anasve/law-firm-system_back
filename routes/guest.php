<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Guest\LawController;

// Guest-accessible public law routes
Route::get('/laws', [LawController::class, 'index']);
Route::get('/laws/{id}', [LawController::class, 'show']);
