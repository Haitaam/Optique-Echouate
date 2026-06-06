<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::latest()->paginate(20);
        return view('admin.notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification)
    {
        $notification->update(['read' => true]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => Notification::unread()->count(),
            ]);
        }

        return back();
    }

    public function readAll()
    {
        Notification::unread()->update(['read' => true]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return back();
    }

    public function json()
    {
        $notifications = Notification::latest()->take(20)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'body' => $n->body,
                'url' => $n->url ?? route('admin.notifications.index'),
                'read' => (bool) $n->read,
                'created_at' => $n->created_at->diffForHumans(),
                'created_at_raw' => $n->created_at->format('d/m/Y H:i'),
            ];
        });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => Notification::unread()->count(),
        ]);
    }
}
