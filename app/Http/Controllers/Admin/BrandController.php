<?php

namespace App\Http\Controllers\Admin;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $brands = Brand::latest()->paginate(20);

        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug',
            'description' => 'nullable|string',
        ]);

        Brand::create($data);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marque créée avec succès.');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:brands,slug,' . $brand->id,
            'description' => 'nullable|string',
        ]);

        $brand->update($data);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marque mise à jour avec succès.');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Marque supprimée avec succès.');
    }
}
