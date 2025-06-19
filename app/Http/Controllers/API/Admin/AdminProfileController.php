<?php

namespace App\Http\Controllers\API\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\AdminProfileRequest;

class AdminProfileController extends Controller
{
      public function show()
    {
        return response()->json([
            'admin' => auth()->user()
        ]);
    }

    public function update(AdminProfileRequest $request)
    {
        $admin = auth()->user();

        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }

        $admin->save();

        return response()->json([
            'message' => 'Profile updated successfully',
            'admin' => $admin
        ]);
    }
}
