<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Admin\LawController;
use App\Http\Controllers\API\Admin\LawyerController;
use App\Http\Controllers\API\Admin\EmployeeController;
use App\Http\Controllers\API\Admin\AdminAuthController;
use App\Http\Controllers\API\Admin\AdminProfileController;
use App\Http\Controllers\API\Admin\SpecializationController;
use App\Http\Controllers\API\Admin\AdminConsultationController;
use App\Http\Controllers\API\Admin\JobApplicationController;

Route::post('/login', [AdminAuthController::class, 'login']);

Route::middleware('auth:admin')->group(function () {

    Route::post('/logout', [AdminAuthController::class, 'logout']);

    Route::get('/profile', [AdminProfileController::class, 'show']);
    Route::put('/profile', [AdminProfileController::class, 'update']);

    
    Route::get('lawyers/total', [LawyerController::class, 'total']);
    Route::apiResource('lawyers', LawyerController::class); 
    Route::get('/lawyers-archived', [LawyerController::class, 'archived']);
    Route::put('/lawyers/{id}/restore', [LawyerController::class, 'restore']);
    Route::delete('/lawyers/{id}/force', [LawyerController::class, 'forceDelete']);


    Route::get('employees/total', [EmployeeController::class, 'total']);
    Route::apiResource('employees', EmployeeController::class);
    Route::put('employees/{id}/restore', [EmployeeController::class, 'restore']);
    Route::delete('employees/{id}/force', [EmployeeController::class, 'forceDelete']);
    Route::get('employees-archived', [EmployeeController::class, 'archived']);


    // Custom law status routes
    Route::get('laws/published', [LawController::class, 'published']);
    Route::get('laws/draft', [LawController::class, 'draft']);
    Route::get('laws/archived', [LawController::class, 'archived']);

    // Extra law actions
    Route::post('laws/{id}/toggle-status', [LawController::class, 'toggleStatus']);
    Route::put('laws/{id}/restore', [LawController::class, 'restore']);
    Route::delete('laws/{id}/force', [LawController::class, 'forceDelete']);
    Route::apiResource('laws', LawController::class);

    Route::apiResource('specializations', SpecializationController::class);
    Route::get('specializations-archived', [SpecializationController::class, 'archived']); 
    Route::put('specializations/{id}/restore', [SpecializationController::class, 'restore']); 
    Route::delete('specializations/{id}/force', [SpecializationController::class, 'forceDelete']);

    // Consultations routes (قراءة فقط - الإدارة للموظف)
    Route::get('consultations', [AdminConsultationController::class, 'index']);
    Route::get('consultations/statistics', [AdminConsultationController::class, 'statistics']);
    Route::get('consultations/{id}', [AdminConsultationController::class, 'show']);

    // Job Applications routes
    Route::get('job-applications', [JobApplicationController::class, 'index']);
    Route::get('job-applications/pending-count', [JobApplicationController::class, 'pendingCount']);
    Route::get('job-applications/{id}', [JobApplicationController::class, 'show']);
    Route::post('job-applications/{id}/approve', [JobApplicationController::class, 'approve']);
    Route::post('job-applications/{id}/reject', [JobApplicationController::class, 'reject']);
    Route::delete('job-applications/{id}', [JobApplicationController::class, 'destroy']);

});
