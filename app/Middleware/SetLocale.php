<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Native\Mobile\Facades\SecureStorage;

class SetLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
        $locale = $this->resolveLocale($request);
        if ($locale !== null) {
            App::setLocale($locale);
        }

        return $next($request);
    }

    protected function resolveLocale(Request $request): ?string
    {
        $supportedLocales = array_keys(config('languages.supported', ['en' => 'English']));

        try {
            $storedLocale = SecureStorage::get('locale');
        } catch (\Exception $e) {
            $storedLocale = null;
        }

        if ($storedLocale && in_array($storedLocale, $supportedLocales, true)) {
            return $storedLocale;
        }

        return null;
    }
}
