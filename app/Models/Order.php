<?php

namespace App\Models;

use App\Features\Products\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    const STATUS_PENDING = 'pending_confirmation';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PREPARING = 'preparing';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_PREPARING,
        self::STATUS_SHIPPED,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
    ];

    const STATUS_LABELS = [
        self::STATUS_PENDING => 'En attente de confirmation',
        self::STATUS_CONFIRMED => 'Confirmée',
        self::STATUS_PREPARING => 'En préparation',
        self::STATUS_SHIPPED => 'Expédiée',
        self::STATUS_DELIVERED => 'Livrée',
        self::STATUS_CANCELLED => 'Annulée',
    ];

    const STATUS_BADGES = [
        self::STATUS_PENDING => 'bg-amber-500/10 text-amber-300 border-amber-500/20',
        self::STATUS_CONFIRMED => 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20',
        self::STATUS_PREPARING => 'bg-blue-500/10 text-blue-300 border-blue-500/20',
        self::STATUS_SHIPPED => 'bg-purple-500/10 text-purple-300 border-purple-500/20',
        self::STATUS_DELIVERED => 'bg-green-500/10 text-green-300 border-green-500/20',
        self::STATUS_CANCELLED => 'bg-red-500/10 text-red-300 border-red-500/20',
    ];

    const STATUS_ICONS = [
        self::STATUS_PENDING => '🟡',
        self::STATUS_CONFIRMED => '🟢',
        self::STATUS_PREPARING => '🔵',
        self::STATUS_SHIPPED => '🟣',
        self::STATUS_DELIVERED => '✅',
        self::STATUS_CANCELLED => '🔴',
    ];

    protected $fillable = [
        'session_id',
        'customer_id',
        'name',
        'phone',
        'email',
        'address',
        'city',
        'notes',
        'status',
        'total_price',
        'payment_method',
        'payment_status',
        'items',
        'type',
        'whatsapp_sent',
        'return_reason',
        'prescription_path',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'items' => 'array',
            'whatsapp_sent' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopePendingConfirmation($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeByStatus($query, ?string $status)
    {
        if ($status && in_array($status, self::STATUSES)) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeSearch($query, ?string $term)
    {
        if (!$term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('id', (int) preg_replace('/[^0-9]/', '', $term))
              ->orWhere('name', 'like', "%{$term}%")
              ->orWhere('phone', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }

    public function scopeDateRange($query, ?string $start, ?string $end)
    {
        if ($start) $query->whereDate('created_at', '>=', $start);
        if ($end) $query->whereDate('created_at', '<=', $end);
        return $query;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getBadgeClassAttribute(): string
    {
        return self::STATUS_BADGES[$this->status] ?? 'bg-gray-500/10 text-gray-300 border-gray-500/20';
    }

    public function getStatusIconAttribute(): string
    {
        return self::STATUS_ICONS[$this->status] ?? '⚪';
    }

    public function getProfitAttribute(): string
    {
        return number_format((float) $this->total_price, 2, ',', ' ');
    }

    public function canTransitionTo(?string $newStatus): bool
    {
        $flow = [
            self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
            self::STATUS_CONFIRMED => [self::STATUS_PREPARING, self::STATUS_CANCELLED],
            self::STATUS_PREPARING => [self::STATUS_SHIPPED, self::STATUS_CANCELLED],
            self::STATUS_SHIPPED => [self::STATUS_DELIVERED, self::STATUS_CANCELLED],
            self::STATUS_DELIVERED => [],
            self::STATUS_CANCELLED => [],
        ];

        return in_array($newStatus, $flow[$this->status] ?? []);
    }

    public function recalculateTotal(): void
    {
        $this->load('orderItems');
        $this->total_price = $this->orderItems->sum(fn($item) => $item->price * $item->quantity);
        $this->saveQuietly();
    }

    public function syncItemsFromOrderItems(): void
    {
        $this->load('orderItems.product');
        $items = $this->orderItems->map(fn($oi) => [
            'product_id' => $oi->product_id,
            'name' => $oi->product?->name ?? '#' . $oi->product_id,
            'price' => (float) $oi->price,
            'quantity' => $oi->quantity,
            'image' => $oi->product?->image,
        ])->toArray();

        $this->items = $items;
        $this->saveQuietly();
    }
}
