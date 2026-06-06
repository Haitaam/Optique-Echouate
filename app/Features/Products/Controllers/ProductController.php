<?php

namespace App\Features\Products\Controllers;

use App\Features\Categories\Models\Category;
use App\Features\Products\Models\Product;

class ProductController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $products = Product::with('categories')->filter(request()->all())->paginate(12)->withQueryString();
        $products->setCollection(
            $products->getCollection()->filter(fn($p) => $p->imageExists())
        );

        $brands = Product::select('brand')->distinct()->pluck('brand');
        $colors = Product::select('color')->distinct()->pluck('color');
        $shapes = Product::select('frame_shape')->distinct()->pluck('frame_shape');
        $genders = Product::select('gender')->distinct()->pluck('gender');
        $materials = Product::select('material')->distinct()->pluck('material');
        $categories = Category::orderBy('name')->get();

        if (request()->wantsJson()) {
            return response()->json([
                'html' => view('partials.product-grid', compact('products'))->render(),
            ]);
        }

        return view('pages.products', compact('products', 'brands', 'colors', 'shapes', 'genders', 'materials', 'categories'));
    }

    public function filter()
    {
        $products = Product::with('categories')->filter(request()->all())->paginate(12)->withQueryString();
        $products->setCollection(
            $products->getCollection()->filter(fn($p) => $p->imageExists())
        );

        return response()->json([
            'html' => view('partials.product-grid', compact('products'))->render(),
            'count' => $products->total(),
        ]);
    }

    public function searchJson()
    {
        $q = request('q');
        $filters = request()->only(['brand', 'color', 'frame_shape', 'gender', 'material', 'category', 'price_min', 'price_max']);

        $query = Product::with('categories');

        if ($q && strlen($q) >= 2) {
            $query->where(function ($qry) use ($q) {
                $qry->where('name', 'like', "%{$q}%")
                    ->orWhere('brand', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('color', 'like', "%{$q}%")
                    ->orWhere('frame_shape', 'like', "%{$q}%")
                    ->orWhere('gender', 'like', "%{$q}%")
                    ->orWhere('material', 'like', "%{$q}%");
            });
        } else {
            $query->where('id', '>', 0);
        }

        $query->when($filters['brand'] ?? null, fn($q, $v) => $q->where('brand', $v))
            ->when($filters['color'] ?? null, fn($q, $v) => $q->where('color', $v))
            ->when($filters['frame_shape'] ?? null, fn($q, $v) => $q->where('frame_shape', $v))
            ->when($filters['gender'] ?? null, fn($q, $v) => $q->where('gender', $v))
            ->when($filters['material'] ?? null, fn($q, $v) => $q->where('material', $v))
            ->when($filters['category'] ?? null, fn($q, $v) => $q->whereHas('categories', fn($q) => $q->where('categories.id', $v)))
            ->when($filters['price_min'] ?? null, fn($q, $v) => $q->where('price', '>=', $v))
            ->when($filters['price_max'] ?? null, fn($q, $v) => $q->where('price', '<=', $v));

        $products = (clone $query)
            ->limit(6)
            ->get()
            ->filter(fn($p) => $p->imageExists())
            ->values()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'brand' => $p->brand,
                'image' => $p->image,
                'price' => $p->price,
                'price_formatted' => number_format($p->price, 2, ',', ' ') . ' MAD',
                'category_names' => $p->categories->pluck('name')->implode(', '),
                'color' => $p->color,
                'shape' => $p->frame_shape,
            ]);

        $matchingBrands = [];
        $matchingCategories = [];
        if ($q && strlen($q) >= 2) {
            $matchingBrands = Product::where('brand', 'like', "%{$q}%")
                ->select('brand')
                ->distinct()
                ->limit(5)
                ->pluck('brand')
                ->values()
                ->map(fn($name) => ['name' => $name, 'count' => Product::where('brand', $name)->count()]);

            $matchingCategories = Category::where('name', 'like', "%{$q}%")
                ->limit(5)
                ->get()
                ->map(fn($c) => ['id' => $c->id, 'name' => $c->name, 'count' => $c->products()->count()]);
        }

        $total = (clone $query)->count();

        return response()->json([
            'products' => $products,
            'brands' => $matchingBrands,
            'categories' => $matchingCategories,
            'total' => $total,
        ]);
    }
}
