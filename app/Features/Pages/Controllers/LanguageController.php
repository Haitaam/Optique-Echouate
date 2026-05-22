<?php

namespace App\Features\Pages\Controllers;

class LanguageController extends \App\Http\Controllers\Controller
{
    public function switch(string $locale)
    {
        if (!in_array($locale, ['fr', 'ar', 'en'])) {
            $locale = 'fr';
        }

        session(['locale' => $locale]);
        app()->setLocale($locale);

        return back();
    }
}
