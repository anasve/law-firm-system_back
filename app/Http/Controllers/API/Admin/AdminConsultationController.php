<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;

class AdminConsultationController extends Controller
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

