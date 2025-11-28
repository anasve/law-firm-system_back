<?php

namespace App\Http\Controllers\API\Admin;

use Illuminate\Http\Request;
use App\Models\Specialization;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\Specialization\StoreSpecializationRequest;
use App\Http\Requests\admin\Specialization\UpdateSpecializationRequest;

class SpecializationController extends Controller
{
    public function index()
    {
        return response()->json(Specialization::all());
    }

    // Create a new specialization
    public function store(StoreSpecializationRequest $request)
    {
        $specialization = Specialization::create($request->validated());
        return response()->json($specialization, 201);
    }

    // Show one specialization
    public function show($id)
    {
        $specialization = Specialization::findOrFail($id);
        return response()->json($specialization);
    }

    public function archived()
    {
        $specializations = Specialization::onlyTrashed()->latest()->paginate(10);
        return response()->json($specializations);
    }

    // Update an existing specialization
    public function update(UpdateSpecializationRequest $request, $id)
    {
        $specialization = Specialization::findOrFail($id);
        $specialization->update($request->validated());

        return response()->json($specialization);
    }

    // Soft delete (archive)
    public function destroy($id)
    {
        $specialization = Specialization::findOrFail($id);
        $specialization->delete();

        return response()->json(['message' => 'Specialization archived']);
    }

    // Restore soft deleted specialization
    public function restore($id)
    {
        $specialization = Specialization::onlyTrashed()->findOrFail($id);
        $specialization->restore();

        return response()->json(['message' => 'Specialization restored successfully']);
    }

    // Permanently delete
    public function forceDelete($id)
    {
        $specialization = Specialization::withTrashed()->findOrFail($id);
        $specialization->forceDelete();

        return response()->json(['message' => 'Specialization permanently deleted']);
    }
}
