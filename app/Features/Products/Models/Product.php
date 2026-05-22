<?php

namespace App\Features\Products\Models;

use App\Features\Categories\Models\Category;
use App\Shared\Services\FaceCompatibilityService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'image',
        'brand', 'color', 'frame_shape', 'gender', 'material',
        'is_featured', 'is_luxury', 'style_tags', 'secondary_color',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_luxury' => 'boolean',
            'style_tags' => 'array',
        ];
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['brand'] ?? null, fn($q, $v) => $q->where('brand', $v))
            ->when($filters['color'] ?? null, fn($q, $v) => $q->where('color', $v))
            ->when($filters['frame_shape'] ?? null, fn($q, $v) => $q->where('frame_shape', $v))
            ->when($filters['gender'] ?? null, fn($q, $v) => $q->where('gender', $v))
            ->when($filters['material'] ?? null, fn($q, $v) => $q->where('material', $v))
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
