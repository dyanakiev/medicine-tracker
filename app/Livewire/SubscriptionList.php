<?php

namespace App\Livewire;

use App\Models\Subscription;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Alert\ButtonPressed;
use Native\Mobile\Facades\Dialog;
use Native\Mobile\Facades\SecureStorage;

#[Layout('layouts.app')]
class SubscriptionList extends Component
{
    public string $sortOrder = 'desc';

    public bool $compactView = false;

    public string $statusFilter = 'active';

    public bool $filtersOpen = false;

    public function mount(): void
    {
        try {
            $this->compactView = SecureStorage::get('compact_view') === 'true';
        } catch (\Exception $e) {
            $this->compactView = false;
        }
    }

    #[On('subscription-saved')]
    public function refresh(): void
    {
        // Component will automatically re-render when subscriptions change
    }

    public function sortBy(string $order): void
    {
        $this->sortOrder = $order;
    }

    public function setStatusFilter(string $filter): void
    {
        $this->statusFilter = $filter;
    }

    public function toggleFilters(): void
    {
        $this->filtersOpen = ! $this->filtersOpen;
    }

    public function delete(int $id): void
    {
        $subscription = Subscription::findOrFail($id);

        Dialog::alert(
            __('app.dialogs.delete_title'),
            __('app.dialogs.delete_body', ['name' => $subscription->name]),
            [__('app.actions.cancel'), __('app.actions.delete')]
        )->id("delete-subscription-{$id}")->event(ButtonPressed::class);
    }

    #[OnNative(ButtonPressed::class)]
    public function handleDeleteConfirmation(int $index, string $label, ?string $id = null): void
    {
        if ($index === 1 && $id && str_starts_with($id, 'delete-subscription-')) {
            // User clicked "Delete" (index 1) and this is a delete dialog
            $subscriptionId = (int) str_replace('delete-subscription-', '', $id);
            Subscription::findOrFail($subscriptionId)->delete();
            Dialog::toast(__('app.toasts.subscription_deleted'));
        }
        // If Cancel (index 0) or invalid, do nothing
    }

    public function edit(int $id): void
    {
        $this->redirect(route('add-subscription', ['id' => $id]), navigate: true);
    }

    public function render(): View
    {
        $subscriptions = Subscription::query()
            ->when($this->statusFilter === 'active', fn ($query) => $query->where('is_active', true))
            ->when($this->statusFilter === 'inactive', fn ($query) => $query->where('is_active', false))
            ->when($this->sortOrder === 'desc', fn ($query) => $query->orderByDesc('price'))
            ->when($this->sortOrder === 'asc', fn ($query) => $query->orderBy('price'))
            ->get();

        $totalPerMonth = $subscriptions->sum('price');
        $totalPerYear = $totalPerMonth * 12;

        return view('livewire.subscription-list', [
            'subscriptions' => $subscriptions,
            'totalPerMonth' => $totalPerMonth,
            'totalPerYear' => $totalPerYear,
            'compactView' => $this->compactView,
            'title' => __('app.titles.subscriptions'),
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
