<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\AvailabilityTemplate;
use App\Models\LawyerAvailability;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EmployeeAvailabilityTemplateController extends Controller
{
    // إنشاء قالب أوقات
    public function store(Request $request)
    {
        $request->validate([
            'lawyer_id' => 'required|exists:lawyers,id',
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'days_of_week' => 'required|array|min:1',
            'days_of_week.*' => 'integer|between:0,6', // 0 = الأحد، 6 = السبت
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $template = AvailabilityTemplate::create([
            'lawyer_id' => $request->lawyer_id,
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'days_of_week' => $request->days_of_week,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'message' => 'تم إنشاء القالب بنجاح',
            'template' => $template->load('lawyer'),
        ], 201);
    }

    // تطبيق قالب على فترة زمنية
    public function apply(Request $request, $id)
    {
        $template = AvailabilityTemplate::findOrFail($id);

        $request->validate([
            'start_date' => 'required|date|after_or_equal:' . now()->format('Y-m-d'),
            'end_date' => 'required|date|after:start_date',
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $created = [];
        $errors = [];

        $currentDate = $startDate->copy();
        while ($currentDate->lte($endDate)) {
            $dayOfWeek = $currentDate->dayOfWeek; // 0 = الأحد، 6 = السبت

            // التحقق من أن اليوم موجود في القالب
            if (in_array($dayOfWeek, $template->days_of_week)) {
                // التحقق من عدم وجود تعارض
                $conflict = LawyerAvailability::where('lawyer_id', $template->lawyer_id)
                    ->where('date', $currentDate->format('Y-m-d'))
                    ->where(function ($query) use ($template) {
                        $query->whereBetween('start_time', [$template->start_time, $template->end_time])
                            ->orWhereBetween('end_time', [$template->start_time, $template->end_time])
                            ->orWhere(function ($q) use ($template) {
                                $q->where('start_time', '<=', $template->start_time)
                                    ->where('end_time', '>=', $template->end_time);
                            });
                    })
                    ->where('status', '!=', 'unavailable')
                    ->first();

                if (!$conflict) {
                    try {
                        $availability = LawyerAvailability::create([
                            'lawyer_id' => $template->lawyer_id,
                            'date' => $currentDate->format('Y-m-d'),
                            'start_time' => $template->start_time,
                            'end_time' => $template->end_time,
                            'status' => 'available',
                            'is_vacation' => false,
                        ]);
                        $created[] = $availability;
                    } catch (\Exception $e) {
                        $errors[] = [
                            'date' => $currentDate->format('Y-m-d'),
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            }

            $currentDate->addDay();
        }

        return response()->json([
            'message' => 'تم تطبيق القالب بنجاح',
            'created_count' => count($created),
            'created' => $created,
            'errors' => $errors,
        ], 201);
    }

    // عرض جميع القوالب
    public function index(Request $request)
    {
        $query = AvailabilityTemplate::with('lawyer');

        if ($lawyerId = $request->input('lawyer_id')) {
            $query->where('lawyer_id', $lawyerId);
        }

        if ($isActive = $request->input('is_active')) {
            $query->where('is_active', $isActive);
        }

        $templates = $query->orderBy('created_at', 'desc')->get();

        return response()->json($templates);
    }

    // عرض قالب محدد
    public function show($id)
    {
        $template = AvailabilityTemplate::with('lawyer')->findOrFail($id);
        return response()->json($template);
    }

    // تحديث قالب
    public function update(Request $request, $id)
    {
        $template = AvailabilityTemplate::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'days_of_week' => 'sometimes|array|min:1',
            'days_of_week.*' => 'integer|between:0,6',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        $template->update($request->only([
            'name', 'start_time', 'end_time', 'days_of_week', 'start_date', 'end_date', 'is_active'
        ]));

        return response()->json([
            'message' => 'تم تحديث القالب بنجاح',
            'template' => $template->load('lawyer'),
        ]);
    }

    // حذف قالب
    public function destroy($id)
    {
        $template = AvailabilityTemplate::findOrFail($id);
        $template->delete();

        return response()->json([
            'message' => 'تم حذف القالب بنجاح',
        ]);
    }
}

