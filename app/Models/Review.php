<?php

namespace App\Models;

use App\Features\Products\Models\Product;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'product_id',
        'customer_id',
        'order_id',
        'rating',
        'title',
        'body',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'is_approved' => 'boolean',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}
