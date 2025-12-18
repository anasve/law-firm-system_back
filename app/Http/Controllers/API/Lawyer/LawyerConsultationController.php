<?php

namespace App\Http\Controllers\API\Lawyer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lawyer\AcceptConsultationRequest;
use App\Http\Requests\Lawyer\RejectConsultationRequest;
use App\Http\Requests\Lawyer\SendMessageRequest;
use App\Http\Requests\Lawyer\CreateAppointmentRequest;
use App\Models\Consultation;
use App\Models\ConsultationMessage;
use App\Models\Appointment;
use App\Notifications\Consultation\ConsultationAcceptedNotification;
use App\Notifications\Consultation\ConsultationRejectedNotification;
use App\Notifications\Consultation\NewMessageNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LawyerConsultationController extends Controller
{
    // عرض جميع الاستشارات الواردة للمحامي
    public function index(Request $request)
    {
        $query = Consultation::with(['client', 'specialization'])
            ->where('lawyer_id', Auth::id());

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $consultations = $query->orderBy('created_at', 'desc')->get();

        return response()->json($consultations);
    }

    // عرض الاستشارات المعلقة (لم يتم قبولها أو رفضها بعد)
    public function pending()
    {
        $consultations = Consultation::with(['client', 'specialization'])
            ->where('lawyer_id', Auth::id())
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($consultations);
    }

    // عرض استشارة محددة
    public function show($id)
    {
        $consultation = Consultation::with([
            'client',
            'specialization',
            'attachments',
            'messages' => function ($query) {
                $query->orderBy('created_at', 'asc');
            },
            'appointments',
            'review'
        ])->where('lawyer_id', Auth::id())->findOrFail($id);

        return response()->json($consultation);
    }

    // قبول الاستشارة
    public function accept(AcceptConsultationRequest $request, $id)
    {
        $consultation = Consultation::where('lawyer_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $consultation->status = 'accepted';
        $consultation->save();

        // إرسال إشعار للعميل
        $consultation->client->notify(new ConsultationAcceptedNotification($consultation));

        return response()->json([
            'message' => 'تم قبول الاستشارة بنجاح',
            'consultation' => $consultation->load(['client', 'specialization']),
        ]);
    }

    // رفض الاستشارة
    public function reject(RejectConsultationRequest $request, $id)
    {
        $consultation = Consultation::where('lawyer_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        $consultation->status = 'rejected';
        $consultation->rejection_reason = $request->rejection_reason;
        $consultation->save();

        // إرسال إشعار للعميل
        $consultation->client->notify(new ConsultationRejectedNotification($consultation));

        return response()->json([
            'message' => 'تم رفض الاستشارة',
            'consultation' => $consultation->load(['client', 'specialization']),
        ]);
    }

    // إرسال رسالة في المحادثة
    public function sendMessage(SendMessageRequest $request, $consultationId)
    {
        $consultation = Consultation::where('lawyer_id', Auth::id())
            ->where('status', 'accepted')
            ->findOrFail($consultationId);

        $data = [
            'consultation_id' => $consultation->id,
            'sender_type' => 'lawyer',
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

        // إرسال إشعار للعميل
        $consultation->client->notify(new NewMessageNotification($message));

        return response()->json([
            'message' => 'تم إرسال الرسالة بنجاح',
            'message_data' => $message,
        ], 201);
    }

    // عرض رسائل الاستشارة
    public function getMessages($consultationId)
    {
        $consultation = Consultation::where('lawyer_id', Auth::id())
            ->findOrFail($consultationId);

        $messages = ConsultationMessage::where('consultation_id', $consultationId)
            ->with('sender')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // إنشاء موعد
    public function createAppointment(CreateAppointmentRequest $request, $consultationId)
    {
        $consultation = Consultation::where('lawyer_id', Auth::id())
            ->where('status', 'accepted')
            ->findOrFail($consultationId);

        $appointment = Appointment::create([
            'consultation_id' => $consultation->id,
            'lawyer_id' => Auth::id(),
            'client_id' => $consultation->client_id,
            'datetime' => $request->datetime,
            'type' => 'in_office',
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        // إرسال إشعار للعميل
        // TODO: إضافة Notification

        return response()->json([
            'message' => 'تم إنشاء الموعد بنجاح',
            'appointment' => $appointment,
        ], 201);
    }

    // إنهاء الاستشارة
    public function complete(Request $request, $id)
    {
        $consultation = Consultation::where('lawyer_id', Auth::id())
            ->where('status', 'accepted')
            ->findOrFail($id);

        $consultation->status = 'completed';
        if ($request->has('legal_summary')) {
            $consultation->legal_summary = $request->legal_summary;
        }
        $consultation->save();

        return response()->json([
            'message' => 'تم إنهاء الاستشارة بنجاح',
            'consultation' => $consultation,
        ]);
    }

    // تحديث حالة الرسالة كمقروءة
    public function markMessageAsRead($consultationId, $messageId)
    {
        $consultation = Consultation::where('lawyer_id', Auth::id())
            ->findOrFail($consultationId);

        $message = ConsultationMessage::where('consultation_id', $consultationId)
            ->where('id', $messageId)
            ->where('sender_type', 'client')
            ->findOrFail($messageId);

        $message->is_read = true;
        $message->read_at = now();
        $message->save();

        return response()->json(['message' => 'تم تحديث حالة الرسالة']);
    }
}

