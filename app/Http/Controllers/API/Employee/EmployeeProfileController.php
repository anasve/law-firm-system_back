<?php

namespace App\Http\Controllers\API\Employee;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Employee\EmployeeProfileRequest;

class EmployeeProfileController extends Controller
{
      public function show()
    {
        return response()->json(auth('employee')->user());
    }

    public function update(EmployeeProfileRequest $request)
    {
        $employee = auth('employee')->user();
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $employee->update($data);

        return response()->json($employee);
    }
}
