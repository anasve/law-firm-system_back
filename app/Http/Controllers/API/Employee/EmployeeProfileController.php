<?php

namespace App\Http\Controllers\API\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Employee\EmployeeProfileRequest;

class EmployeeProfileController extends Controller
{
      public function show()
    {
        $employee = auth('employee')->user();
        $employeeArray = $employee->toArray();
        
        if ($employee->photo) {
            $employeeArray['photo'] = asset('storage/' . $employee->photo);
            $employeeArray['photo_path'] = $employee->photo;
        }
        
        return response()->json($employeeArray);
    }

    public function update(EmployeeProfileRequest $request)
    {
        $employee = auth('employee')->user();
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($employee->photo) {
                Storage::disk('public')->delete($employee->photo);
            }
            $data['photo'] = $request->file('photo')->store('employees/photos', 'public');
        }

        $employee->update($data);
        $employee->refresh();
        $employeeArray = $employee->toArray();
        
        if ($employee->photo) {
            $employeeArray['photo'] = asset('storage/' . $employee->photo);
            $employeeArray['photo_path'] = $employee->photo;
        }

        return response()->json($employeeArray);
    }
}
