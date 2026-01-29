<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class ServeStorageFileController extends Controller
{
    /**
     * Serve a file from storage/app/public (no auth - for img src to work).
     * Path is relative to storage/app/public, e.g. "lawyers/photos/xxx.jpg"
     */
    public function __invoke(Request $request, string $path)
    {
        $path = str_replace(['../', '..\\'], '', $path);
        $path = trim($path, '/\\');

        if ($path === '' || !Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $fullPath = storage_path('app/public/' . $path);
        if (!File::isFile($fullPath)) {
            abort(404);
        }
        $realPath = realpath($fullPath);
        $publicRoot = realpath(storage_path('app/public'));
        if (!$publicRoot || !$realPath || strpos($realPath, $publicRoot) !== 0) {
            abort(404);
        }

        $mime = File::mimeType($fullPath);
        return response()->file($fullPath, ['Content-Type' => $mime]);
    }
}
