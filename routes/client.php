<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Models\Client;
use App\Http\Controllers\API\Client\ClientAuthController;

// -----------------
// Public routes
// -----------------

// Register (Sign Up)



Route::post('/register', [ClientAuthController::class, 'register']);

// Login (Sign In)
Route::post('/login', [ClientAuthController::class, 'login'])->name('login');

// -----------------
// Protected routes (requires login)
// -----------------

Route::middleware('auth:client')->group(function () {

    // Logout
    Route::post('/logout', [ClientAuthController::class, 'logout']);

    // Get authenticated client info
    Route::get('/profile', function (Request $request) {
        return $request->user();
    });

    // Resend verification email
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification email sent']);
    })->middleware('throttle:6,1')->name('verification.send');
});

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
