<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Http\Request;

class SpecializationController extends Controller
{public function index()
    {
        return response()->json(Specialization::all());
    }

    // Create a new specialization
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:specializations,name',
        ]);

        $spec = Specialization::create($data);
        return response()->json($spec, 201);
    }

    // Show one specialization
    public function show($id)
    {
        $spec = Specialization::findOrFail($id);
        return response()->json($spec);
    }

    // Update an existing specialization
    public function update(Request $request, $id)
    {
        $spec = Specialization::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:100|unique:specializations,name,' . $spec->id,
        ]);

        $spec->update($data);
        return response()->json($spec);
    }

    // Soft delete (archive) a specialization
    public function destroy($id)
    {
        $spec = Specialization::findOrFail($id);
        $spec->delete();
        return response()->json(['message' => 'Specialization archived']);
    }

    // Permanently delete a specialization
    public function forceDelete($id)
    {
        $spec = Specialization::withTrashed()->findOrFail($id);
        $spec->forceDelete();
        return response()->json(['message' => 'Specialization permanently deleted']);
    }
}
