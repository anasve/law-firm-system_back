<?php
namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeLoginRequest;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeAuthController extends Controller
{
     public function login(EmployeeLoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        $employee = Employee::where('email', $credentials['email'])->first();

        if (!$employee || !Hash::check($credentials['password'], $employee->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $employee->createToken('EmployeeToken')->plainTextToken;

        $employeeArray = $employee->toArray();
        
        if ($employee->photo) {
            $employeeArray['photo'] = asset('storage/' . $employee->photo);
            $employeeArray['photo_path'] = $employee->photo;
        }

        return response()->json([
            'token' => $token,
            'employee' => $employeeArray,
        ]);
    }

    public function logout()
    {
        $employee = Auth::guard('employee')->user();

        if ($employee) {
            $employee->tokens()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        }

        return response()->json(['message' => 'No active session'], 401);
    }
}
