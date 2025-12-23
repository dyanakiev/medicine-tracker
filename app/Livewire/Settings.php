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

    public array $currencies = [];

    public string $currency = 'EUR';

    public function mount(): void
    {
        $this->languages = config('languages.supported', ['en' => 'English']);
        $this->currencies = config('currencies.supported', ['EUR' => '€']);

        try {
            $this->compactView = SecureStorage::get('compact_view') === 'true';
        } catch (\Exception $e) {
            $this->compactView = false;
        }

        $this->locale = $this->resolveLocale();
        $this->currency = $this->resolveCurrency();
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

    public function updatedCurrency(string $value): void
    {
        if (! array_key_exists($value, $this->currencies)) {
            return;
        }

        $this->currency = $value;

        try {
            SecureStorage::set('currency', $value);
        } catch (\Exception $e) {
        }

        Dialog::toast(__('app.settings.currency_updated'));

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

    protected function resolveCurrency(): string
    {
        try {
            $storedCurrency = SecureStorage::get('currency');
        } catch (\Exception $e) {
            $storedCurrency = null;
        }

        $currency = $storedCurrency ?: array_key_first($this->currencies);

        if (! array_key_exists($currency, $this->currencies)) {
            $currency = array_key_first($this->currencies);
        }

        return $currency;
    }

    public function render(): View
    {
        return view('livewire.settings', [
            'title' => __('app.titles.settings'),
            'languages' => $this->languages,
            'currencies' => $this->currencies,
        ]);
    }
}
