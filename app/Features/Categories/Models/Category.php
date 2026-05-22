<?php

namespace App\Features\Categories\Models;

use App\Features\Products\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
