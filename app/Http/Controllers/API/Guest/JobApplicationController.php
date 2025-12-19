<?php

namespace App\Http\Controllers\API\Guest;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guest\StoreJobApplicationRequest;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JobApplicationController extends Controller
{
    /**
     * Submit a new job application
     */
    public function store(StoreJobApplicationRequest $request)
    {
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

        return response()->json([
            'message' => 'تم تقديم طلب التوظيف بنجاح',
            'data' => [
                'id' => $jobApplication->id,
                'type' => $jobApplication->type,
                'name' => $jobApplication->name,
                'email' => $jobApplication->email,
                'status' => $jobApplication->status,
                'created_at' => $jobApplication->created_at,
            ],
        ], 201);
    }
}

