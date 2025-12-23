<?php

namespace App\Livewire;

use App\Models\Subscription;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Native\Mobile\Facades\Dialog;
use Native\Mobile\Facades\SecureStorage;

#[Layout('layouts.app')]
class SubscriptionForm extends Component
{
    public ?Subscription $subscription = null;

    public string $name = '';

    public string $price = '';

    public ?string $url = null;

    public string $title = '';

    public bool $isActive = true;

    public function mount(?int $id = null): void
    {
        if ($id || request()->has('id')) {
            $subscriptionId = $id ?? (int) request()->query('id');
            $this->loadSubscription($subscriptionId);
        }

        $this->title = $this->subscription
            ? __('app.titles.edit_subscription')
            : __('app.titles.add_subscription');
    }

    protected function loadSubscription(int $id): void
    {
        $this->subscription = Subscription::findOrFail($id);
        $this->name = $this->subscription->name;
        $this->price = (string) $this->subscription->price;
        $this->url = $this->subscription->url;
        $this->isActive = $this->subscription->is_active;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'url' => ['nullable', 'url', 'max:255'],
            'isActive' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $isNewSubscription = $this->subscription === null;

        if ($this->subscription) {
            $this->subscription->update([
                'name' => $this->name,
                'price' => (float) $this->price,
                'url' => $this->url ?: null,
                'is_active' => $this->isActive,
            ]);

            Dialog::toast(__('app.toasts.subscription_updated'));
        } else {
            Subscription::create([
                'name' => $this->name,
                'price' => (float) $this->price,
                'url' => $this->url ?: null,
                'is_active' => true,
            ]);

            Dialog::toast(__('app.toasts.subscription_added'));
        }

        $this->reset(['name', 'price', 'url', 'isActive']);
        $this->subscription = null;

        $this->dispatch('subscription-saved');

        $this->redirect(route('subscriptions'), navigate: true);
    }

    public function cancel(): void
    {
        $this->reset(['name', 'price', 'url', 'isActive']);
        $this->subscription = null;

        $this->redirect(route('subscriptions'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.subscription-form', [
            'title' => $this->title,
            'currencySymbol' => $this->resolveCurrencySymbol(),
        ]);
    }

    protected function resolveCurrencySymbol(): string
    {
        $currencies = config('currencies.supported', ['EUR' => '€']);

        try {
            $storedCurrency = SecureStorage::get('currency');
        } catch (\Exception $e) {
            $storedCurrency = null;
        }

        $currency = $storedCurrency ?: array_key_first($currencies);

        return $currencies[$currency] ?? '€';
    }
}
