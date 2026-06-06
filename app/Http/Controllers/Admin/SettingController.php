<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($request->except(['_token', 'favicon']) as $key => $value) {
            Setting::set($key, $value);
        }

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('favicon', 'public');
            Setting::set('favicon', '/storage/' . $path);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Paramètres mis à jour avec succès.');
    }
}
