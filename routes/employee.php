<?php

use App\Http\Controllers\API\Employee\ClientManagementController;
use App\Http\Controllers\API\Employee\EmployeeAuthController;
use App\Http\Controllers\API\Employee\EmployeeProfileController;
use App\Http\Controllers\API\Employee\EmployeeAvailabilityController;
use App\Http\Controllers\API\Employee\EmployeeAvailabilityTemplateController;
use App\Http\Controllers\API\Employee\EmployeeAppointmentController;
use App\Http\Controllers\API\Employee\EmployeeConsultationController;
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

    Route::apiResource('clients', ClientManagementController::class)->except(['create', 'edit', 'store']);

    Route::post('clients/{id}/activate', [ClientManagementController::class, 'activate']);
    Route::post('clients/{id}/reject', [ClientManagementController::class, 'reject']);
    Route::post('clients/{id}/suspend', [ClientManagementController::class, 'suspend']);

    Route::put('clients/{id}/restore', [ClientManagementController::class, 'restore']);
    Route::delete('clients/{id}/force', [ClientManagementController::class, 'forceDelete']);

    // Availability management routes
    Route::apiResource('availability', EmployeeAvailabilityController::class);
    Route::post('availability/batch', [EmployeeAvailabilityController::class, 'storeBatch']);
    Route::post('availability/create-schedule', [EmployeeAvailabilityController::class, 'createSchedule']); // إنشاء جدول عمل بسيط

    // Availability templates routes
    Route::apiResource('availability-templates', EmployeeAvailabilityTemplateController::class);
    Route::post('availability-templates/{id}/apply', [EmployeeAvailabilityTemplateController::class, 'apply']);

    // Appointments management routes
    Route::get('appointments', [EmployeeAppointmentController::class, 'index']);
    Route::get('appointments/calendar/month', [EmployeeAppointmentController::class, 'calendarMonth']); // تقويم شهري
    Route::get('appointments/calendar/week', [EmployeeAppointmentController::class, 'calendarWeek']); // تقويم أسبوعي
    Route::get('appointments/calendar/day', [EmployeeAppointmentController::class, 'calendarDay']); // تقويم يومي
    Route::post('appointments/calendar/create', [EmployeeAppointmentController::class, 'createFromCalendar']); // إضافة موعد من التقويم
    Route::get('appointments/{id}', [EmployeeAppointmentController::class, 'show']);
    Route::put('appointments/{id}', [EmployeeAppointmentController::class, 'update']);
    Route::delete('appointments/{id}', [EmployeeAppointmentController::class, 'destroy']);
    Route::post('appointments/{id}/confirm', [EmployeeAppointmentController::class, 'confirm']);
    Route::post('appointments/{id}/cancel', [EmployeeAppointmentController::class, 'cancel']);

    // Consultations management routes (إدارة الاستشارات)
    Route::get('consultations', [EmployeeConsultationController::class, 'index']);
    Route::get('consultations/pending', [EmployeeConsultationController::class, 'pending']);
    Route::get('consultations/statistics', [EmployeeConsultationController::class, 'statistics']);
    Route::get('consultations/{id}', [EmployeeConsultationController::class, 'show']);
    Route::post('consultations/{id}/assign', [EmployeeConsultationController::class, 'assign']);
    Route::post('consultations/{id}/auto-assign', [EmployeeConsultationController::class, 'autoAssign']);

    // Notifications routes
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread', [NotificationController::class, 'unread']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::put('notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);
});
