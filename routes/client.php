<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\API\Client\ClientAuthController;

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
