<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Lawyer;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class JobApplicationController extends Controller
{
    /**
     * Get all job applications with filtering
     */
    public function index(Request $request)
    {
        $query = JobApplication::with(['specialization', 'reviewer']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $applications = $query->latest()->paginate($perPage);

        return response()->json($applications);
    }

    /**
     * Get a specific job application
     */
    public function show($id)
    {
        $application = JobApplication::with(['specialization', 'reviewer'])
            ->findOrFail($id);

        return response()->json([
            'data' => [
                'id' => $application->id,
                'type' => $application->type,
                'status' => $application->status,
                'name' => $application->name,
                'email' => $application->email,
                'phone' => $application->phone,
                'age' => $application->age,
                'address' => $application->address,
                'photo' => $application->photo_url,
                'experience_years' => $application->experience_years,
                'bio' => $application->bio,
                'certificate' => $application->certificate_url,
                'specialization' => $application->specialization,
                'admin_notes' => $application->admin_notes,
                'reviewed_at' => $application->reviewed_at,
                'reviewer' => $application->reviewer,
                'created_at' => $application->created_at,
                'updated_at' => $application->updated_at,
            ],
        ]);
    }

    /**
     * Approve a job application and create the user account
     */
    public function approve(Request $request, $id)
    {
        $application = JobApplication::findOrFail($id);

        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'لا يمكن الموافقة على طلب تم معالجته مسبقاً',
            ], 400);
        }

        // Check if email already exists in the target table
        if ($application->type === 'lawyer') {
            if (Lawyer::where('email', $application->email)->exists()) {
                return response()->json([
                    'message' => 'البريد الإلكتروني مستخدم بالفعل في جدول المحامين',
                ], 422);
            }
        } else {
            if (Employee::where('email', $application->email)->exists()) {
                return response()->json([
                    'message' => 'البريد الإلكتروني مستخدم بالفعل في جدول الموظفين',
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            if ($application->type === 'lawyer') {
                // Generate a random password
                $password = \Illuminate\Support\Str::random(12);
                
                $user = Lawyer::create([
                    'name' => $application->name,
                    'email' => $application->email,
                    'password' => Hash::make($password),
                    'age' => $application->age,
                    'phone' => $application->phone,
                    'address' => $application->address,
                    'photo' => $application->photo,
                    'certificate' => $application->certificate,
                    'specialization_id' => $application->specialization_id,
                ]);

                // Attach specialization (many-to-many relationship)
                if ($application->specialization_id) {
                    $user->specializations()->attach($application->specialization_id);
                }
            } else {
                // Generate a random password
                $password = \Illuminate\Support\Str::random(12);
                
                $user = Employee::create([
                    'name' => $application->name,
                    'email' => $application->email,
                    'password' => Hash::make($password),
                    'age' => $application->age,
                    'phone' => $application->phone,
                    'address' => $application->address,
                    'photo' => $application->photo,
                ]);
            }

            // Update application status
            $application->status = 'approved';
            $application->reviewed_at = now();
            $application->reviewed_by = auth('admin')->id();
            $application->admin_notes = $request->input('admin_notes');
            $application->save();

            DB::commit();

            return response()->json([
                'message' => 'تم الموافقة على الطلب وإنشاء الحساب بنجاح',
                'data' => [
                    'application_id' => $application->id,
                    'user_id' => $user->id,
                    'user_type' => $application->type,
                    'email' => $user->email,
                    'password' => $password, // Temporary password - should be sent via email in production
                ],
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'حدث خطأ أثناء معالجة الطلب',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reject a job application
     */
    public function reject(Request $request, $id)
    {
        $application = JobApplication::findOrFail($id);

        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'لا يمكن رفض طلب تم معالجته مسبقاً',
            ], 400);
        }

        $application->status = 'rejected';
        $application->reviewed_at = now();
        $application->reviewed_by = auth('admin')->id();
        $application->admin_notes = $request->input('admin_notes');
        $application->save();

        return response()->json([
            'message' => 'تم رفض الطلب بنجاح',
            'data' => [
                'id' => $application->id,
                'status' => $application->status,
                'reviewed_at' => $application->reviewed_at,
            ],
        ], 200);
    }

    /**
     * Get pending applications count
     */
    public function pendingCount()
    {
        $count = JobApplication::where('status', 'pending')->count();

        return response()->json([
            'count' => $count,
        ]);
    }

    /**
     * Delete a job application (soft delete)
     */
    public function destroy($id)
    {
        $application = JobApplication::findOrFail($id);

        // Delete associated files
        if ($application->photo) {
            Storage::disk('public')->delete($application->photo);
        }
        if ($application->certificate) {
            Storage::disk('public')->delete($application->certificate);
        }

        $application->delete();

        return response()->json([
            'message' => 'تم حذف الطلب بنجاح',
        ], 200);
    }
}

