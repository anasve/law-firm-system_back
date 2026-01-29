<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/app', function () {
    return response()->file(public_path('react/index.html'));
});

// Fallback: serve storage files when symlink is not available (e.g. some Windows/shared hosting)
Route::get('/storage/{path}', function ($path) {
    $path = str_replace(['../', '..\\'], '', $path); // prevent path traversal
    $fullPath = storage_path('app/public/' . $path);
    $publicPath = realpath(storage_path('app/public'));
    if (!$publicPath || !\Illuminate\Support\Facades\File::exists($fullPath) || !\Illuminate\Support\Facades\File::isFile($fullPath)) {
        abort(404);
    }
    if (strpos(realpath($fullPath), $publicPath) !== 0) {
        abort(404);
    }
    $mime = \Illuminate\Support\Facades\File::mimeType($fullPath);
    return response()->file($fullPath, ['Content-Type' => $mime]);
})->where('path', '.*');


