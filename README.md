# Subscription Tracker

<p align="center">
  <img src="public/icon.png" alt="App Icon" width="50%" style="border-radius: 16px;">
</p>

A NativePHP Mobile app for tracking recurring subscriptions, built with Laravel, Livewire, and Tailwind. It stores device-specific settings via NativePHP Secure Storage, so the web-only Laravel app experience is incomplete.

## Features
- Create, edit, and delete subscriptions
- Mark subscriptions active/inactive and filter the list
- Sort by monthly price
- Monthly and yearly totals
- Multi-language UI
- Currency selection
- Compact view toggle

## Stack
- Laravel 12
- Livewire 3
- Tailwind CSS 4
- NativePHP Mobile

## Requirements
- PHP 8.4
- Composer
- Node.js + npm

## Development
For NativePHP, follow the official docs and ensure the required `NATIVEPHP_*` values are set in `.env`.

### NativePHP Emulators
If iOS emulator shows "No Devices":
```bash
sudo xcode-select -s /Applications/Xcode.app/Contents/Developer
```

Run Android emulator:
```bash
php artisan native:run android --watch
```

Run iOS emulator:
```bash
php artisan native:run ios --watch
```

## Tests
Tests use SQLite at `database/testing.sqlite`.

```bash
composer run test
```

## NativePHP
This project targets NativePHP Mobile. Follow the NativePHP setup docs before running on iOS or Android, and follow the NativePHP deployment docs for App Store / Play Store releases.
