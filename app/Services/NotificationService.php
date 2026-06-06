<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function newOrder(object $order): void
    {
        Notification::create([
            'type' => 'new_order',
            'title' => 'Nouvelle commande #' . $order->id,
            'body' => $order->name . ' — ' . number_format($order->total_price, 2, ',', ' ') . ' MAD',
            'url' => route('admin.orders.show', $order),
            'read' => false,
        ]);
    }

    public function unreadCount(): int
    {
        return Notification::unread()->count();
    }

    public function markRead(Notification $notification): void
    {
        $notification->update(['read' => true]);
    }

    public function markAllRead(): void
    {
        Notification::unread()->update(['read' => true]);
    }

    public function recent(int $limit = 10)
    {
        return Notification::latest()->take($limit)->get();
    }
}
