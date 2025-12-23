<?php

use App\Livewire\SubscriptionList;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Native\Mobile\Facades\Dialog;

uses(RefreshDatabase::class);

it('shows only active subscriptions by default', function () {
    $active = Subscription::factory()->create(['name' => 'Spotify', 'is_active' => true]);
    $inactive = Subscription::factory()->create(['name' => 'Game Pass', 'is_active' => false]);

    Livewire::test(SubscriptionList::class)
        ->assertSee($active->name)
        ->assertDontSee($inactive->name);
});

it('can filter inactive subscriptions', function () {
    $active = Subscription::factory()->create(['name' => 'Prime', 'is_active' => true]);
    $inactive = Subscription::factory()->create(['name' => 'YouTube Premium', 'is_active' => false]);

    Livewire::test(SubscriptionList::class)
        ->call('setStatusFilter', 'inactive')
        ->assertSee($inactive->name)
        ->assertDontSee($active->name);
});

it('sorts subscriptions by price', function () {
    $low = Subscription::factory()->create(['name' => 'Apple TV', 'price' => 5, 'is_active' => true]);
    $high = Subscription::factory()->create(['name' => 'Adobe', 'price' => 25, 'is_active' => true]);

    Livewire::test(SubscriptionList::class)
        ->assertSeeInOrder([$high->name, $low->name])
        ->call('sortBy', 'asc')
        ->assertSeeInOrder([$low->name, $high->name]);
});

it('deletes a subscription when confirmed', function () {
    $subscription = Subscription::factory()->create(['name' => 'Dropbox', 'is_active' => true]);

    Dialog::shouldReceive('toast')->once()->andReturnNull();

    Livewire::test(SubscriptionList::class)
        ->call('handleDeleteConfirmation', 1, 'Delete', "delete-subscription-{$subscription->id}");

    expect(Subscription::query()->whereKey($subscription->id)->exists())->toBeFalse();
});
