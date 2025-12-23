<?php

namespace App\Middleware;

use Closure;
use Illuminate\Http\Request;
use Native\Mobile\Facades\SecureStorage;
use Symfony\Component\HttpFoundation\Response;

class SetTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $timezone = $this->resolveTimezone();

        if ($timezone !== null) {
            config(['app.timezone' => $timezone]);
            date_default_timezone_set($timezone);
        }

        return $next($request);
    }

    protected function resolveTimezone(): ?string
    {
        try {
            $timezone = SecureStorage::get('timezone');
        } catch (\Exception $e) {
            return null;
        }

        if (! is_string($timezone) || $timezone === '') {
            return null;
        }

        if (! in_array($timezone, \DateTimeZone::listIdentifiers(), true)) {
            return null;
        }

        return $timezone;
    }
}
