<?php

namespace App\Features\Pages\Controllers;

use App\Features\Products\Models\Product;
use App\Models\Testimonial;

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

    public function storeAvis(\Illuminate\Http\Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'text' => 'required|string|max:1000',
            'work' => 'nullable|string|max:255',
        ]);

        $words = explode(' ', $data['name']);
        $initials = collect($words)->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('');

        Testimonial::create([
            'name' => $data['name'],
            'role' => $data['work'] ?? null,
            'text' => $data['text'],
            'avatar_initials' => $initials,
            'source' => 'visitor',
            'is_active' => true,
        ]);

        return redirect()->route('home')->with('success', 'Merci pour votre avis ! Il est maintenant visible sur la page.');
    }

    public function eyeHealth()
    {
        return view('pages.eye-health');
    }
}
