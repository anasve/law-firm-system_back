<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Admin\AdminAuthController;
use App\Http\Controllers\API\Admin\AdminProfileController;
use App\Http\Controllers\API\ServeStorageFileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Serve storage files (images) via API - no auth so <img src> can load
Route::get('files/{path}', ServeStorageFileController::class)->where('path', '.*');


