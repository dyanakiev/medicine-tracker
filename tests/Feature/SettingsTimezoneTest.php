<?php

use Native\Mobile\Facades\SecureStorage;

it('stores the selected timezone and updates configuration', function () {
    SecureStorage::shouldReceive('get')->andReturnNull();
    SecureStorage::shouldReceive('set')->once()->with('timezone', 'Europe/Sofia')->andReturnTrue();

    $this->put('/settings', [
        'timezone' => 'Europe/Sofia',
    ])->assertRedirect('/settings');

    expect(config('app.timezone'))->toBe('Europe/Sofia')
        ->and(date_default_timezone_get())->toBe('Europe/Sofia');
});
