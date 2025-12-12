<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\LawyerAvailability;
use Illuminate\Http\Request;

class EmployeeAppointmentController extends Controller
{
    // عرض جميع المواعيد
    public function index(Request $request)
    {
        $query = Appointment::with(['client', 'lawyer', 'consultation']);

        if ($lawyerId = $request->input('lawyer_id')) {
            $query->where('lawyer_id', $lawyerId);
        }

        if ($clientId = $request->input('client_id')) {
            $query->where('client_id', $clientId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($date = $request->input('date')) {
            $query->whereDate('datetime', $date);
        }

        // فلترة المواعيد بوقت مخصص
        if ($request->has('custom_time_only') && $request->custom_time_only) {
            $query->whereNull('availability_id');
        }

        $appointments = $query->orderBy('datetime', 'asc')->get();

        // إضافة is_custom_time_request لكل موعد
        $appointmentsData = $appointments->map(function ($appointment) {
            $data = $appointment->toArray();
            $data['is_custom_time_request'] = $appointment->is_custom_time_request;
            return $data;
        });

        return response()->json($appointmentsData);
    }

    // عرض المواعيد بوقت مخصص (في انتظار التأكيد)
    public function customTimeRequests(Request $request)
    {
        $query = Appointment::with(['client', 'lawyer', 'consultation'])
            ->whereNull('availability_id')
            ->where('status', 'pending');

        if ($lawyerId = $request->input('lawyer_id')) {
            $query->where('lawyer_id', $lawyerId);
        }

        $appointments = $query->orderBy('datetime', 'asc')->get();

        $appointmentsData = $appointments->map(function ($appointment) {
            $data = $appointment->toArray();
            $data['is_custom_time_request'] = true;
            return $data;
        });

        return response()->json($appointmentsData);
    }

    // عرض موعد محدد
    public function show($id)
    {
        $appointment = Appointment::with(['client', 'lawyer', 'consultation', 'availability'])
            ->findOrFail($id);

        $appointmentData = $appointment->toArray();
        $appointmentData['is_custom_time_request'] = $appointment->is_custom_time_request;

        return response()->json($appointmentData);
    }

    // تحديث موعد
    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $request->validate([
            'datetime' => 'sometimes|date|after:now',
            'type' => 'sometimes|in:online,in_office,phone',
            'meeting_link' => 'nullable|url|required_if:type,online',
            'notes' => 'nullable|string|max:1000',
            'status' => 'sometimes|in:pending,confirmed,done,cancelled',
        ]);

        $appointment->update($request->only([
            'datetime', 'type', 'meeting_link', 'notes', 'status'
        ]));

        return response()->json([
            'message' => 'تم تحديث الموعد بنجاح',
            'appointment' => $appointment->load(['client', 'lawyer', 'consultation']),
        ]);
    }

    // حذف موعد
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        // إعادة تفعيل الـ availability إذا كان موجود
        if ($appointment->availability_id) {
            $availability = LawyerAvailability::find($appointment->availability_id);
            if ($availability) {
                $availability->status = 'available';
                $availability->save();
            }
        }

        $appointment->delete();

        return response()->json([
            'message' => 'تم حذف الموعد بنجاح',
        ]);
    }

    // تأكيد موعد
    public function confirm($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'confirmed';
        $appointment->save();

        // إرسال إشعار للعميل والمحامي
        $appointment->client->notify(new \App\Notifications\Consultation\AppointmentConfirmedNotification($appointment));
        $appointment->lawyer->notify(new \App\Notifications\Consultation\AppointmentConfirmedNotification($appointment));

        return response()->json([
            'message' => 'تم تأكيد الموعد بنجاح',
            'appointment' => $appointment->load(['client', 'lawyer', 'consultation']),
        ]);
    }

    // إلغاء موعد
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'cancelled';
        $appointment->cancelled_by = 'employee';
        $appointment->cancellation_reason = $request->cancellation_reason;
        $appointment->save();

        // إعادة تفعيل الـ availability
        if ($appointment->availability_id) {
            $availability = LawyerAvailability::find($appointment->availability_id);
            if ($availability) {
                $availability->status = 'available';
                $availability->save();
            }
        }

        // إرسال إشعار للعميل
        $appointment->client->notify(new \App\Notifications\Consultation\AppointmentCancelledNotification($appointment));

