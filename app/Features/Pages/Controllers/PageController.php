<?php

namespace App\Features\Pages\Controllers;

use App\Features\Products\Models\Product;
use App\Models\Notification;
use App\Models\Testimonial;

class PageController extends \App\Http\Controllers\Controller
{
    public function home()
    {
        $products = Product::with('categories')
            ->orderBy('created_at', 'desc')
            ->get();

        $withImages = $products->filter(fn($p) => $p->imageExists());

        $featured = $withImages->take(6);
        $heroProducts = $withImages->take(5);

        return view('pages.home', compact('featured', 'heroProducts') + ['gmapsUrl' => 'https://maps.google.com/?q=Optique+Échouate+Maroc']);
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

        $testimonial = Testimonial::create([
            'name' => $data['name'],
            'role' => $data['work'] ?? null,
            'text' => $data['text'],
            'avatar_initials' => $initials,
            'source' => 'visitor',
            'is_active' => false,
        ]);

        Notification::create([
            'type' => 'new_review',
            'title' => 'Nouvel avis client',
            'body' => $data['name'] . ' — ' . mb_substr($data['text'], 0, 80) . (mb_strlen($data['text']) > 80 ? '…' : ''),
            'url' => route('admin.testimonials.index'),
            'read' => false,
        ]);

        return redirect()->route('home')->with('success', 'Merci pour votre avis ! Il sera visible après modération.');
    }

    public function eyeHealth()
    {
        return view('pages.eye-health');
    }
}
