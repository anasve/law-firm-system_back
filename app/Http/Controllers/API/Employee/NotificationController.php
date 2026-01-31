<?php

namespace App\Http\Controllers\API\Employee;

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

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 15));

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
        $unreadCount = Auth::user()->unreadNotifications()->count();

        return response()->json([
            'message' => 'Notification marked as read.',
            'notification' => $notification,
            'unread_count' => $unreadCount,
        ]);
    }

    // تحديد جميع الإشعارات كمقروءة
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $unreadCount = Auth::user()->unreadNotifications()->count();

        return response()->json([
            'message' => 'All notifications marked as read.',
            'unread_count' => $unreadCount,
        ]);
    }

    // حذف إشعار
    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return response()->json([
            'message' => 'Notification deleted.',
        ]);
    }
}

