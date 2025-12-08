<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Http\Controllers\API\Guest\LawController;
use App\Http\Controllers\API\Guest\LawyerController;
use App\Http\Controllers\API\Guest\SpecializationController;
use App\Http\Controllers\API\Guest\GuestAuthController;

// -----------------
// Authentication routes (Public)
// -----------------

// Register (Sign Up)
Route::post('/register', [GuestAuthController::class, 'register']);

// Login (Sign In)
Route::post('/login', [GuestAuthController::class, 'login'])->name('login');

// -----------------
// Email verification route (signed)
// -----------------

Route::middleware('signed')->get('/verify-email/{id}/{hash}', function (Request $request) {
    $client = Client::find($request->route('id'));
    if (! $client) {
        return response()->json(['message' => 'Invalid link or user not found.'], 404);
    }
    // Check the hash manually:
    if (! hash_equals((string) $request->route('hash'), sha1($client->getEmailForVerification()))) {
        return response()->json(['message' => 'Invalid or tampered link.'], 403);
    }
    if ($client->hasVerifiedEmail()) {
        return response()->json(['message' => 'Email already verified.']);
    }
    $client->markEmailAsVerified();
    return response()->json(['message' => 'Email verified successfully.']);
})->name('verification.verify');

// -----------------
// Public content routes
// -----------------

// Guest-accessible public law routes
Route::get('/laws', [LawController::class, 'index']);
Route::get('/laws/{id}', [LawController::class, 'show']);

// Guest-accessible public lawyer routes
Route::get('/lawyers', [LawyerController::class, 'index']);
Route::get('/lawyers/{id}', [LawyerController::class, 'show']);

// Guest-accessible public specialization routes
Route::get('/specializations', [SpecializationController::class, 'index']);
Route::get('/specializations/{id}', [SpecializationController::class, 'show']);
