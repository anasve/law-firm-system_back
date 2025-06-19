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

    Route::apiResource('clients', ClientManagementController::class)->except(['create', 'edit']);

    Route::get('clients/pending-verified', [ClientManagementController::class, 'pendingVerified']);
    Route::post('clients/{id}/activate', [ClientManagementController::class, 'activate']);
    Route::post('clients/{id}/suspend', [ClientManagementController::class, 'suspend']);

});
