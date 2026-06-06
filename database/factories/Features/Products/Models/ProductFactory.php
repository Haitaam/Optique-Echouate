<?php

namespace Database\Factories\Features\Products\Models;

use App\Features\Products\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 100, 3000),
            'stock' => fake()->numberBetween(0, 50),
            'min_stock_threshold' => 3,
            'brand' => fake()->randomElement(['Ray-Ban', 'Oakley', 'Gucci']),
            'color' => fake()->randomElement(['Black', 'Gold', 'Silver']),
            'frame_shape' => fake()->randomElement(['Round', 'Square', 'Aviator']),
            'gender' => fake()->randomElement(['Men', 'Women', 'Unisex']),
            'material' => fake()->randomElement(['Metal', 'Acetate', 'Titanium']),
            'is_featured' => false,
            'is_luxury' => false,
        ];
    }
}
