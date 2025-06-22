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
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        return response()->json($query->latest()->get());
    }

    // View a specific published law
    public function show($id)
    {
        $law = Law::where('id', $id)->where('status', 'published')->firstOrFail();
        return response()->json($law);
    }
}
