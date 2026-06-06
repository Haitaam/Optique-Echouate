<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shape;
use Illuminate\Http\Request;

class ShapeController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $shapes = Shape::latest()->paginate(20);

        return view('admin.shapes.index', compact('shapes'));
    }

    public function create()
    {
        return view('admin.shapes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shapes,slug',
            'description' => 'nullable|string',
        ]);

        Shape::create($data);

        return redirect()->route('admin.shapes.index')
            ->with('success', 'Forme créée avec succès.');
    }

    public function edit(Shape $shape)
    {
        return view('admin.shapes.edit', compact('shape'));
    }

    public function update(Request $request, Shape $shape)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:shapes,slug,' . $shape->id,
            'description' => 'nullable|string',
        ]);

        $shape->update($data);

        return redirect()->route('admin.shapes.index')
            ->with('success', 'Forme mise à jour avec succès.');
    }

    public function destroy(Shape $shape)
    {
        $shape->delete();

        return redirect()->route('admin.shapes.index')
            ->with('success', 'Forme supprimée avec succès.');
    }
}
