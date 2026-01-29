<?php

namespace App\Http\Controllers\API\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guest\StoreJobApplicationRequest;
use App\Models\JobApplication;
use App\Models\Admin;
use App\Notifications\Admin\NewJobApplicationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    /**
     * Submit a new job application
     */
    public function store(StoreJobApplicationRequest $request)
    {
        try {
            $data = $request->validated();

            // Handle photo upload
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('job-applications/photos', 'public');
                $data['photo'] = $photoPath;
            }

            // Handle certificate upload (for lawyers only)
            if ($request->hasFile('certificate') && $data['type'] === 'lawyer') {
                $certificatePath = $request->file('certificate')->store('job-applications/certificates', 'public');
                $data['certificate'] = $certificatePath;
            }

            // Create job application
            $jobApplication = JobApplication::create($data);

            // Load relationships for response
            $jobApplication->load('specialization');

            // Send notification to all admins
            try {
                $admins = Admin::all();
                foreach ($admins as $admin) {
                    $admin->notify(new NewJobApplicationNotification($jobApplication));
                }
            } catch (\Exception $e) {
                // Log error but don't fail the request
                \Log::error('Failed to send job application notification: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'Job application submitted successfully.',
                'data' => [
                    'id' => $jobApplication->id,
                    'type' => $jobApplication->type,
                    'name' => $jobApplication->name,
                    'email' => $jobApplication->email,
                    'status' => $jobApplication->status,
                    'created_at' => $jobApplication->created_at,
                ],
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Job Application Store Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
            
            return response()->json([
                'message' => 'An error occurred while submitting the application.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }
}

