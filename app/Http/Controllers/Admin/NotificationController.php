<?php

namespace App\Http\Controllers\Admin;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends \App\Http\Controllers\Controller
{
    public function __construct(
        private NotificationService $notificationService,
    ) {}

    public function index()
    {
        $notifications = Notification::latest()->paginate(20);

        return view('admin.notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        $this->notificationService->markRead($notification);

        return response()->json(['success' => true]);
    }

    public function readAll()
    {
        $this->notificationService->markAllRead();

        return redirect()->back()->with('success', 'Toutes les notifications marquées comme lues.');
    }

    public function json()
    {
        $notifications = $this->notificationService->recent(10);
        $unreadCount = $this->notificationService->unreadCount();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }
}
