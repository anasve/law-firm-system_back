<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Lawyer\LawyerAuthController;
use App\Http\Controllers\API\lawyer\LawyerProfileController;
use App\Http\Controllers\API\Lawyer\LawyerConsultationController;
use App\Http\Controllers\API\Lawyer\LawyerAppointmentController;
use App\Http\Controllers\API\Lawyer\NotificationController;

Route::post('/login', [LawyerAuthController::class, 'login']);

Route::middleware('auth:lawyer')->group(function () {
    Route::post('/logout', [LawyerAuthController::class, 'logout']);

    Route::get('/profile', [LawyerProfileController::class, 'show']);
    Route::put('/profile', [LawyerProfileController::class, 'update']);

    // Consultations routes
    Route::get('consultations', [LawyerConsultationController::class, 'index']);
    Route::get('consultations/pending', [LawyerConsultationController::class, 'pending']);
    Route::get('consultations/{id}', [LawyerConsultationController::class, 'show']);
    Route::post('consultations/{id}/accept', [LawyerConsultationController::class, 'accept']);
    Route::post('consultations/{id}/reject', [LawyerConsultationController::class, 'reject']);
    Route::post('consultations/{id}/complete', [LawyerConsultationController::class, 'complete']);
    Route::post('consultations/{consultationId}/messages', [LawyerConsultationController::class, 'sendMessage']);
    Route::get('consultations/{consultationId}/messages', [LawyerConsultationController::class, 'getMessages']);
    Route::put('consultations/{consultationId}/messages/{messageId}/read', [LawyerConsultationController::class, 'markMessageAsRead']);

    // Appointments routes (قراءة فقط)
    Route::get('appointments', [LawyerAppointmentController::class, 'index']);
    Route::get('appointments/upcoming', [LawyerAppointmentController::class, 'upcoming']);
    Route::get('appointments/calendar/month', [LawyerAppointmentController::class, 'calendarMonth']); // تقويم شهري
    Route::get('appointments/{id}', [LawyerAppointmentController::class, 'show']);

    // Notifications routes
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread', [NotificationController::class, 'unread']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);
});
