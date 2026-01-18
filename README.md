# Medicine Tracker

<p align="center">
  <img src="public/icon.png" alt="App Icon" width="50%" style="border-radius: 16px;">
</p>

A NativePHP Mobile app for tracking medicines and doses, built with Laravel, Inertia, React, and Tailwind. It stores device-specific settings via NativePHP Secure Storage.

## Features
- Create, edit, and delete medicines
- Flexible schedules (hours, days, weekdays, daily times, specific dates, as-needed)
- Mark doses as taken and keep a dose history (with removal)
- Active/paused filters and compact view toggle
- Notes preview with full notes on expand
- Multi-language UI
- Timezone setting for accurate scheduling

## Stack
- Laravel 12
- Inertia.js v2 (Laravel adapter)
- React 18
- Tailwind CSS 4
- NativePHP Mobile

## Requirements
- PHP 8.2+
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
npm run build -- --mode=android
php artisan native:run android --watch
```

Run iOS emulator:
```bash
npm run build -- --mode=ios
php artisan native:run ios --watch
```

## Tests
Tests use SQLite at `database/testing.sqlite`.

```bash
composer run test
```

## NativePHP
This project targets NativePHP Mobile. Follow the NativePHP setup docs before running on iOS or Android, and follow the NativePHP deployment docs for App Store / Play Store releases.
