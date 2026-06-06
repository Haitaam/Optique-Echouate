<?php

namespace App\Http\Controllers\Admin;

use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingController extends \App\Http\Controllers\Controller
{
    public function __construct(
        private SettingsService $settingsService,
    ) {}

    public function index()
    {
        $settings = $this->settingsService->all();

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->settingsService->updateFromRequest($request->except(['_token', 'favicon']));

        if ($request->hasFile('favicon')) {
            $path = $request->file('favicon')->store('favicon', 'public');
            $this->settingsService->set('favicon', '/storage/' . $path);
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Paramètres mis à jour avec succès.');
    }
}
