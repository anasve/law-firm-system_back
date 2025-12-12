<?php

namespace App\Http\Controllers\API\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // عرض جميع الإشعارات
    public function index(Request $request)
    {
        $query = Auth::user()->notifications();

        if ($request->has('unread_only') && $request->unread_only) {
            $query->whereNull('read_at');
        }

        // دعم limit و per_page
        $perPage = $request->get('limit', $request->get('per_page', 15));

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json($notifications);
    }

    // عرض الإشعارات غير المقروءة فقط
    public function unread()
    {
        $notifications = Auth::user()->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    // عدد الإشعارات غير المقروءة
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifications()->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    // تحديد إشعار كمقروء
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'message' => 'تم تحديد الإشعار كمقروء',
            'notification' => $notification,
        ]);
    }

    // تحديد جميع الإشعارات كمقروءة
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'تم تحديد جميع الإشعارات كمقروءة',
        ]);
    }

    // حذف إشعار
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json([
            'message' => 'تم حذف الإشعار',
        ]);
    }

    // حذف جميع الإشعارات
    public function destroyAll()
    {
        Auth::user()->notifications()->delete();

        return response()->json([
            'message' => 'تم حذف جميع الإشعارات',
        ]);
    }
}

