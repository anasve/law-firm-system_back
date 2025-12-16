<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreConsultationRequest;
use App\Http\Requests\Client\SendMessageRequest;
use App\Http\Requests\Client\CreateReviewRequest;
use App\Models\Consultation;
use App\Models\ConsultationMessage;
use App\Models\ConsultationAttachment;
use App\Models\ConsultationReview;
use App\Models\Appointment;
use App\Models\LawyerAvailability;
use App\Notifications\Consultation\NewMessageNotification;
use App\Notifications\Consultation\NewConsultationNotification;
use App\Notifications\Consultation\NewAppointmentNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ConsultationController extends Controller
{
    // عرض جميع استشارات العميل
    public function index(Request $request)
    {
        $query = Consultation::with(['lawyer', 'specialization', 'review'])
            ->where('client_id', Auth::id());

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $consultations = $query->orderBy('created_at', 'desc')->get();

        return response()->json($consultations);
    }

    // عرض استشارة محددة
    public function show($id)
    {
        $consultation = Consultation::with([
            'lawyer',
            'specialization',
            'attachments',
            'messages' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'appointments',
            'review'
        ])->where('client_id', Auth::id())->findOrFail($id);

        return response()->json($consultation);
    }

    // إنشاء استشارة جديدة
    public function store(StoreConsultationRequest $request)
    {
        $data = $request->validated();
        $data['client_id'] = Auth::id();

        // إذا لم يتم اختيار محامي، يتم البحث عن محامي حسب التخصص تلقائياً
        if (!$data['lawyer_id'] && $data['specialization_id']) {
            $lawyer = \App\Models\Lawyer::whereHas('specializations', function ($q) use ($data) {
                $q->where('specializations.id', $data['specialization_id']);
            })->first();

            if ($lawyer) {
                $data['lawyer_id'] = $lawyer->id;
            }
        }

        $consultation = Consultation::create($data);

        // رفع المرفقات
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('consultations/attachments', 'public');
                ConsultationAttachment::create([
                    'consultation_id' => $consultation->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        $consultation->load(['lawyer', 'specialization', 'attachments']);

        $appointment = null;

        // إذا اختار العميل حجز موعد مباشر
        if ($request->preferred_channel === 'appointment' && $request->has('appointment_availability_id')) {
            // يجب أن يكون هناك محامي محدد
            if (!$consultation->lawyer_id) {
                return response()->json([
                    'message' => 'يجب اختيار محامي محدد لحجز موعد مباشر',
                ], 400);
            }

            $availability = LawyerAvailability::where('id', $request->appointment_availability_id)
                ->where('lawyer_id', $consultation->lawyer_id)
                ->where('status', 'available')
                ->where('is_vacation', false)
                ->firstOrFail();

            // التحقق من عدم وجود موعد آخر
            $existingAppointment = Appointment::where('availability_id', $availability->id)
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existingAppointment) {
                return response()->json([
                    'message' => 'هذا الموعد محجوز بالفعل',
                ], 400);
            }

            // إنشاء الموعد
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
                'lawyer_id' => $consultation->lawyer_id,
                'client_id' => Auth::id(),
                'datetime' => $datetime,
                'type' => $request->appointment_type,
                'meeting_link' => $request->appointment_meeting_link,
                'notes' => $request->appointment_notes,
                'status' => 'pending',
            ]);

            // تحديث حالة الـ availability
            $availability->status = 'booked';
            $availability->save();

            // إذا كان المحامي محدد، قبول الاستشارة تلقائياً
            if ($consultation->lawyer_id) {
                $consultation->status = 'accepted';
                $consultation->save();
            }
        }

        // إرسال إشعار للمحامي إذا تم تعيينه
        if ($consultation->lawyer) {
            $consultation->lawyer->notify(new NewConsultationNotification($consultation));
            
            // إشعار بالموعد إذا تم حجزه
            if ($appointment) {
                $consultation->lawyer->notify(new NewAppointmentNotification($appointment));
            }
        }

        $response = [
            'message' => 'تم إنشاء الاستشارة بنجاح',
            'consultation' => $consultation->load(['lawyer', 'specialization', 'attachments']),
        ];

        if ($appointment) {
            $response['appointment'] = $appointment->load(['lawyer', 'availability']);
            $response['message'] = 'تم إنشاء الاستشارة وحجز الموعد بنجاح';
        }

        return response()->json($response, 201);
    }

    // إرسال رسالة في المحادثة
    public function sendMessage(SendMessageRequest $request, $consultationId)
    {
        $consultation = Consultation::where('client_id', Auth::id())
            ->where('status', '!=', 'rejected')
            ->findOrFail($consultationId);

        $data = [
            'consultation_id' => $consultation->id,
            'sender_type' => 'client',
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ];

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('consultations/messages', 'public');
            $data['attachment_path'] = $path;
        }

        $message = ConsultationMessage::create($data);
        $message->load('sender');

        // إرسال إشعار للمحامي
        if ($consultation->lawyer) {
            $consultation->lawyer->notify(new NewMessageNotification($message));
        }

        return response()->json([
            'message' => 'تم إرسال الرسالة بنجاح',
            'message_data' => $message,
        ], 201);
    }

    // عرض رسائل الاستشارة
    public function getMessages($consultationId)
    {
        $consultation = Consultation::where('client_id', Auth::id())
            ->findOrFail($consultationId);

        $messages = ConsultationMessage::where('consultation_id', $consultationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // إلغاء الاستشارة
    public function cancel($id)
    {
        $consultation = Consultation::where('client_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $consultation->status = 'cancelled';
        $consultation->save();

        return response()->json(['message' => 'تم إلغاء الاستشارة بنجاح']);
    }

    // إكمال الاستشارة
    public function complete($id)
    {
        $consultation = Consultation::where('client_id', Auth::id())
            ->whereIn('status', ['accepted', 'pending'])
            ->findOrFail($id);

        // التحقق من وجود محامي للاستشارة
        if (!$consultation->lawyer_id) {
            return response()->json([
                'message' => 'لا يمكن إكمال الاستشارة بدون محامي محدد',
            ], 400);
        }

        $consultation->status = 'completed';
        $consultation->save();

        return response()->json([
            'message' => 'تم إكمال الاستشارة بنجاح',
            'consultation' => $consultation->load(['lawyer', 'specialization']),
        ]);
    }

    // إنشاء تقييم للاستشارة
    public function createReview(CreateReviewRequest $request, $consultationId)
    {
        $consultation = Consultation::where('client_id', Auth::id())
            ->where('status', 'completed')
            ->findOrFail($consultationId);

        // التحقق من عدم وجود تقييم سابق
        if ($consultation->review) {
            return response()->json(['message' => 'تم التقييم مسبقاً'], 400);
        }

        $review = ConsultationReview::create([
            'consultation_id' => $consultation->id,
            'client_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'تم إنشاء التقييم بنجاح',
            'review' => $review,
        ], 201);
    }
}

