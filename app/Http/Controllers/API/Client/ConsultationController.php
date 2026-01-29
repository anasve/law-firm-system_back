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
use App\Notifications\Consultation\NewMessageNotification;
use App\Notifications\Consultation\NewConsultationNotification;
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

        // إضافة meeting_link إذا كان preferred_channel هو meeting_link
        if ($request->preferred_channel === 'meeting_link' && $request->has('meeting_link')) {
            $data['meeting_link'] = $request->meeting_link;
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

        // إرسال إشعار للمحامي إذا تم تعيينه
        if ($consultation->lawyer_id) {
            $consultation->lawyer->notify(new NewConsultationNotification($consultation));
        } else if ($consultation->specialization_id) {
            // إذا لم يتم تعيين محامي، أرسل إشعار لجميع المحامين المتخصصين في هذا التخصص
            $lawyers = \App\Models\Lawyer::whereHas('specializations', function ($q) use ($consultation) {
                $q->where('specializations.id', $consultation->specialization_id);
            })->get();
            
            foreach ($lawyers as $lawyer) {
                $lawyer->notify(new NewConsultationNotification($consultation));
            }
        }

        return response()->json([
            'message' => 'Consultation created successfully.',
            'consultation' => $consultation->load(['lawyer', 'specialization', 'attachments']),
        ], 201);
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
            'message' => 'Message sent successfully.',
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

        return response()->json(['message' => 'Consultation cancelled successfully.']);
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
                'message' => 'Cannot complete consultation without an assigned lawyer.',
            ], 400);
        }

        $consultation->status = 'completed';
        $consultation->save();

        return response()->json([
            'message' => 'Consultation completed successfully.',
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
            return response()->json(['message' => 'Already reviewed.'], 400);
        }

        $review = ConsultationReview::create([
            'consultation_id' => $consultation->id,
            'client_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Review created successfully.',
            'review' => $review,
        ], 201);
    }
}

