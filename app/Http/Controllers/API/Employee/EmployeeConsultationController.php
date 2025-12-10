<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignConsultationRequest;
use App\Models\Consultation;
use App\Models\Lawyer;
use App\Notifications\Consultation\NewConsultationNotification;
use Illuminate\Http\Request;

class EmployeeConsultationController extends Controller
{
    // عرض جميع الاستشارات
    public function index(Request $request)
    {
        $query = Consultation::with(['client', 'lawyer', 'specialization']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($lawyerId = $request->input('lawyer_id')) {
            $query->where('lawyer_id', $lawyerId);
        }

        if ($clientId = $request->input('client_id')) {
            $query->where('client_id', $clientId);
        }

        $consultations = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($consultations);
    }

    // عرض الاستشارات المعلقة (بدون محامي)
    public function pending()
    {
        $consultations = Consultation::with(['client', 'specialization'])
            ->whereNull('lawyer_id')
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
            'lawyer',
            'specialization',
            'attachments',
            'messages',
            'appointments',
            'review'
        ])->findOrFail($id);

        return response()->json($consultation);
    }

    // إسناد استشارة لمحامي
    public function assign(AssignConsultationRequest $request, $id)
    {
        $consultation = Consultation::whereNull('lawyer_id')
            ->where('status', 'pending')
            ->findOrFail($id);

        $lawyer = Lawyer::findOrFail($request->lawyer_id);

        $consultation->lawyer_id = $lawyer->id;
        $consultation->save();
        $consultation->load('client');

        // إرسال إشعار للمحامي
        $lawyer->notify(new NewConsultationNotification($consultation));

        return response()->json([
            'message' => 'تم إسناد الاستشارة للمحامي بنجاح',
            'consultation' => $consultation->load(['client', 'lawyer', 'specialization']),
        ]);
    }

    // إسناد تلقائي حسب التخصص
    public function autoAssign($id)
    {
        $consultation = Consultation::whereNull('lawyer_id')
            ->where('status', 'pending')
            ->findOrFail($id);

        if (!$consultation->specialization_id) {
            return response()->json([
                'message' => 'لا يمكن الإسناد التلقائي بدون تخصص',
            ], 400);
        }

        $lawyer = Lawyer::whereHas('specializations', function ($q) use ($consultation) {
            $q->where('specializations.id', $consultation->specialization_id);
        })->first();

        if (!$lawyer) {
            return response()->json([
                'message' => 'لا يوجد محامي متاح لهذا التخصص',
            ], 404);
        }

        $consultation->lawyer_id = $lawyer->id;
        $consultation->save();
        $consultation->load('client');

        // إرسال إشعار للمحامي
        $lawyer->notify(new NewConsultationNotification($consultation));

        return response()->json([
            'message' => 'تم الإسناد التلقائي بنجاح',
            'consultation' => $consultation->load(['client', 'lawyer', 'specialization']),
        ]);
    }

    // إحصائيات الاستشارات
    public function statistics()
    {
        $stats = [
            'total' => Consultation::count(),
            'pending' => Consultation::where('status', 'pending')->count(),
            'accepted' => Consultation::where('status', 'accepted')->count(),
            'completed' => Consultation::where('status', 'completed')->count(),
            'rejected' => Consultation::where('status', 'rejected')->count(),
            'unassigned' => Consultation::whereNull('lawyer_id')->where('status', 'pending')->count(),
        ];

        return response()->json($stats);
    }
}

