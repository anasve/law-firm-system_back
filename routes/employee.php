<?php

use App\Http\Controllers\API\Employee\ClientManagementController;
use App\Http\Controllers\API\Employee\EmployeeAuthController;
use App\Http\Controllers\API\Employee\EmployeeProfileController;
use App\Http\Controllers\API\Employee\EmployeeAvailabilityController;
use App\Http\Controllers\API\Employee\EmployeeAvailabilityTemplateController;
use App\Http\Controllers\API\Employee\EmployeeAppointmentController;
use App\Http\Controllers\API\Employee\EmployeeConsultationController;
use App\Http\Controllers\API\Employee\EmployeeFixedPriceController;
use App\Http\Controllers\API\Employee\NotificationController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [EmployeeAuthController::class, 'login']);

Route::middleware('auth:employee')->group(function () {

    Route::post('/logout', [EmployeeAuthController::class, 'logout']);
    Route::get('/profile', [EmployeeProfileController::class, 'show']);
    Route::post('/profile', [EmployeeProfileController::class, 'update']);

    // Client resource routes (index, show, update, destroy)

    // Custom client management routes
    Route::get('clients/pending-verified', [ClientManagementController::class, 'pendingVerified']);
    Route::get('clients/rejected', [ClientManagementController::class, 'rejected']);
    Route::get('clients/archived', [ClientManagementController::class, 'archived']);
    Route::get('clients/approved', [ClientManagementController::class, 'approved']);
    Route::get('clients/suspended', [ClientManagementController::class, 'suspended']);

    Route::apiResource('clients', ClientManagementController::class)->except(['create', 'edit']);
    Route::post('clients', [ClientManagementController::class, 'store']);

    Route::post('clients/{id}/activate', [ClientManagementController::class, 'activate']);
    Route::post('clients/{id}/reject', [ClientManagementController::class, 'reject']);
    Route::post('clients/{id}/suspend', [ClientManagementController::class, 'suspend']);

    Route::put('clients/{id}/restore', [ClientManagementController::class, 'restore']);
    Route::delete('clients/{id}/force', [ClientManagementController::class, 'forceDelete']);

    // Availability management routes
    Route::apiResource('availability', EmployeeAvailabilityController::class);
    Route::post('availability/batch', [EmployeeAvailabilityController::class, 'storeBatch']);
    Route::post('availability/create-schedule', [EmployeeAvailabilityController::class, 'createSchedule']); // Create simple work schedule

    // Availability templates routes
    Route::apiResource('availability-templates', EmployeeAvailabilityTemplateController::class);
    Route::post('availability-templates/{id}/apply', [EmployeeAvailabilityTemplateController::class, 'apply']);

    // Appointments management routes
    Route::get('appointments', [EmployeeAppointmentController::class, 'index']);
    Route::get('appointments/custom-time-requests', [EmployeeAppointmentController::class, 'customTimeRequests']); // المواعيد بوقت مخصص
    Route::get('appointments/calendar/month', [EmployeeAppointmentController::class, 'calendarMonth']); // Monthly calendar
    Route::get('appointments/calendar/week', [EmployeeAppointmentController::class, 'calendarWeek']); // Weekly calendar
    Route::get('appointments/calendar/day', [EmployeeAppointmentController::class, 'calendarDay']); // Daily calendar
    Route::post('appointments/calendar/create', [EmployeeAppointmentController::class, 'calendarCreate']); // Create appointment from calendar
    Route::get('appointments/{id}', [EmployeeAppointmentController::class, 'show']);
    Route::post('appointments/{id}/accept', [EmployeeAppointmentController::class, 'accept']); // Accept appointment
    Route::post('appointments/{id}/reject', [EmployeeAppointmentController::class, 'reject']); // Reject appointment

    // Consultations management routes
    Route::get('consultations', [EmployeeConsultationController::class, 'index']);
    Route::get('consultations/pending', [EmployeeConsultationController::class, 'pending']);
    Route::get('consultations/statistics', [EmployeeConsultationController::class, 'statistics']);
    Route::get('consultations/{id}', [EmployeeConsultationController::class, 'show']);
    Route::post('consultations/{id}/assign', [EmployeeConsultationController::class, 'assign']);
    Route::post('consultations/{id}/auto-assign', [EmployeeConsultationController::class, 'autoAssign']);

    // Fixed Prices management routes
    Route::get('fixed-prices', [EmployeeFixedPriceController::class, 'index']);
    Route::get('fixed-prices/active', [EmployeeFixedPriceController::class, 'active']);
    Route::get('fixed-prices/archived', [EmployeeFixedPriceController::class, 'archived']);
    Route::get('fixed-prices/{id}', [EmployeeFixedPriceController::class, 'show']);
    Route::post('fixed-prices', [EmployeeFixedPriceController::class, 'store']);
    Route::put('fixed-prices/{id}', [EmployeeFixedPriceController::class, 'update']);
    Route::delete('fixed-prices/{id}', [EmployeeFixedPriceController::class, 'destroy']);
    Route::put('fixed-prices/{id}/restore', [EmployeeFixedPriceController::class, 'restore']);
    Route::delete('fixed-prices/{id}/force', [EmployeeFixedPriceController::class, 'forceDelete']);

    // Notifications routes
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);
});
