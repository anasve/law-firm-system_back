<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\LawyerAvailability;
use App\Models\Lawyer;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeAvailabilityController extends Controller
{
    // إضافة وقت متاح لمحامي
    public function store(Request $request)
    {
        $request->validate([
            'lawyer_id' => 'required|exists:lawyers,id',
            'date' => ['required', 'date', 'after_or_equal:' . now()->format('Y-m-d')],
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'notes' => 'nullable|string|max:500',
        ]);

        // التحقق من عدم وجود تعارض
        $conflict = LawyerAvailability::where('lawyer_id', $request->lawyer_id)
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
                'message' => 'This time conflicts with another availability slot.',
            ], 400);
        }

        $availability = LawyerAvailability::create([
            'lawyer_id' => $request->lawyer_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'notes' => $request->notes,
            'status' => 'available',
            'is_vacation' => $request->is_vacation ?? false,
            'vacation_reason' => $request->vacation_reason ?? null,
        ]);

        return response()->json([
            'message' => 'Availability slot added successfully.',
            'availability' => $availability->load('lawyer'),
        ], 201);
    }

    // عرض جميع الأوقات المتاحة
    public function index(Request $request)
    {
        $query = LawyerAvailability::with('lawyer');

        if ($lawyerId = $request->input('lawyer_id')) {
            $query->where('lawyer_id', $lawyerId);
        }

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

    // عرض وقت متاح محدد
    public function show($id)
    {
        $availability = LawyerAvailability::with('lawyer')->findOrFail($id);
        return response()->json($availability);
    }

    // تحديث وقت متاح
    public function update(Request $request, $id)
    {
        $availability = LawyerAvailability::findOrFail($id);

        // لا يمكن تعديل وقت محجوز
        if ($availability->status === 'booked') {
            return response()->json([
                'message' => 'Cannot update a booked slot.',
            ], 400);
        }

        $request->validate([
            'date' => ['sometimes', 'date', 'after_or_equal:' . now()->format('Y-m-d')],
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'status' => 'sometimes|in:available,unavailable',
            'notes' => 'nullable|string|max:500',
            'is_vacation' => 'boolean',
            'vacation_reason' => 'nullable|string|max:500',
        ]);

        $availability->update($request->only([
            'date', 'start_time', 'end_time', 'status', 'notes', 'is_vacation', 'vacation_reason'
        ]));

        return response()->json([
            'message' => 'Availability slot updated successfully.',
            'availability' => $availability->load('lawyer'),
        ]);
    }

    // حذف وقت متاح
    public function destroy($id)
    {
        $availability = LawyerAvailability::findOrFail($id);

        // لا يمكن حذف وقت محجوز
        if ($availability->status === 'booked') {
            return response()->json([
                'message' => 'Cannot delete a booked slot. Cancel the appointment first.',
            ], 400);
        }

        $availability->delete();

        return response()->json([
            'message' => 'Availability slot deleted successfully.',
        ]);
    }

    // إضافة أوقات متعددة دفعة واحدة
    public function storeBatch(Request $request)
    {
        $request->validate([
            'lawyer_id' => 'required|exists:lawyers,id',
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
                    'lawyer_id' => $request->lawyer_id,
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
            'message' => count($created) . ' availability slot(s) added.',
            'created' => $created,
            'errors' => $errors,
        ], 201);
    }

    // إنشاء جدول عمل بسيط (يولد الأوقات تلقائياً)
    public function createSchedule(Request $request)
    {
        $request->validate([
            'lawyer_id' => 'required|exists:lawyers,id',
            'days_of_week' => 'required|array|min:1', // [1,2,3,4,5] = الاثنين-الجمعة
            'days_of_week.*' => 'integer|between:0,6',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'start_date' => 'required|date|after_or_equal:' . now()->format('Y-m-d'),
            'end_date' => 'required|date|after:start_date',
            'slot_duration' => 'nullable|integer|min:15|max:240', // مدة كل موعد بالدقائق (افتراضي 60)
        ]);

        $lawyerId = $request->lawyer_id;
        $daysOfWeek = $request->days_of_week;
        $startTime = Carbon::createFromFormat('H:i', $request->start_time);
        $endTime = Carbon::createFromFormat('H:i', $request->end_time);
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $slotDuration = $request->slot_duration ?? 60; // افتراضي 60 دقيقة

        $created = 0;
        $skipped = 0;
        $errors = [];

        $currentDate = $startDate->copy();
        
        while ($currentDate->lte($endDate)) {
            $dayOfWeek = $currentDate->dayOfWeek; // 0 = الأحد، 6 = السبت

            // التحقق من أن اليوم موجود في الأيام المحددة
            if (in_array($dayOfWeek, $daysOfWeek)) {
                // توليد المواعيد (slots) لهذا اليوم
                $currentSlotTime = $startTime->copy();
                
                while ($currentSlotTime->copy()->addMinutes($slotDuration)->lte($endTime)) {
                    $slotStart = $currentSlotTime->format('H:i');
                    $slotEnd = $currentSlotTime->copy()->addMinutes($slotDuration)->format('H:i');
                    
                    // التحقق من عدم وجود تعارض
                    $conflict = LawyerAvailability::where('lawyer_id', $lawyerId)
                        ->where('date', $currentDate->format('Y-m-d'))
                        ->where(function ($query) use ($slotStart, $slotEnd) {
                            $query->whereBetween('start_time', [$slotStart, $slotEnd])
                                ->orWhereBetween('end_time', [$slotStart, $slotEnd])
                                ->orWhere(function ($q) use ($slotStart, $slotEnd) {
                                    $q->where('start_time', '<=', $slotStart)
                                        ->where('end_time', '>=', $slotEnd);
                                });
                        })
                        ->where('status', '!=', 'unavailable')
                        ->first();

                    if (!$conflict) {
                        try {
                            LawyerAvailability::create([
                                'lawyer_id' => $lawyerId,
                                'date' => $currentDate->format('Y-m-d'),
                                'start_time' => $slotStart,
                                'end_time' => $slotEnd,
                                'status' => 'available',
                                'is_vacation' => false,
                            ]);
                            $created++;
                        } catch (\Exception $e) {
                            $errors[] = [
                                'date' => $currentDate->format('Y-m-d'),
                                'time' => $slotStart . '-' . $slotEnd,
                                'error' => $e->getMessage(),
                            ];
                        }
                    } else {
                        $skipped++;
                    }

                    $currentSlotTime->addMinutes($slotDuration);
                }
            }

            $currentDate->addDay();
        }

        return response()->json([
            'message' => 'Schedule created successfully.',
            'created_slots' => $created,
            'skipped_slots' => $skipped,
            'errors' => $errors,
        ], 201);
    }
}

