<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $statusColor;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->statusColor = match ($order->status) {
            Order::STATUS_CONFIRMED => '#22c55e',
            Order::STATUS_PREPARING => '#3b82f6',
            Order::STATUS_SHIPPED => '#a855f7',
            Order::STATUS_DELIVERED => '#16a34a',
            Order::STATUS_CANCELLED => '#ef4444',
            default => '#f97316',
        };
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mise à jour commande #' . $this->order->id . ' — ' . $this->order->status_label,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-status-changed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}