<?php

use App\Http\Controllers\API\Employee\ClientManagementController;
use App\Http\Controllers\API\Employee\EmployeeAuthController;
use App\Http\Controllers\API\Employee\EmployeeProfileController;
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
});
