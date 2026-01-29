<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\FixedPrice;
use Illuminate\Http\Request;

class EmployeeFixedPriceController extends Controller
{
    /**
     * Get all fixed prices
     */
    public function index(Request $request)
    {
        $query = FixedPrice::query();

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = $request->input('per_page', 15);
        $prices = $query->latest()->paginate($perPage);

        return response()->json($prices);
    }

    /**
     * Get active fixed prices only
     */
    public function active()
    {
        $prices = FixedPrice::active()->latest()->get();

        return response()->json([
            'data' => $prices,
        ]);
    }

    /**
     * Get archived fixed prices
     */
    public function archived()
    {
        $prices = FixedPrice::onlyTrashed()->latest()->get();

        return response()->json([
            'data' => $prices,
        ]);
    }

    /**
     * Get a specific fixed price
     */
    public function show($id)
    {
        $price = FixedPrice::withTrashed()->findOrFail($id);

        return response()->json([
            'data' => $price,
        ]);
    }

    /**
     * Create a new fixed price
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'type' => 'required|in:fee,copy,stamp,translation,court_fee,document,other',
            'price' => 'required|numeric|min:0',
            'unit' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $data['is_active'] ?? true;

        $price = FixedPrice::create($data);

        return response()->json([
            'message' => 'Fixed price created successfully.',
            'data' => $price,
        ], 201);
    }

    /**
     * Update a fixed price
     */
    public function update(Request $request, $id)
    {
        $price = FixedPrice::findOrFail($id);

        $data = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'name_ar' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:fee,copy,stamp,translation,court_fee,document,other',
            'price' => 'sometimes|required|numeric|min:0',
            'unit' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $price->update($data);

        return response()->json([
            'message' => 'Fixed price updated successfully.',
            'data' => $price,
        ]);
    }

    /**
     * Archive (soft delete) a fixed price
     */
    public function destroy($id)
    {
        $price = FixedPrice::findOrFail($id);
        $price->delete();

        return response()->json([
            'message' => 'Fixed price archived successfully.',
        ]);
    }

    /**
     * Restore an archived fixed price
     */
    public function restore($id)
    {
        $price = FixedPrice::onlyTrashed()->findOrFail($id);
        $price->restore();

        return response()->json([
            'message' => 'Fixed price restored successfully.',
            'data' => $price,
        ]);
    }

    /**
     * Permanently delete a fixed price
     */
    public function forceDelete($id)
    {
        $price = FixedPrice::onlyTrashed()->findOrFail($id);
        $price->forceDelete();

        return response()->json([
            'message' => 'Fixed price permanently deleted.',
        ]);
    }
}

