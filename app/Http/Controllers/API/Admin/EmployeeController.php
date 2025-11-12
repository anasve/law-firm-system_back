<?php
namespace App\Http\Controllers\API\Admin;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Admin\Employee\StoreEmployeeRequest;
use App\Http\Requests\Admin\Employee\UpdateEmployeeRequest;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        Log::info('Search param:', ['search' => $request->query('search')]);

        $employees = Employee::query()
            ->when($request->query('search'), function ($query, $search) {
                return $query->where('name', $search);
            })
            ->latest()
            ->get();

        if ($employees->isEmpty()) {
            return response()->json([
                'message' => 'No employee found with the exact name.',
            ], 404);
        }

        return response()->json($employees);
    }

    // Show one employee
    public function show($id)
    {
        $employee = Employee::findOrFail($id);

        return response()->json($employee);
    }

    // Store new employee
    public function store(StoreEmployeeRequest $request)
    {
        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $employee = Employee::create($data);

        return response()->json([
            'message'  => 'Employee created successfully',
            'employee' => $employee,
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

        $employee->update($data);

        return response()->json([
            'message'  => 'Employee updated successfully',
            'employee' => $employee,
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
