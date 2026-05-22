<?php

namespace App\Http\Controllers\Admin;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TestimonialController extends \App\Http\Controllers\Controller
{
    public function index(Request $request)
    {
        $query = Testimonial::orderBy('sort_order')->orderBy('id');

        if ($source = $request->query('source')) {
            $query->where('source', $source);
        }

        $testimonials = $query->get();
        $visitorCount = Testimonial::where('source', 'visitor')->count();
        $adminCount = Testimonial::where('source', 'admin')->count();

        return view('admin.testimonials.index', compact('testimonials', 'visitorCount', 'adminCount'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'text' => 'required|string',
            'avatar_initials' => 'nullable|string|max:4',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if (empty($data['avatar_initials'])) {
            $words = explode(' ', $data['name']);
            $data['avatar_initials'] = collect($words)->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('');
        }

        $data['sort_order'] = $data['sort_order'] ?? Testimonial::max('sort_order') + 1;

        Testimonial::create($data);
        Cache::flush();

        return redirect()->route('admin.testimonials.index')->with('success', 'Témoignage ajouté.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'nullable|string|max:255',
            'text' => 'required|string',
            'avatar_initials' => 'nullable|string|max:4',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (empty($data['avatar_initials'])) {
            $words = explode(' ', $data['name']);
            $data['avatar_initials'] = collect($words)->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('');
        }

        $testimonial->update($data);
        Cache::flush();

        return redirect()->route('admin.testimonials.index')->with('success', 'Témoignage mis à jour.');
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();
        Cache::flush();

        return redirect()->route('admin.testimonials.index')->with('success', 'Témoignage supprimé.');
    }
}
