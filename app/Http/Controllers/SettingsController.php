<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Inertia\Inertia;
use Inertia\Response;
use Native\Mobile\Facades\Dialog;
use Native\Mobile\Facades\SecureStorage;

class SettingsController extends Controller
{
    public function show(): Response
    {
        $languages = config('languages.supported', ['en' => 'English']);
        $timezones = \DateTimeZone::listIdentifiers();

        return Inertia::render('Settings', [
            'compactView' => $this->resolveCompactView(),
            'languages' => $languages,
            'timezones' => $timezones,
            'locale' => $this->resolveLocale($languages),
            'timezone' => $this->resolveTimezone($timezones),
            'title' => __('app.titles.settings'),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (array_key_exists('compact_view', $data)) {
            $compactView = (bool) $data['compact_view'];

            try {
                SecureStorage::set('compact_view', $compactView ? 'true' : 'false');
            } catch (\Exception $e) {
            }

            Dialog::toast($compactView ? __('app.toasts.compact_enabled') : __('app.toasts.compact_disabled'));
        }

        if (array_key_exists('locale', $data)) {
            $locale = $data['locale'];

            try {
                SecureStorage::set('locale', $locale);
            } catch (\Exception $e) {
            }

            App::setLocale($locale);

            Dialog::toast(__('app.settings.language_updated'));
        }

        if (array_key_exists('timezone', $data)) {
            $timezone = $data['timezone'];

            try {
                SecureStorage::set('timezone', $timezone);
            } catch (\Exception $e) {
            }

            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);

            Dialog::toast(__('app.settings.timezone_updated'));
        }

        return redirect()->route('settings');
    }

    /**
     * @param  array<string, string>  $languages
     */
    protected function resolveLocale(array $languages): string
    {
        try {
            $storedLocale = SecureStorage::get('locale');
        } catch (\Exception $e) {
            $storedLocale = null;
        }

        $locale = $storedLocale ?: App::getLocale();

        if (! array_key_exists($locale, $languages)) {
            $locale = App::getLocale();
        }

        App::setLocale($locale);

        return $locale;
    }

    /**
     * @param  array<int, string>  $timezones
     */
    protected function resolveTimezone(array $timezones): string
    {
        try {
            $storedTimezone = SecureStorage::get('timezone');
        } catch (\Exception $e) {
            $storedTimezone = null;
        }

        $timezone = $storedTimezone ?: config('app.timezone');

        if (! in_array($timezone, $timezones, true)) {
            $timezone = config('app.timezone');
        }

        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);

        return $timezone;
    }

    protected function resolveCompactView(): bool
    {
        try {
            $storedCompactView = SecureStorage::get('compact_view');
        } catch (\Exception $e) {
            $storedCompactView = null;
        }

        return $storedCompactView === 'true';
    }
}
