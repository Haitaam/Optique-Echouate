<?php

namespace App\Features\Products\Models;

use App\Features\Categories\Models\Category;
use App\Models\Brand;
use App\Models\Color;
use App\Models\Gender;
use App\Models\Review;
use App\Models\Shape;
use App\Models\Wishlist;
use App\Shared\Services\FaceCompatibilityService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'price', 'cost_price', 'image',
        'brand', 'color', 'frame_shape', 'gender', 'material',
        'brand_id', 'color_id', 'shape_id', 'gender_id',
        'is_featured', 'is_luxury', 'style_tags', 'secondary_color',
        'stock', 'min_stock_threshold',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_luxury' => 'boolean',
            'style_tags' => 'array',
            'stock' => 'integer',
            'min_stock_threshold' => 'integer',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true);
    }

    public function brandModel(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function colorModel(): BelongsTo
    {
        return $this->belongsTo(Color::class, 'color_id');
    }

    public function shapeModel(): BelongsTo
    {
        return $this->belongsTo(Shape::class, 'shape_id');
    }

    public function genderModel(): BelongsTo
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function avgRating(): ?float
    {
        return $this->approvedReviews()->avg('rating');
    }

    public function inStock(): bool
    {
        return $this->stock > 0;
    }

    public function hasLowStock(): bool
    {
        return $this->stock > 0 && $this->stock <= ($this->min_stock_threshold ?: 3);
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['brand'] ?? null, fn($q, $v) => $q->where('brand', $v))
            ->when($filters['color'] ?? null, fn($q, $v) => $q->where('color', $v))
            ->when($filters['frame_shape'] ?? null, fn($q, $v) => $q->where('frame_shape', $v))
            ->when($filters['gender'] ?? null, fn($q, $v) => $q->where('gender', $v))
            ->when($filters['material'] ?? null, fn($q, $v) => $q->where('material', $v))
            ->when($filters['category'] ?? null, fn($q, $v) => $q->whereHas('categories', fn($q) => $q->where('categories.id', $v)))
            ->when($filters['price_min'] ?? null, fn($q, $v) => $q->where('price', '>=', $v))
            ->when($filters['price_max'] ?? null, fn($q, $v) => $q->where('price', '<=', $v))
            ->when($filters['is_luxury'] ?? null, fn($q, $v) => $q->where('is_luxury', true))
            ->when($filters['tag'] ?? null, fn($q, $v) => $q->whereJsonContains('style_tags', $v))
            ->when($filters['search'] ?? null, fn($q, $v) => $q->where(function($q) use ($v) {
                $q->where('name', 'like', "%{$v}%")
                  ->orWhere('brand', 'like', "%{$v}%")
                  ->orWhere('description', 'like', "%{$v}%");
            }));
    }

    public function imageExists(): bool
    {
        return file_exists(public_path(ltrim($this->image, '/')));
    }

    public function faceCompatibility(?string $shape = null): array
    {
        $service = app(FaceCompatibilityService::class);
        return $shape
            ? $service->analyze($this, $shape)
            : $service->analyzeAll($this);
    }
}
