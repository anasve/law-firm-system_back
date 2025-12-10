<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use App\Models\LawyerAvailability;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Notifications\Consultation\NewAppointmentNotification;
use App\Notifications\Consultation\AppointmentCancelledNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    // عرض المواعيد المتاحة لمحامي معين
    public function getAvailableSlots(Request $request, $lawyerId)
    {
        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:' . now()->format('Y-m-d')],
        ]);

        $date = $request->input('date');

        // التحقق من أن التاريخ ليس في الماضي
        if (\Carbon\Carbon::parse($date)->isPast() && !\Carbon\Carbon::parse($date)->isToday()) {
            return response()->json([
                'message' => 'لا يمكن حجز موعد في الماضي',
            ], 400);
        }

        $availableSlots = LawyerAvailability::where('lawyer_id', $lawyerId)
            ->where('date', $date)
            ->where('status', 'available')
            ->where('is_vacation', false)
            ->whereNotIn('id', function ($query) use ($date) {
                $query->select('availability_id')
                    ->from('appointments')
                    ->where('status', '!=', 'cancelled')
                    ->whereDate('datetime', $date)
                    ->whereNotNull('availability_id');
            })
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'date' => $date,
            'lawyer_id' => $lawyerId,
            'available_slots' => $availableSlots->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'duration' => $this->calculateDuration($slot->start_time, $slot->end_time),
                ];
            }),
        ]);
    }

    // حجز موعد مباشر بدون استشارة
    public function bookDirectAppointment(Request $request)
    {
        $request->validate([
            'lawyer_id' => 'required|exists:lawyers,id',
            'availability_id' => 'required|exists:lawyer_availability,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'type' => 'required|in:online,in_office,phone',
            'meeting_link' => 'nullable|url|required_if:type,online',
            'notes' => 'nullable|string|max:1000',
        ]);

        $lawyer = \App\Models\Lawyer::findOrFail($request->lawyer_id);
        
        $availability = LawyerAvailability::where('id', $request->availability_id)
            ->where('lawyer_id', $request->lawyer_id)
            ->where('status', 'available')
            ->where('is_vacation', false)
            ->firstOrFail();

        // التحقق من أن الموعد ليس في الماضي
        $date = $availability->date;
        $time = strlen($availability->start_time) == 5 ? $availability->start_time . ':00' : $availability->start_time;
        $datetime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . $time);
        
        if ($datetime->isPast()) {
            return response()->json([
                'message' => 'لا يمكن حجز موعد في الماضي',
            ], 400);
        }

        // التحقق من عدم وجود موعد آخر في نفس الوقت
        $existingAppointment = Appointment::where('availability_id', $availability->id)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingAppointment) {
            return response()->json([
                'message' => 'هذا الموعد محجوز بالفعل',
            ], 400);
        }

        // إنشاء الموعد
        $appointment = Appointment::create([
            'consultation_id' => null, // بدون استشارة
            'availability_id' => $availability->id,
            'lawyer_id' => $request->lawyer_id,
            'client_id' => Auth::id(),
            'subject' => $request->subject,
            'description' => $request->description,
            'datetime' => $datetime,
            'type' => $request->type,
            'meeting_link' => $request->meeting_link,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        // تحديث حالة الـ availability
        $availability->status = 'booked';
        $availability->save();

        // إرسال إشعار للمحامي
        $lawyer->notify(new NewAppointmentNotification($appointment));

        return response()->json([
            'message' => 'تم حجز الموعد بنجاح. سيتم تأكيده من قبل الموظف قريباً.',
            'appointment' => $appointment->load(['lawyer']),
        ], 201);
    }

    // حجز موعد من المواعيد المتاحة (مع استشارة)
    public function bookAppointment(Request $request, $consultationId = null)
    {
        $request->validate([
            'availability_id' => 'required|exists:lawyer_availability,id',
            'type' => 'required|in:online,in_office,phone',
            'meeting_link' => 'nullable|url|required_if:type,online',
            'notes' => 'nullable|string|max:1000',
        ]);

        // إذا كان consultationId موجود، يجب أن تكون الاستشارة مقبولة
        if ($consultationId) {
            $consultation = Consultation::where('client_id', Auth::id())
                ->where('status', 'accepted')
                ->findOrFail($consultationId);
            $lawyerId = $consultation->lawyer_id;
        } else {
            // حجز مباشر بدون استشارة
            return $this->bookDirectAppointment($request);
        }

        $availability = LawyerAvailability::where('id', $request->availability_id)
            ->where('lawyer_id', $lawyerId)
            ->where('status', 'available')
            ->where('is_vacation', false)
            ->firstOrFail();

        // التحقق من عدم وجود موعد آخر في نفس الوقت
        $existingAppointment = Appointment::where('availability_id', $availability->id)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingAppointment) {
            return response()->json([
                'message' => 'هذا الموعد محجوز بالفعل. يرجى اختيار وقت آخر.',
            ], 400);
        }

        // التحقق من أن الموعد ليس في الماضي
        $date = $availability->date;
        $time = strlen($availability->start_time) == 5 ? $availability->start_time . ':00' : $availability->start_time;
        $datetime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . $time);
        
        if ($datetime->isPast()) {
            return response()->json([
                'message' => 'لا يمكن حجز موعد في الماضي',
            ], 400);
        }

        $appointment = Appointment::create([
            'consultation_id' => $consultation->id,
            'availability_id' => $availability->id,
            'lawyer_id' => $lawyerId,
            'client_id' => Auth::id(),
            'datetime' => $datetime,
            'type' => $request->type,
            'meeting_link' => $request->meeting_link,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        // تحديث حالة الـ availability
        $availability->status = 'booked';
        $availability->save();

        // إرسال إشعار للمحامي
        $consultation->lawyer->notify(new NewAppointmentNotification($appointment));

        return response()->json([
            'message' => 'تم حجز الموعد بنجاح. سيتم تأكيده من قبل الموظف قريباً.',
            'appointment' => $appointment->load(['lawyer', 'consultation']),
        ], 201);
    }

    // عرض مواعيد العميل
    public function myAppointments(Request $request)
    {
        $query = Appointment::with(['lawyer', 'consultation'])
            ->where('client_id', Auth::id());

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $appointments = $query->orderBy('datetime', 'asc')->get();

        return response()->json($appointments);
    }

    // عرض موعد محدد
    public function show($id)
    {
        // التحقق من أن الـ ID رقم وليس نص (مثل "direct")
        if (!is_numeric($id)) {
            return response()->json([
                'message' => 'معرف الموعد غير صحيح',
            ], 404);
        }

        $appointment = Appointment::with(['lawyer', 'consultation', 'availability'])
            ->where('client_id', Auth::id())
            ->findOrFail($id);

        return response()->json($appointment);
    }

    // إلغاء موعد من قبل العميل
    public function cancel(Request $request, $id)
    {
        // التحقق من أن الـ ID رقم
        if (!is_numeric($id)) {
            return response()->json([
                'message' => 'معرف الموعد غير صحيح',
            ], 404);
        }

        $request->validate([
            'cancellation_reason' => 'nullable|string|max:500',
        ]);

        $appointment = Appointment::where('client_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->findOrFail($id);

        // لا يمكن الإلغاء قبل الموعد بساعة واحدة
        $hoursUntilAppointment = $appointment->datetime->diffInHours(now(), false);
        
        if ($hoursUntilAppointment < 1 && $hoursUntilAppointment > 0) {
            return response()->json([
                'message' => 'لا يمكن إلغاء الموعد قبل ساعة واحدة من الموعد المحدد. الموعد بعد ' . round($hoursUntilAppointment * 60) . ' دقيقة.',
            ], 400);
        }

        // التحقق من أن الموعد ليس في الماضي
        if ($appointment->datetime->isPast()) {
            return response()->json([
                'message' => 'لا يمكن إلغاء موعد منتهي',
            ], 400);
        }

        $appointment->status = 'cancelled';
        $appointment->cancelled_by = 'client';
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

        // إرسال إشعار للمحامي
        if ($appointment->lawyer) {
            $appointment->lawyer->notify(new AppointmentCancelledNotification($appointment));
        }

        return response()->json([
            'message' => 'تم إلغاء الموعد بنجاح',
            'appointment' => $appointment->load(['lawyer', 'consultation']),
        ]);
    }

    // إعادة جدولة موعد
    public function reschedule(Request $request, $id)
    {
        // التحقق من أن الـ ID رقم
        if (!is_numeric($id)) {
            return response()->json([
                'message' => 'معرف الموعد غير صحيح',
            ], 404);
        }

        $request->validate([
            'availability_id' => 'required|exists:lawyer_availability,id',
        ]);

        $appointment = Appointment::where('client_id', Auth::id())
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'done')
            ->findOrFail($id);

        // لا يمكن إعادة الجدولة قبل الموعد بساعة
        if ($appointment->datetime->diffInHours(now()) < 1) {
            return response()->json([
                'message' => 'لا يمكن إعادة جدولة الموعد قبل ساعة واحدة من الموعد المحدد',
            ], 400);
        }

        $newAvailability = LawyerAvailability::where('id', $request->availability_id)
            ->where('lawyer_id', $appointment->lawyer_id)
            ->where('status', 'available')
            ->where('is_vacation', false)
            ->firstOrFail();

        // التحقق من عدم وجود موعد آخر
        $existingAppointment = Appointment::where('availability_id', $newAvailability->id)
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $appointment->id)
            ->first();

        if ($existingAppointment) {
            return response()->json([
                'message' => 'هذا الموعد محجوز بالفعل',
            ], 400);
        }

        // إعادة تفعيل الـ availability القديم
        if ($appointment->availability_id) {
            $oldAvailability = LawyerAvailability::find($appointment->availability_id);
            if ($oldAvailability) {
                $oldAvailability->status = 'available';
                $oldAvailability->save();
            }
        }

        // تحديث الموعد
        $date = $newAvailability->date;
        $time = strlen($newAvailability->start_time) == 5 ? $newAvailability->start_time . ':00' : $newAvailability->start_time;
        $datetime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . $time);

        $appointment->availability_id = $newAvailability->id;
        $appointment->datetime = $datetime;
        $appointment->status = 'pending'; // يعود لـ pending حتى يؤكد الموظف
        $appointment->save();

        // تحديث الـ availability الجديد
        $newAvailability->status = 'booked';
        $newAvailability->save();

        // إرسال إشعارات
        // TODO: إشعار للمحامي والموظف

        return response()->json([
            'message' => 'تم إعادة جدولة الموعد بنجاح',
            'appointment' => $appointment->load(['lawyer', 'consultation', 'availability']),
        ]);
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

        $appointments = Appointment::with(['lawyer', 'consultation'])
            ->where('client_id', Auth::id())
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

    // دالة مساعدة لحساب المدة
    private function calculateDuration($start, $end)
    {
        // التأكد من الصيغة الصحيحة
        $startTime = strlen($start) == 5 ? $start . ':00' : $start;
        $endTime = strlen($end) == 5 ? $end . ':00' : $end;
        
        $startCarbon = \Carbon\Carbon::createFromFormat('H:i:s', $startTime);
        $endCarbon = \Carbon\Carbon::createFromFormat('H:i:s', $endTime);
        return $startCarbon->diffInMinutes($endCarbon);
    }
}

