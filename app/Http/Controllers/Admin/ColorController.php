<?php

namespace App\Http\Controllers\Admin;

use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $colors = Color::latest()->paginate(20);

        return view('admin.colors.index', compact('colors'));
    }

    public function create()
    {
        return view('admin.colors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:colors,slug',
            'hex_value' => 'nullable|string|max:7',
            'description' => 'nullable|string',
        ]);

        Color::create($data);

        return redirect()->route('admin.colors.index')
            ->with('success', 'Couleur créée avec succès.');
    }

    public function edit(Color $color)
    {
        return view('admin.colors.edit', compact('color'));
    }

    public function update(Request $request, Color $color)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:colors,slug,' . $color->id,
            'hex_value' => 'nullable|string|max:7',
            'description' => 'nullable|string',
        ]);

        $color->update($data);

        return redirect()->route('admin.colors.index')
            ->with('success', 'Couleur mise à jour avec succès.');
    }

    public function destroy(Color $color)
    {
        $color->delete();

        return redirect()->route('admin.colors.index')
            ->with('success', 'Couleur supprimée avec succès.');
    }
}
