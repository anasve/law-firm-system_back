<?php

namespace App\Http\Controllers\API\Lawyer;

use App\Models\Lawyer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Lawyer\LawyerLoginRequest;

class LawyerAuthController extends Controller
{
   public function login(LawyerLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $lawyer = Lawyer::where('email', $credentials['email'])->first();

        if (!$lawyer || !Hash::check($credentials['password'], $lawyer->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $lawyer->createToken('LawyerToken')->plainTextToken;

        return response()->json([
            'token' => $token,
            'lawyer' => $lawyer,
        ]);
    }

    public function logout()
    {
        $lawyer = Auth::guard('lawyer')->user();

        if ($lawyer) {
            $lawyer->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }

        return response()->json(['message' => 'No active session'], 401);
    }
}
