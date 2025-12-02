<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Law;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LawController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // ✅ List ALL laws (optional filter by status or search)
    public function index(Request $request)
    {
        $query = Law::query();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $laws = $query->latest()->paginate(10);

        if ($laws->isEmpty()) {
            return response()->json([
                'message' => 'No laws found matching your criteria.'
            ], 404);
        }

        return response()->json($laws);
    }


    // ✅ List PUBLISHED laws only
    public function published()
    {
        $laws = Law::where('status', 'published')->latest()->paginate(10);
        return response()->json($laws);
    }

    // ✅ List DRAFT laws only
    public function draft()
    {
        $laws = Law::where('status', 'draft')->latest()->paginate(10);
        return response()->json($laws);
    }

    // ✅ List ARCHIVED (soft deleted) laws
    public function archived()
    {
        $laws = Law::onlyTrashed()->latest()->paginate(10);
        return response()->json($laws);
    }

    // ✅ Show law details
    public function show($id)
    {
        $law = Law::withTrashed()->findOrFail($id);
        return response()->json($law);
    }

    // ✅ Create new law
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'        => 'required|string|max:255',
            'category'     => 'required|string|max:255',
            'summary'      => 'required|string|max:500',
            'full_content' => 'required|string',
            'status'       => 'in:draft,published',
        ]);

        $law = Law::create(array_merge($data, [
            'status' => $data['status'] ?? 'draft',
        ]));

        return response()->json(['message' => 'Law created successfully', 'law' => $law]);
    }

    // ✅ Update law
    public function update(Request $request, $id)
    {
        $law = Law::findOrFail($id);

        $data = $request->validate([
            'title'        => 'sometimes|required|string|max:255',
            'category'     => 'sometimes|required|string|max:255',
            'summary'      => 'sometimes|required|string|max:500',
            'full_content' => 'sometimes|required|string',
            'status'       => 'sometimes|in:draft,published',
        ]);

        $law->update($data);

        return response()->json(['message' => 'Law updated successfully', 'law' => $law]);
    }

    // ✅ Toggle status between draft and published
    public function toggleStatus($id)
    {
        $law = Law::findOrFail($id);
        $law->status = $law->status === 'draft' ? 'published' : 'draft';
        $law->save();

        return response()->json([
            'message' => "Law status switched to {$law->status}",
            'law'     => $law
        ]);
    }

    // ✅ Archive (soft delete) law
    public function destroy($id)
    {
        $law = Law::findOrFail($id);
        $law->delete();

        return response()->json(['message' => 'Law archived successfully.']);
    }

    // ✅ Restore a soft-deleted law
    public function restore($id)
    {
        $law = Law::onlyTrashed()->findOrFail($id);
        $law->restore();

        return response()->json(['message' => 'Law restored successfully.']);
    }

    public function forceDelete($id)
    {
        $law = Law::onlyTrashed()->findOrFail($id); // must be soft deleted first
        $law->forceDelete();

        return response()->json(['message' => 'Law permanently deleted.']);
    }
}
