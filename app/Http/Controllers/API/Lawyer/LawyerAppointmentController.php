<?php

namespace App\Http\Controllers\API\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LawyerAppointmentController extends Controller
{
    // عرض جميع مواعيد المحامي
    public function index(Request $request)
    {
        $query = Appointment::with(['client', 'consultation'])
            ->where('lawyer_id', Auth::id());

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($date = $request->input('date')) {
            $query->whereDate('datetime', $date);
        }

        $appointments = $query->orderBy('datetime', 'asc')->get();

        return response()->json($appointments);
    }

    // عرض المواعيد القادمة
    public function upcoming()
    {
        $appointments = Appointment::with(['client', 'consultation'])
            ->where('lawyer_id', Auth::id())
            ->upcoming()
            ->orderBy('datetime', 'asc')
            ->get();

        return response()->json($appointments);
    }

    // عرض موعد محدد
    public function show($id)
    {
        $appointment = Appointment::with(['client', 'consultation', 'availability'])
            ->where('lawyer_id', Auth::id())
            ->findOrFail($id);

        return response()->json($appointment);
    }

    // عرض المواعيد بشكل تقويم شهري
    public function calendarMonth(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        $year = $request->input('year');
        $month = $request->input('month');

        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $appointments = Appointment::with(['client', 'consultation'])
            ->where('lawyer_id', Auth::id())
            ->whereBetween('datetime', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($appointment) {
                return $appointment->datetime->format('Y-m-d');
            });

        $calendar = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $calendar[$dateKey] = [
                'date' => $dateKey,
                'day' => $currentDate->day,
                'appointments' => $appointments->get($dateKey, collect())->map(function ($apt) {
                    return [
                        'id' => $apt->id,
                        'time' => $apt->datetime->format('H:i'),
                        'client' => $apt->client->name,
                        'status' => $apt->status,
                        'type' => $apt->type,
                    ];
                })->toArray(),
            ];
            $currentDate->addDay();
        }

        return response()->json([
            'year' => $year,
            'month' => $month,
            'calendar' => $calendar,
        ]);
    }

    // المحامي يمكنه فقط عرض المواعيد (قراءة فقط)
    // التعديل والحذف يتم من قبل الموظف
}

