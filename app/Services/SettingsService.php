<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public function get(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }

    public function set(string $key, $value): void
    {
        Setting::set($key, $value);
        Cache::forget('settings');
    }

    public function all(): array
    {
        return Cache::remember('settings', 3600, function () {
            return Setting::all()->pluck('value', 'key')->toArray();
        });
    }

    public function updateFromRequest(array $data): void
    {
        foreach ($data as $key => $value) {
            if ($key !== '_token' && $key !== 'favicon') {
                $this->set($key, $value);
            }
        }

        Cache::forget('settings');
    }
}
