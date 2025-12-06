<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Law;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\law\StoreLawRequest;
use App\Http\Requests\Admin\law\UpdateLawRequest;

class LawController extends Controller
{
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

    // ✅ Create new law (using StoreLawRequest)
    public function store(StoreLawRequest $request)
    {
        $data = $request->validated();

        $law = Law::create([
            'title'        => $data['title'],
            'category'     => $data['category'],
            'summary'      => $data['summary'],
            'full_content' => $data['full_content'],
            'status'       => $data['status'] ?? 'draft',
        ]);

        return response()->json([
            'message' => 'Law created successfully.',
            'law'     => $law,
        ], 201);
    }

    // ✅ Update law (using UpdateLawRequest)
    public function update(UpdateLawRequest $request, $id)
    {
        $law  = Law::findOrFail($id);
        $data = $request->validated();

        $law->update($data);

        return response()->json([
            'message' => 'Law updated successfully.',
            'law'     => $law,
        ]);
    }

    // ✅ Toggle status between draft and published
    public function toggleStatus($id)
    {
        $law = Law::findOrFail($id);
        $law->status = $law->status === 'draft' ? 'published' : 'draft';
        $law->save();

        return response()->json([
            'message' => "Law status switched to {$law->status}.",
            'law'     => $law,
        ]);
    }

    // ✅ Archive (soft delete) law
    public function destroy($id)
    {
        $law = Law::findOrFail($id);
        $law->delete();

        return response()->json([
            'message' => 'Law archived successfully.',
        ]);
    }

    // ✅ Restore a soft-deleted law
    public function restore($id)
    {
        $law = Law::onlyTrashed()->findOrFail($id);
        $law->restore();

        return response()->json([
            'message' => 'Law restored successfully.',
        ]);
    }

    // ✅ Force delete
    public function forceDelete($id)
    {
        $law = Law::onlyTrashed()->findOrFail($id);
        $law->forceDelete();

        return response()->json([
            'message' => 'Law permanently deleted.',
        ]);
    }
}
