<?php

use App\Livewire\SubscriptionForm;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Native\Mobile\Facades\Dialog;

uses(RefreshDatabase::class);

it('creates a subscription', function () {
    Dialog::shouldReceive('toast')->once()->andReturnNull();

    Livewire::test(SubscriptionForm::class)
        ->set('name', 'Netflix')
        ->set('price', '9.99')
        ->set('url', 'https://netflix.com')
        ->call('save')
        ->assertRedirect(route('subscriptions'))
        ->assertDispatched('subscription-saved');

    $subscription = Subscription::query()->first();

    expect($subscription)->not->toBeNull()
        ->and($subscription->name)->toBe('Netflix')
        ->and($subscription->price)->toBe('9.99')
        ->and($subscription->url)->toBe('https://netflix.com')
        ->and($subscription->is_active)->toBeTrue();
});

it('updates a subscription', function () {
    $subscription = Subscription::factory()->create([
        'name' => 'Hulu',
        'price' => 12.5,
        'url' => 'https://hulu.com',
        'is_active' => false,
    ]);

    Dialog::shouldReceive('toast')->once()->andReturnNull();

    Livewire::test(SubscriptionForm::class, ['id' => $subscription->id])
        ->set('name', 'Hulu Plus')
        ->set('price', '15.00')
        ->set('url', null)
        ->set('isActive', true)
        ->call('save')
        ->assertRedirect(route('subscriptions'))
        ->assertDispatched('subscription-saved');

    $subscription->refresh();

    expect($subscription->name)->toBe('Hulu Plus')
        ->and($subscription->price)->toBe('15.00')
        ->and($subscription->url)->toBeNull()
        ->and($subscription->is_active)->toBeTrue();
});

it('validates required fields', function () {
    Dialog::shouldReceive('toast')->never();

    Livewire::test(SubscriptionForm::class)
        ->set('name', '')
        ->set('price', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required', 'price' => 'required']);
});
