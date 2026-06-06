<?php

namespace App\Http\Controllers\Admin;

use App\Models\Gender;
use Illuminate\Http\Request;

class GenderController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $genders = Gender::latest()->paginate(20);

        return view('admin.genders.index', compact('genders'));
    }

    public function create()
    {
        return view('admin.genders.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:genders,slug',
            'description' => 'nullable|string',
        ]);

        Gender::create($data);

        return redirect()->route('admin.genders.index')
            ->with('success', 'Genre créé avec succès.');
    }

    public function edit(Gender $gender)
    {
        return view('admin.genders.edit', compact('gender'));
    }

    public function update(Request $request, Gender $gender)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:genders,slug,' . $gender->id,
            'description' => 'nullable|string',
        ]);

        $gender->update($data);

        return redirect()->route('admin.genders.index')
            ->with('success', 'Genre mis à jour avec succès.');
    }

    public function destroy(Gender $gender)
    {
        $gender->delete();

        return redirect()->route('admin.genders.index')
            ->with('success', 'Genre supprimé avec succès.');
    }
}
