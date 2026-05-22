<?php

namespace App\Features\Pages\Controllers;

use App\Features\Products\Models\Product;

class PageController extends \App\Http\Controllers\Controller
{
    public function home()
    {
        $latest = Product::with('categories')
            ->orderBy('created_at', 'desc')
            ->get()
            ->filter(fn($p) => $p->imageExists());

        $featured = $latest->take(6);
        $heroProducts = $latest->take(5);

        return view('pages.home', compact('featured', 'heroProducts'));
    }

    public function eyeHealth()
    {
        return view('pages.eye-health');
    }
}
