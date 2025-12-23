<?php

namespace App\Livewire;

use Illuminate\Support\Facades\App;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Native\Mobile\Facades\Dialog;
use Native\Mobile\Facades\SecureStorage;

#[Layout('layouts.app')]
class Settings extends Component
{
    public bool $compactView = false;

    public array $languages = [];

    public string $locale = 'en';

    public array $timezones = [];

    public string $timezone = 'UTC';

    public function mount(): void
    {
        $this->languages = config('languages.supported', ['en' => 'English']);
        $this->timezones = \DateTimeZone::listIdentifiers();

        try {
            $this->compactView = SecureStorage::get('compact_view') === 'true';
        } catch (\Exception $e) {
            $this->compactView = false;
        }

        $this->locale = $this->resolveLocale();
        $this->timezone = $this->resolveTimezone();
    }

    public function toggleCompactView(): void
    {
        $this->compactView = ! $this->compactView;

        try {
            SecureStorage::set('compact_view', $this->compactView ? 'true' : 'false');
        } catch (\Exception $e) {
        }

        Dialog::toast($this->compactView ? __('app.toasts.compact_enabled') : __('app.toasts.compact_disabled'));
    }

    public function updatedLocale(string $value): void
    {
        if (! array_key_exists($value, $this->languages)) {
            return;
        }

        $this->locale = $value;

        try {
            SecureStorage::set('locale', $value);
        } catch (\Exception $e) {
        }

        App::setLocale($value);

        Dialog::toast(__('app.settings.language_updated'));

        $this->redirect(route('settings'), navigate: true);
    }

    public function updatedTimezone(string $value): void
    {
        if (! in_array($value, $this->timezones, true)) {
            return;
        }

        $this->timezone = $value;

        try {
            SecureStorage::set('timezone', $value);
        } catch (\Exception $e) {
        }

        config(['app.timezone' => $value]);
        date_default_timezone_set($value);

        Dialog::toast(__('app.settings.timezone_updated'));

        $this->redirect(route('settings'), navigate: true);
    }

    protected function resolveLocale(): string
    {
        try {
            $storedLocale = SecureStorage::get('locale');
        } catch (\Exception $e) {
            $storedLocale = null;
        }

        $locale = $storedLocale ?: App::getLocale();

        if (! array_key_exists($locale, $this->languages)) {
            $locale = App::getLocale();
        }

        App::setLocale($locale);

        return $locale;
    }

    protected function resolveTimezone(): string
    {
        try {
            $storedTimezone = SecureStorage::get('timezone');
        } catch (\Exception $e) {
            $storedTimezone = null;
        }

        $timezone = $storedTimezone ?: config('app.timezone');

        if (! in_array($timezone, $this->timezones, true)) {
            $timezone = config('app.timezone');
        }

        config(['app.timezone' => $timezone]);
        date_default_timezone_set($timezone);

        return $timezone;
    }

    public function render(): View
    {
        return view('livewire.settings', [
            'title' => __('app.titles.settings'),
            'languages' => $this->languages,
            'timezones' => $this->timezones,
        ]);
    }
}
