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
        // تحديث المواعيد المنتهية تلقائياً قبل جلبها
        Appointment::markCompletedAppointments();

        $query = Appointment::with(['client', 'lawyer', 'consultation', 'availability']);

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

    // قبول موعد (Accept Appointment)
    public function accept(Request $request, $id)
    {
        $request->validate([
            'availability_id' => 'nullable|exists:lawyer_availability,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $appointment = Appointment::findOrFail($id);

        // التحقق من أن الموعد في حالة pending
        if ($appointment->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending appointments can be accepted.',
            ], 400);
        }

        // إذا كان موعد بوقت مخصص وتم إرسال availability_id، قم بتعيينه
        if ($request->has('availability_id') && $appointment->is_custom_time_request) {
            $availability = LawyerAvailability::where('id', $request->availability_id)
                ->where('lawyer_id', $appointment->lawyer_id)
                ->where('status', 'available')
                ->firstOrFail();

            // تحديث الموعد
            $appointment->availability_id = $availability->id;
            $date = $availability->date;
            $time = strlen($availability->start_time) == 5 ? $availability->start_time . ':00' : $availability->start_time;
            $appointment->datetime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . $time);

            // تحديث الـ availability
            $availability->status = 'booked';
            $availability->save();
        }

        // قبول الموعد
        $appointment->status = 'confirmed';
        if ($request->has('notes')) {
            $appointment->notes = ($appointment->notes ? $appointment->notes . ' | ' : '') . $request->notes;
        }
        $appointment->save();

        // إرسال إشعار للعميل والمحامي
        $appointment->client->notify(new \App\Notifications\Consultation\AppointmentConfirmedNotification($appointment));
        $appointment->lawyer->notify(new \App\Notifications\Consultation\AppointmentConfirmedNotification($appointment));

        return response()->json([
            'message' => 'Appointment accepted successfully.',
            'appointment' => $appointment->load(['client', 'lawyer', 'consultation', 'availability']),
        ]);
    }

    // رفض موعد (Reject Appointment)
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $appointment = Appointment::findOrFail($id);

        // التحقق من أن الموعد في حالة pending
        if ($appointment->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending appointments can be rejected.',
            ], 400);
        }

        // رفض الموعد
        $appointment->status = 'cancelled';
        $appointment->cancelled_by = 'employee';
        $appointment->cancellation_reason = $request->rejection_reason;
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
            'message' => 'Appointment rejected successfully.',
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

    // إنشاء موعد من التقويم
    public function calendarCreate(Request $request)
    {
        $request->validate([
            'lawyer_id' => 'required|exists:lawyers,id',
            'client_id' => 'required|exists:clients,id',
            'datetime' => 'required|date',
            'subject' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'availability_id' => 'nullable|exists:lawyer_availability,id',
        ]);

        $appointment = Appointment::create([
            'lawyer_id' => $request->lawyer_id,
            'client_id' => $request->client_id,
            'datetime' => $request->datetime,
            'subject' => $request->subject,
            'description' => $request->description,
            'availability_id' => $request->availability_id,
            'status' => 'pending',
            'type' => 'in_office',
        ]);

        // تحديث حالة availability إذا تم تعيينه
        if ($request->availability_id) {
            $availability = LawyerAvailability::find($request->availability_id);
            if ($availability) {
                $availability->status = 'booked';
                $availability->save();
            }
        }

        return response()->json([
            'message' => 'Appointment created successfully.',
            'appointment' => $appointment->load(['client', 'lawyer', 'consultation', 'availability']),
        ], 201);
    }

}

