<?php

namespace App\Http\Controllers\API\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\LawyerAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LawyerAvailabilityController extends Controller
{
    // إضافة أوقات متاحة جديدة
    public function store(Request $request)
    {
        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:' . now()->format('Y-m-d')],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500',
        ]);

        // التحقق من عدم وجود تعارض
        $conflict = LawyerAvailability::where('lawyer_id', Auth::id())
            ->where('date', $request->date)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->where('status', '!=', 'unavailable')
            ->first();

        if ($conflict) {
            return response()->json([
                'message' => 'يوجد تعارض مع وقت متاح آخر',
            ], 400);
        }

        $availability = LawyerAvailability::create([
            'lawyer_id' => Auth::id(),
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'notes' => $request->notes,
            'status' => 'available',
        ]);

        return response()->json([
            'message' => 'تم إضافة الوقت المتاح بنجاح',
            'availability' => $availability,
        ], 201);
    }

    // عرض الأوقات المتاحة للمحامي
    public function index(Request $request)
    {
        $query = LawyerAvailability::where('lawyer_id', Auth::id());

        if ($date = $request->input('date')) {
            $query->where('date', $date);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $availabilities = $query->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json($availabilities);
    }

    // تحديث وقت متاح
    public function update(Request $request, $id)
    {
        $availability = LawyerAvailability::where('lawyer_id', Auth::id())
            ->findOrFail($id);

        // لا يمكن تعديل وقت محجوز
        if ($availability->status === 'booked') {
            return response()->json([
                'message' => 'لا يمكن تعديل وقت محجوز',
            ], 400);
        }

        $request->validate([
            'date' => ['sometimes', 'date', 'after_or_equal:' . now()->format('Y-m-d')],
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'status' => 'sometimes|in:available,unavailable',
            'notes' => 'nullable|string|max:500',
        ]);

        $availability->update($request->only([
            'date', 'start_time', 'end_time', 'status', 'notes'
        ]));

        return response()->json([
            'message' => 'تم تحديث الوقت المتاح بنجاح',
            'availability' => $availability,
        ]);
    }

    // حذف وقت متاح
    public function destroy($id)
    {
        $availability = LawyerAvailability::where('lawyer_id', Auth::id())
            ->findOrFail($id);

        // لا يمكن حذف وقت محجوز
        if ($availability->status === 'booked') {
            return response()->json([
                'message' => 'لا يمكن حذف وقت محجوز. يجب إلغاء الموعد أولاً',
            ], 400);
        }

        $availability->delete();

        return response()->json([
            'message' => 'تم حذف الوقت المتاح بنجاح',
        ]);
    }

    // إضافة أوقات متعددة (batch)
    public function storeBatch(Request $request)
    {
        $request->validate([
            'availabilities' => 'required|array|min:1',
            'availabilities.*.date' => ['required', 'date', 'after_or_equal:' . now()->format('Y-m-d')],
            'availabilities.*.start_time' => 'required|date_format:H:i',
            'availabilities.*.end_time' => 'required|date_format:H:i|after:availabilities.*.start_time',
            'availabilities.*.notes' => 'nullable|string|max:500',
        ]);

        $created = [];
        $errors = [];

        foreach ($request->availabilities as $index => $availabilityData) {
            try {
                $availability = LawyerAvailability::create([
                    'lawyer_id' => Auth::id(),
                    'date' => $availabilityData['date'],
                    'start_time' => $availabilityData['start_time'],
                    'end_time' => $availabilityData['end_time'],
                    'notes' => $availabilityData['notes'] ?? null,
                    'status' => 'available',
                ]);
                $created[] = $availability;
            } catch (\Exception $e) {
                $errors[] = [
                    'index' => $index,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json([
            'message' => 'تم إضافة ' . count($created) . ' وقت متاح',
            'created' => $created,
            'errors' => $errors,
        ], 201);
    }
}

