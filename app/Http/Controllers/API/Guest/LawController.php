<?php

namespace App\Http\Controllers\API\Guest;

use App\Models\Law;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LawController extends Controller
{
    // List all published laws
    public function index()
    {
        $laws = Law::where('status', 'published')->select('id', 'title', 'category', 'summary')->paginate(10);
        return response()->json($laws);
    }

    // View a specific law's full content
    public function show($id)
    {
        $law = Law::where('status', 'published')->findOrFail($id);
        return response()->json($law);
    }
}
