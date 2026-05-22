<?php

namespace App\Features\Products\Controllers;

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

        if (request()->wantsJson()) {
            return response()->json([
                'html' => view('partials.product-grid', compact('products'))->render(),
            ]);
        }

        return view('pages.products', compact('products', 'brands', 'colors', 'shapes', 'genders', 'materials'));
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
}
