<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{

    use ApiResponse;

    public function index()
    {
        $notifications = Auth::user()->notifications;

        // تحويل الإشعارات إلى Resource Collection
        $notificationResources = NotificationResource::collection($notifications);

        return $this->successResponse($notificationResources, 'Notifications retrieved successfully.');
    }



    public function markAsRead($id)
    {
        // استدعاء كائن الإشعار باستخدام نموذج إشعارات Laravel الافتراضي
        $notification = Auth::user()->notifications()->findOrFail($id);
        if ($notification) {
            $notification->markAsRead();

            return $this->successResponse(null, 'Notification marked as read.');

        } else {
            return $this->successResponse(null, 'error.');
        }
    }

    public function markAllAsRead()
    {
        $notifications = Auth::user()->unreadNotifications;
        foreach ($notifications as $notification) {
            $notification->markAsRead();
        }

        return $this->successResponse(null, 'All notifications marked as read.');
    }



    public function readNotifications()
    {
        $notifications = Auth::user()->readNotifications;

        // تحويل الإشعارات إلى Resource Collection
        $notificationResources = NotificationResource::collection($notifications);

        return $this->successResponse($notificationResources, 'Read notifications retrieved successfully.');
    }


    public function unreadNotifications()
    {
        $notifications = Auth::user()->unreadNotifications;

        // تحويل الإشعارات إلى Resource Collection
        $notificationResources = NotificationResource::collection($notifications);

        return $this->successResponse($notificationResources, 'Unread notifications retrieved successfully.');
    }


    public function deleteNotification($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();

        return $this->successResponse(null, 'Notification deleted successfully.');
    }

    public function deleteAllNotifications()
    {
        Auth::user()->notifications()->delete();

        return $this->successResponse(null, 'All notifications deleted successfully.');
    }
}
