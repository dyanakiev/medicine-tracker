<?php

use App\Livewire\Settings;
use Livewire\Livewire;
use Native\Mobile\Facades\SecureStorage;

it('stores the selected timezone and updates configuration', function () {
    SecureStorage::shouldReceive('get')->andReturnNull();
    SecureStorage::shouldReceive('set')->once()->with('timezone', 'Europe/Sofia')->andReturnTrue();

    Livewire::test(Settings::class)
        ->set('timezone', 'Europe/Sofia');

    expect(config('app.timezone'))->toBe('Europe/Sofia')
        ->and(date_default_timezone_get())->toBe('Europe/Sofia');
});
