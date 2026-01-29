<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\API\Client\ClientAuthController;
use App\Http\Controllers\API\Client\ClientProfileController;
use App\Http\Controllers\API\Client\ConsultationController;
use App\Http\Controllers\API\Client\AppointmentController;
use App\Http\Controllers\API\Client\ClientLawController;
use App\Http\Controllers\API\Client\ClientFixedPriceController;
use App\Http\Controllers\API\Client\NotificationController;

// -----------------
// Protected routes (requires login)
// -----------------

Route::middleware('auth:client')->group(function () {

    // Logout
    Route::post('/logout', [ClientAuthController::class, 'logout']);

    // Profile routes
    Route::get('/profile', [ClientProfileController::class, 'show']);
    Route::put('/profile', [ClientProfileController::class, 'update']);
    Route::patch('/profile', [ClientProfileController::class, 'update']);

    // Resend verification email
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Verification email sent']);
    })->middleware('throttle:6,1')->name('verification.send');

    // Consultations routes
    Route::apiResource('consultations', ConsultationController::class);
    Route::post('consultations/{id}/cancel', [ConsultationController::class, 'cancel']);
    Route::post('consultations/{id}/complete', [ConsultationController::class, 'complete']);
    Route::post('consultations/{consultationId}/messages', [ConsultationController::class, 'sendMessage']);
    Route::get('consultations/{consultationId}/messages', [ConsultationController::class, 'getMessages']);
    Route::post('consultations/{consultationId}/review', [ConsultationController::class, 'createReview']);

    // Appointments routes
    Route::get('lawyers/{lawyerId}/available-slots', [AppointmentController::class, 'getAvailableSlots']);
    Route::get('appointments', [AppointmentController::class, 'myAppointments']);
    Route::get('appointments/calendar/month', [AppointmentController::class, 'calendarMonth']); // Monthly calendar
    Route::post('appointments/direct', [AppointmentController::class, 'bookDirectAppointment']); // Direct appointment booking without consultation (must be before appointments/{id})
    Route::post('consultations/{consultationId}/appointments', [AppointmentController::class, 'bookAppointment']);
    Route::get('appointments/{id}', [AppointmentController::class, 'show'])->where('id', '[0-9]+');
    Route::post('appointments/{id}/cancel', [AppointmentController::class, 'cancel'])->where('id', '[0-9]+');
    Route::post('appointments/{id}/reschedule', [AppointmentController::class, 'reschedule'])->where('id', '[0-9]+'); // Reschedule

    // Laws routes
    Route::get('laws', [ClientLawController::class, 'index']);
    Route::get('laws/categories', [ClientLawController::class, 'categories']);
    Route::get('laws/{id}', [ClientLawController::class, 'show']);

    // Fixed Prices routes
    Route::get('fixed-prices', [ClientFixedPriceController::class, 'index']);

    // Notifications routes
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);
});
