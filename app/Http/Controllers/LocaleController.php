<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    public function set(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, ['en', 'tr'])) {
            $locale = config('app.fallback_locale');
        }

        session(['locale' => $locale]);
        App::setLocale($locale);

        return redirect()->back();
    }
}
