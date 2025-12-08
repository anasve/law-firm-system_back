<?php

namespace App\Http\Controllers\API\Guest;

use App\Models\Specialization;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SpecializationController extends Controller
{
    // List all active specializations (not deleted)
    public function index(Request $request)
    {
        $query = Specialization::withCount('lawyers');

        // Search by name or description
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $specializations = $query->paginate(10);

        if ($specializations->isEmpty()) {
            return response()->json([
                'message' => 'No specializations found matching your criteria.',
            ], 404);
        }

        $specializations->getCollection()->transform(function ($specialization) {
            return [
                'id'            => $specialization->id,
                'name'          => $specialization->name,
                'description'   => $specialization->description,
                'lawyers_count' => $specialization->lawyers_count,
                'created_at'    => $specialization->created_at,
            ];
        });

        return response()->json($specializations);
    }

    // View a specific specialization with its lawyers
    public function show($id)
    {
        $specialization = Specialization::with('lawyers')->findOrFail($id);

        return response()->json([
            'id'          => $specialization->id,
            'name'        => $specialization->name,
            'description' => $specialization->description,
            'lawyers'     => $specialization->lawyers->map(function ($lawyer) {
                return [
                    'id'   => $lawyer->id,
                    'name' => $lawyer->name,
                    'age'  => $lawyer->age,
                    'photo' => $lawyer->photo ? asset('storage/' . $lawyer->photo) : null,
                ];
            }),
            'lawyers_count' => $specialization->lawyers->count(),
            'created_at'    => $specialization->created_at,
        ]);
    }
}

