<?php

namespace Database\Seeders;

use App\Features\Categories\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Optical Collection', 'slug' => 'optical-collection', 'description' => 'Elegant optical frames for daily comfort and timeless style.'],
            ['name' => 'Solar Collection', 'slug' => 'solar-collection', 'description' => 'Stylish sunglasses for protection and bold modern look.'],
            ['name' => 'Clip-On Collection', 'slug' => 'clip-on-collection', 'description' => 'Versatile clip-on eyewear for modern convenience.'],
            ['name' => 'Blue Light Protection', 'slug' => 'blue-light-protection', 'description' => 'Premium lenses to shield your eyes from digital strain.'],
        ])->map(fn($c) => Category::firstOrCreate(['slug' => $c['slug']], $c));

        $this->call(ImageProductSeeder::class);
    }
}
