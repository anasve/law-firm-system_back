<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\AdminLoginRequest;

class AdminAuthController extends Controller
{
   public function login(AdminLoginRequest $request)
    {
        $admin = Admin::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

         $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'admin' => $admin,
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
       $admin = Auth::guard('admin')->user();

        if ($admin) {
            $admin->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }

        return response()->json(['message' => 'No active session'], 401);
    }
}
