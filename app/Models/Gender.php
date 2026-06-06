<?php

namespace App\Models;

use App\Features\Products\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gender extends Model
{
    protected $fillable = [
        'name', 'slug',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
