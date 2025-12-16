<?php

namespace App\Http\Controllers\API\Lawyer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Lawyer\LawyerProfileRequest;

class LawyerProfileController extends Controller
{
   public function show()
    {
        $lawyer = auth()->guard('lawyer')->user();

        return response()->json([
            'lawyer' => [
                'id'              => $lawyer->id,
                'name'            => $lawyer->name,
                'email'           => $lawyer->email,
                'age'             => $lawyer->age,
                'phone'           => $lawyer->phone,
                'address'         => $lawyer->address,
                'specializations' => $lawyer->specializations()->pluck('name'), // return all specialization names
                'photo'           => $lawyer->photo,
                'certificate'     => $lawyer->certificate,
                'created_at'      => $lawyer->created_at,
                'updated_at'      => $lawyer->updated_at,
            ],
        ]);
    }

    /**
     * Update the authenticated lawyer’s profile.
     */
    public function update(LawyerProfileRequest $request)
    {
        $lawyer = auth()->guard('lawyer')->user();
        $data = $request->validated();

        // Handle file uploads
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($lawyer->photo) {
                Storage::disk('public')->delete($lawyer->photo);
            }
            $data['photo'] = $request->file('photo')->store('lawyers/photos', 'public');
        }

        if ($request->hasFile('certificate')) {
            // Delete old certificate if exists
            if ($lawyer->certificate) {
                Storage::disk('public')->delete($lawyer->certificate);
            }
            $data['certificate'] = $request->file('certificate')->store('lawyers/certificates', 'public');
        }

        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Remove specializations data from $data so it doesn't try to update a non-existent column
        $specializations = $data['specializations'] ?? null;
        unset($data['specializations']);

        // Update lawyer data
        $lawyer->update($data);

        // Sync specializations if provided
        if ($specializations) {
            // Assuming $specializations is an array of specialization IDs
            $lawyer->specializations()->sync($specializations);
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'lawyer'  => $lawyer->fresh()->load('specializations'), // eager load specializations
        ]);
    }
}
