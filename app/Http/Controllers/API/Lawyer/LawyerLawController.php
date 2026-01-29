<?php

namespace App\Http\Controllers\API\Lawyer;

use App\Models\Law;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LawyerLawController extends Controller
{
    public function index(Request $request)
    {
        $query = Law::where('status', 'published');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $laws = $query->latest()->paginate($request->input('per_page', 15));

        if ($laws->isEmpty()) {
            return response()->json([
                'data' => [],
                'message' => 'لا توجد قوانين منشورة متاحة حالياً.',
            ], 200);
        }

        return response()->json($laws);
    }

    // View a specific published law
    public function show($id)
    {
        $law = Law::where('id', $id)
            ->where('status', 'published')
            ->first();

        if (!$law) {
            return response()->json([
                'message' => 'The requested law does not exist or is not published.',
            ], 404);
        }

        return response()->json($law);
    }

    /**
     * الحصول على جميع التصنيفات المتاحة
     */
    public function categories()
    {
        $categories = Law::where('status', 'published')
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        return response()->json($categories);
    }
}