        return response()->json([
            'message' => 'تم إلغاء الموعد بنجاح',
            'appointment' => $appointment->load(['client', 'lawyer', 'consultation']),
        ]);
    }

    // عرض المواعيد بشكل تقويم شهري
    public function calendarMonth(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'lawyer_id' => 'nullable|exists:lawyers,id',
        ]);

        $year = $request->input('year');
        $month = $request->input('month');
        $lawyerId = $request->input('lawyer_id');

        $startDate = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $query = Appointment::with(['client', 'lawyer', 'consultation'])
            ->whereBetween('datetime', [$startDate, $endDate]);

        if ($lawyerId) {
            $query->where('lawyer_id', $lawyerId);
        }

        $appointments = $query->get()->groupBy(function ($appointment) {
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
                        'lawyer' => $apt->lawyer->name,
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

    // عرض المواعيد بشكل أسبوعي
    public function calendarWeek(Request $request)
    {
        $request->validate([
            'date' => 'required|date', // أي تاريخ في الأسبوع
            'lawyer_id' => 'nullable|exists:lawyers,id',
        ]);

        $date = \Carbon\Carbon::parse($request->input('date'));
        $startDate = $date->copy()->startOfWeek();
        $endDate = $date->copy()->endOfWeek();
        $lawyerId = $request->input('lawyer_id');

        $query = Appointment::with(['client', 'lawyer', 'consultation'])
            ->whereBetween('datetime', [$startDate, $endDate]);

        if ($lawyerId) {
            $query->where('lawyer_id', $lawyerId);
        }

        $appointments = $query->get()->groupBy(function ($appointment) {
            return $appointment->datetime->format('Y-m-d');
        });

        $week = [];
        $currentDate = $startDate->copy();

        while ($currentDate->lte($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $week[] = [
                'date' => $dateKey,
                'day_name' => $currentDate->format('l'),
                'day' => $currentDate->day,
                'appointments' => $appointments->get($dateKey, collect())->map(function ($apt) {
                    return [
                        'id' => $apt->id,
                        'time' => $apt->datetime->format('H:i'),
                        'client' => $apt->client->name,
                        'lawyer' => $apt->lawyer->name,
                        'status' => $apt->status,
                        'type' => $apt->type,
                    ];
                })->toArray(),
            ];
            $currentDate->addDay();
        }

        return response()->json([
            'week_start' => $startDate->format('Y-m-d'),
            'week_end' => $endDate->format('Y-m-d'),
            'week' => $week,
        ]);
    }

    // عرض المواعيد والأوقات المتاحة ليوم محدد
    public function calendarDay(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'lawyer_id' => 'nullable|exists:lawyers,id',
        ]);

        $date = $request->input('date');
        $lawyerId = $request->input('lawyer_id');

        // المواعيد المحجوزة
        $appointmentsQuery = Appointment::with(['client', 'lawyer', 'consultation'])
            ->whereDate('datetime', $date);

        if ($lawyerId) {
            $appointmentsQuery->where('lawyer_id', $lawyerId);
        }

        $appointments = $appointmentsQuery->orderBy('datetime', 'asc')->get();

        // الأوقات المتاحة
        $availabilityQuery = LawyerAvailability::where('date', $date)
            ->where('status', 'available')
            ->where('is_vacation', false);

        if ($lawyerId) {
            $availabilityQuery->where('lawyer_id', $lawyerId);
        }

        $availabilities = $availabilityQuery->orderBy('start_time', 'asc')->get();

        return response()->json([
            'date' => $date,
            'appointments' => $appointments->map(function ($apt) {
                return [
                    'id' => $apt->id,
                    'time' => $apt->datetime->format('H:i'),
                    'client' => $apt->client->name,
                    'lawyer' => $apt->lawyer->name,
                    'status' => $apt->status,
                    'type' => $apt->type,
                    'subject' => $apt->subject,
                ];
            }),
            'available_slots' => $availabilities->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'lawyer' => $slot->lawyer->name,
                ];
            }),
        ]);
    }

    // إضافة موعد مباشرة من التقويم
    public function createFromCalendar(Request $request)
    {
        $request->validate([
            'lawyer_id' => 'required|exists:lawyers,id',
            'client_id' => 'required|exists:clients,id',
            'date' => 'required|date|after_or_equal:' . now()->format('Y-m-d'),
            'time' => 'required|date_format:H:i',
            'type' => 'required|in:online,in_office,phone',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'meeting_link' => 'nullable|url|required_if:type,online',
            'notes' => 'nullable|string|max:1000',
        ]);

        $date = $request->input('date');
        $time = $request->input('time');
        $datetime = \Carbon\Carbon::createFromFormat('Y-m-d H:i', $date . ' ' . $time);

        if ($datetime->isPast()) {
            return response()->json([
                'message' => 'لا يمكن حجز موعد في الماضي',
            ], 400);
        }

        // البحث عن availability مناسب أو إنشاء موعد مباشر
        $availability = LawyerAvailability::where('lawyer_id', $request->lawyer_id)
            ->where('date', $date)
            ->where('start_time', '<=', $time)
            ->where('end_time', '>', $time)
            ->where('status', 'available')
            ->where('is_vacation', false)
            ->first();

        $appointment = Appointment::create([
            'consultation_id' => null,
            'availability_id' => $availability?->id,
            'lawyer_id' => $request->lawyer_id,
            'client_id' => $request->client_id,
            'subject' => $request->subject,
            'description' => $request->description,
            'datetime' => $datetime,
            'type' => $request->type,
            'meeting_link' => $request->meeting_link,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        if ($availability) {
            $availability->status = 'booked';
            $availability->save();
        }

        return response()->json([
            'message' => 'تم إنشاء الموعد بنجاح',
            'appointment' => $appointment->load(['client', 'lawyer']),
        ], 201);
    }
}

