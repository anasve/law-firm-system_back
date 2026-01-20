<?php
namespace App\Http\Controllers\API\Admin;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Employee\StoreEmployeeRequest;
use App\Http\Requests\Admin\Employee\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        Log::info('Search param:', ['search' => $request->query('search')]);

        $employees = Employee::query()
            ->when($request->query('search'), function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        if ($employees->isEmpty()) {
            return response()->json([
                'message' => 'No employee found with the exact name.',
            ], 404);
        }

        $employees = $employees->map(function ($employee) {
            $employeeArray = $employee->toArray();
            if ($employee->photo) {
                $employeeArray['photo'] = asset('storage/' . $employee->photo);
                $employeeArray['photo_path'] = $employee->photo;
            }
            return $employeeArray;
        });

        return response()->json($employees);
    }

    // Show one employee
    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        $employeeArray = $employee->toArray();
        
        if ($employee->photo) {
            $employeeArray['photo'] = asset('storage/' . $employee->photo);
            $employeeArray['photo_path'] = $employee->photo;
        }

        return response()->json($employeeArray);
    }

    // Store new employee
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employees/photos', 'public');
        }

        $employee = Employee::create($data);
        $employeeArray = $employee->toArray();
        
        if ($employee->photo) {
            $employeeArray['photo'] = asset('storage/' . $employee->photo);
            $employeeArray['photo_path'] = $employee->photo;
        }

        return response()->json([
            'message'  => 'Employee created successfully',
            'employee' => $employeeArray,
        ], 201);
    }

    // Update existing employee
    public function update(UpdateEmployeeRequest $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $data     = $request->validated();

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

        return response()->json([
            'message'  => 'Employee updated successfully',
            'employee' => $employeeArray,
        ]);
    }

    // Soft delete (archive)
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return response()->json(['message' => 'Employee archived']);
    }

    // List archived employees
    public function archived()
    {
        $employees = Employee::onlyTrashed()->get();
        
        $employees = $employees->map(function ($employee) {
            $employeeArray = $employee->toArray();
            if ($employee->photo) {
                $employeeArray['photo'] = asset('storage/' . $employee->photo);
                $employeeArray['photo_path'] = $employee->photo;
            }
            return $employeeArray;
        });

        return response()->json($employees);
    }

    // Restore archived employee
    public function restore($id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        $employee->restore();

        return response()->json(['message' => 'Employee restored successfully']);
    }

    // Force delete employee permanently
    public function forceDelete($id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        $employee->forceDelete();

        return response()->json(['message' => 'Employee permanently deleted']);
    }

    public function total()
    {
        return response()->json(['total_employees' => Employee::count()]);
    }
}
