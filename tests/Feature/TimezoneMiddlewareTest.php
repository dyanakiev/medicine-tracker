<?php

use Native\Mobile\Facades\SecureStorage;

it('sets the timezone from the system', function () {
    SecureStorage::shouldReceive('get')->with('timezone')->once()->andReturn('Europe/Sofia');

    $this->get('/medicines')->assertSuccessful();

    expect(config('app.timezone'))->toBe('Europe/Sofia')
        ->and(date_default_timezone_get())->toBe('Europe/Sofia');
});
