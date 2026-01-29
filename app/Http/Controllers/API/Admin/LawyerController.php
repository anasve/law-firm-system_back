<?php

namespace App\Http\Controllers\API\Admin;

use App\Models\Lawyer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Admin\Lawyer\StoreLawyerRequest;
use App\Http\Requests\Admin\Lawyer\UpdateLawyerRequest;

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

        $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');
        $lawyers = $lawyers->map(function ($lawyer) use ($baseUrl) {
            return [
                'id'              => $lawyer->id,
                'name'            => $lawyer->name,
                'email'           => $lawyer->email,
                'age'             => $lawyer->age,
                'phone'           => $lawyer->phone,
                'address'         => $lawyer->address,
                'photo'           => $lawyer->photo ? $baseUrl . '/storage/' . $lawyer->photo : null,
                'photo_path'      => $lawyer->photo,
                'certificate'     => $lawyer->certificate ? $baseUrl . '/storage/' . $lawyer->certificate : null,
                'certificate_path' => $lawyer->certificate,
                'specializations' => $lawyer->specializations->pluck('name'),
                'created_at'      => $lawyer->created_at,
            ];
        });

        return response()->json($lawyers);
    }

    public function show($id)
    {
        $lawyer = Lawyer::with('specializations')->findOrFail($id);

        $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');
        return response()->json([
            'id'              => $lawyer->id,
            'name'            => $lawyer->name,
            'email'           => $lawyer->email,
            'age'             => $lawyer->age,
            'phone'           => $lawyer->phone,
            'address'         => $lawyer->address,
            'photo'           => $lawyer->photo ? $baseUrl . '/storage/' . $lawyer->photo : null,
            'photo_path'      => $lawyer->photo,
            'certificate'     => $lawyer->certificate ? $baseUrl . '/storage/' . $lawyer->certificate : null,
            'certificate_path' => $lawyer->certificate,
            'specializations' => $lawyer->specializations->pluck('name'),
            'created_at'      => $lawyer->created_at,
        ]);
    }

    public function store(StoreLawyerRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('lawyers/photos', 'public');
        }

        if ($request->hasFile('certificate')) {
            $data['certificate'] = $request->file('certificate')->store('certificates', 'public');
        }

        $data['password'] = Hash::make($data['password']);

        $lawyer = Lawyer::create($data);

        if (!empty($data['specialization_ids'])) {
            $lawyer->specializations()->sync($data['specialization_ids']);
        }

        $lawyer->load('specializations');

        $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');
        return response()->json([
            'message' => 'Lawyer created successfully',
            'lawyer'  => [
                'id'              => $lawyer->id,
                'name'            => $lawyer->name,
                'email'           => $lawyer->email,
                'age'             => $lawyer->age,
                'phone'           => $lawyer->phone,
                'address'         => $lawyer->address,
                'photo'           => $lawyer->photo ? $baseUrl . '/storage/' . $lawyer->photo : null,
                'photo_path'      => $lawyer->photo,
                'certificate'     => $lawyer->certificate ? $baseUrl . '/storage/' . $lawyer->certificate : null,
                'certificate_path' => $lawyer->certificate,
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
            // Delete old photo if exists
            if ($lawyer->photo) {
                Storage::disk('public')->delete($lawyer->photo);
            }
            $data['photo'] = $request->file('photo')->store('lawyers/photos', 'public');
        }

        if ($request->hasFile('certificate')) {
            // Delete old certificate if exists
            if ($lawyer->certificate) {
                Storage::disk('public')->delete($lawyer->certificate);
            }
            $data['certificate'] = $request->file('certificate')->store('lawyers/certificates', 'public');
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $lawyer->update($data);

        if (!empty($data['specialization_ids'])) {
            $lawyer->specializations()->sync($data['specialization_ids']);
        }

        $lawyer->load('specializations');

        $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');
        return response()->json([
            'message' => 'Lawyer updated successfully',
            'lawyer'  => [
                'id'              => $lawyer->id,
                'name'            => $lawyer->name,
                'email'           => $lawyer->email,
                'age'             => $lawyer->age,
                'phone'           => $lawyer->phone,
                'address'         => $lawyer->address,
                'photo'           => $lawyer->photo ? $baseUrl . '/storage/' . $lawyer->photo : null,
                'photo_path'      => $lawyer->photo,
                'certificate'     => $lawyer->certificate ? $baseUrl . '/storage/' . $lawyer->certificate : null,
                'certificate_path' => $lawyer->certificate,
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
        $baseUrl = rtrim(request()->getSchemeAndHttpHost(), '/');
        $archived = Lawyer::onlyTrashed()->with('specializations')->get()->map(function ($lawyer) use ($baseUrl) {
            return [
                'id'              => $lawyer->id,
                'name'            => $lawyer->name,
                'email'           => $lawyer->email,
                'age'             => $lawyer->age,
                'phone'           => $lawyer->phone,
                'address'         => $lawyer->address,
                'photo'           => $lawyer->photo ? $baseUrl . '/storage/' . $lawyer->photo : null,
                'photo_path'      => $lawyer->photo,
                'certificate'     => $lawyer->certificate ? $baseUrl . '/storage/' . $lawyer->certificate : null,
                'certificate_path' => $lawyer->certificate,
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
