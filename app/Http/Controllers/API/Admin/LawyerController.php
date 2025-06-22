<?php
namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Lawyer\StoreLawyerRequest;
use App\Http\Requests\Admin\Lawyer\UpdateLawyerRequest;
use App\Models\Lawyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LawyerController extends Controller
{
    public function index(Request $request)
    {
        $query = Lawyer::with('specializations');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $lawyers = $query->get();

        if ($lawyers->isEmpty()) {
            return response()->json([
                'message' => 'No lawyers found matching your search.',
            ], 404);
        }

        $lawyers = $lawyers->map(function ($lawyer) {
            return [
                'id'              => $lawyer->id,
                'name'            => $lawyer->name,
                'email'           => $lawyer->email,
                'age'             => $lawyer->age,
                'photo'           => $lawyer->photo,
                'certificate'     => $lawyer->certificate,
                'specializations' => $lawyer->specializations->pluck('name'),
                'created_at'      => $lawyer->created_at,
            ];
        });

        return response()->json($lawyers);
    }

    public function show($id)
    {
        $lawyer = Lawyer::with('specializations')->findOrFail($id);

        return response()->json([
            'id'              => $lawyer->id,
            'name'            => $lawyer->name,
            'email'           => $lawyer->email,
            'age'             => $lawyer->age,
            'photo'           => $lawyer->photo,
            'certificate'     => $lawyer->certificate,
            'specializations' => $lawyer->specializations->pluck('name'),
            'created_at'      => $lawyer->created_at,
        ]);
    }

    public function store(StoreLawyerRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        if ($request->hasFile('certificate')) {
            $data['certificate'] = $request->file('certificate')->store('certificates', 'public');
        }

        $data['password'] = Hash::make($data['password']);

        $lawyer = Lawyer::create($data);

        if (isset($data['specializations'])) {
            $lawyer->specializations()->sync($data['specializations']);
        }

        $lawyer->load('specializations');

        return response()->json([
            'message' => 'Lawyer created successfully',
            'lawyer'  => [
                'id'              => $lawyer->id,
                'name'            => $lawyer->name,
                'email'           => $lawyer->email,
                'age'             => $lawyer->age,
                'photo'           => $lawyer->photo,
                'certificate'     => $lawyer->certificate,
                'specializations' => $lawyer->specializations->pluck('name'),
                'created_at'      => $lawyer->created_at,
            ],
        ], 201);
    }

    public function update(UpdateLawyerRequest $request, $id)
    {
        $lawyer = Lawyer::findOrFail($id);
        $data   = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('photos', 'public');
        }

        if ($request->hasFile('certificate')) {
            $data['certificate'] = $request->file('certificate')->store('certificates', 'public');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $lawyer->update($data);

        if (isset($data['specializations'])) {
            $lawyer->specializations()->sync($data['specializations']);
        }

        $lawyer->load('specializations');

        return response()->json([
            'message' => 'Lawyer updated successfully',
            'lawyer'  => [
                'id'              => $lawyer->id,
                'name'            => $lawyer->name,
                'email'           => $lawyer->email,
                'age'             => $lawyer->age,
                'photo'           => $lawyer->photo,
                'certificate'     => $lawyer->certificate,
                'specializations' => $lawyer->specializations->pluck('name'),
            ],
        ]);
    }

    public function destroy($id)
    {
        $lawyer = Lawyer::findOrFail($id);
        $lawyer->delete();

        return response()->json(['message' => 'Lawyer archived']);
    }

    public function archived()
    {
        $archived = Lawyer::onlyTrashed()->with('specializations')->get()->map(function ($lawyer) {
            return [
                'id'              => $lawyer->id,
                'name'            => $lawyer->name,
                'email'           => $lawyer->email,
                'age'             => $lawyer->age,
                'photo'           => $lawyer->photo,
                'certificate'     => $lawyer->certificate,
                'specializations' => $lawyer->specializations->pluck('name'),
                'deleted_at'      => $lawyer->deleted_at,
            ];
        });

        return response()->json($archived);
    }

    public function restore($id)
    {
        $lawyer = Lawyer::onlyTrashed()->findOrFail($id);
        $lawyer->restore();

        return response()->json(['message' => 'Lawyer restored successfully']);
    }

    public function forceDelete($id)
    {
        $lawyer = Lawyer::onlyTrashed()->findOrFail($id);

        if ($lawyer->photo) {
            Storage::disk('public')->delete($lawyer->photo);
        }

        if ($lawyer->certificate) {
            Storage::disk('public')->delete($lawyer->certificate);
        }

        $lawyer->forceDelete();

        return response()->json(['message' => 'Lawyer permanently deleted']);
    }

    public function total()
    {
        return response()->json(['total_lawyers' => Lawyer::count()]);
    }
}
