<?php

namespace App\Http\Controllers\API\Guest;

use App\Models\Lawyer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LawyerController extends Controller
{
    // List all active lawyers (not deleted) with their specializations
    public function index(Request $request)
    {
        $query = Lawyer::with('specializations');

        // Search by name
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by specialization
        if ($specializationId = $request->input('specialization_id')) {
            $query->whereHas('specializations', function ($q) use ($specializationId) {
                $q->where('specializations.id', $specializationId);
            });
        }

        $lawyers = $query->paginate(10);

        if ($lawyers->isEmpty()) {
            return response()->json([
                'message' => 'No lawyers found matching your criteria.',
            ], 404);
        }

        $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');
        $lawyers->getCollection()->transform(function ($lawyer) use ($baseUrl) {
            return [
                'id'              => $lawyer->id,
                'name'            => $lawyer->name,
                'age'             => $lawyer->age,
                'photo'           => $lawyer->photo ? $baseUrl . '/api/files/' . $lawyer->photo : null,
                'photo_path'      => $lawyer->photo,
                'specializations' => $lawyer->specializations->map(function ($spec) {
                    return [
                        'id'          => $spec->id,
                        'name'        => $spec->name,
                        'description' => $spec->description,
                    ];
                }),
                'created_at'      => $lawyer->created_at,
            ];
        });

        return response()->json($lawyers);
    }

    // View a specific lawyer's details
    public function show($id)
    {
        $lawyer = Lawyer::with('specializations')->findOrFail($id);

        $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');
        return response()->json([
            'id'              => $lawyer->id,
            'name'            => $lawyer->name,
            'age'             => $lawyer->age,
            'photo'           => $lawyer->photo ? $baseUrl . '/api/files/' . $lawyer->photo : null,
            'photo_path'      => $lawyer->photo,
            'specializations' => $lawyer->specializations->map(function ($spec) {
                return [
                    'id'          => $spec->id,
                    'name'        => $spec->name,
                    'description' => $spec->description,
                ];
            }),
            'created_at'      => $lawyer->created_at,
        ]);
    }
}


