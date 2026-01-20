<?php

namespace App\Http\Controllers\API\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\AdminProfileRequest;

class AdminProfileController extends Controller
{
      public function show()
    {
        $admin = auth()->user();
        $adminArray = $admin->toArray();
        
        if ($admin->photo) {
            $adminArray['photo'] = asset('storage/' . $admin->photo);
            $adminArray['photo_path'] = $admin->photo;
        }
        
        return response()->json([
            'admin' => $adminArray
        ]);
    }

    public function update(AdminProfileRequest $request)
    {
        $admin = auth()->user();

        $admin->name = $request->name;
        $admin->email = $request->email;

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($admin->photo) {
                Storage::disk('public')->delete($admin->photo);
            }
            $admin->photo = $request->file('photo')->store('admins/photos', 'public');
        }

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();
        $admin->refresh();
        $adminArray = $admin->toArray();
        
        if ($admin->photo) {
            $adminArray['photo'] = asset('storage/' . $admin->photo);
            $adminArray['photo_path'] = $admin->photo;
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'admin' => $adminArray
        ]);
    }
}
