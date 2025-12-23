<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <title>{{ $title ?? __('app.app_name') }}</title>
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body class="min-h-screen text-slate-900 dark:text-slate-100 antialiased bg-gradient-to-br from-amber-50 via-slate-50 to-sky-100 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900 transition-colors relative overflow-x-hidden nativephp-safe-area">
    <div class="pointer-events-none fixed inset-0 -z-10" wire:ignore.self>
        <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-amber-200/60 blur-3xl dark:bg-amber-500/10"></div>
        <div class="absolute top-20 -right-16 h-80 w-80 rounded-full bg-sky-300/50 blur-3xl dark:bg-sky-500/10"></div>
        <div class="absolute bottom-10 left-1/3 h-64 w-64 rounded-full bg-rose-200/40 blur-3xl dark:bg-rose-500/10"></div>
    </div>

    <header wire:ignore.self class="max-w-5xl mx-auto px-5 pt-6">
        <div class="app-card-strong px-5 py-4 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">{{ __('app.header_tagline') }}</p>
                <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">
                    {{ $title ?? __('app.app_name') }}
                </h1>
            </div>
            <div class="h-10 w-10 rounded-full bg-slate-900 text-white dark:bg-white dark:text-slate-900 flex items-center justify-center">
                <svg class="w-6 h-6" x="0px" y="0px" viewBox="0 0 88.47 122.88" style="enable-background:new 0 0 88.47 122.88" xml:space="preserve"><g><path fill="currentColor" d="M11.86,68.08L7.62,92.09c-0.07,0.33-0.2,0.63-0.4,0.86c-1.51,1.95-2.46,3.62-2.74,4.97c-0.2,1,0,1.8,0.67,2.43l16.56,16.56 c1.05,1.01,2.28,1.65,3.74,1.91c1.6,0.3,3.46,0.13,5.6-0.45c0.03,0,0.08-0.02,0.12-0.02c0.81-0.22,1.88-0.48,2.89-0.73 c4.44-1.08,8.31-2.03,11.91-5.29l4.62-4.82c0.05-0.08,0.12-0.15,0.18-0.22c0.07-0.07,0.52-0.52,1.13-1.1 c3.16-3.09,7.07-6.9,4.69-10.24l-1.85-1.85c-0.9,0.86-1.85,1.71-2.76,2.53c-0.83,0.73-1.61,1.41-2.33,2.13 c-0.76,0.76-2,0.76-2.76,0c-0.76-0.77-0.76-2,0-2.76c0.71-0.72,1.6-1.5,2.51-2.31c3.13-2.76,6.72-5.92,4.79-8.68l-1.83-1.83 c-0.1-0.1-0.18-0.22-0.27-0.33c-1.05,1.08-2.21,2.11-3.34,3.11c-0.83,0.73-1.61,1.41-2.33,2.13c-0.77,0.77-2,0.77-2.76,0 c-0.77-0.76-0.77-2,0-2.76c0.71-0.71,1.6-1.5,2.51-2.31c3.13-2.76,6.72-5.92,4.79-8.68l-1.83-1.83c-0.13-0.13-0.23-0.27-0.32-0.42 l-5.37,5.37c-0.77,0.76-2,0.76-2.76,0c-0.76-0.77-0.76-2,0-2.76l10.07-10.07c2.41-2.41,2.96-4.92,2.33-6.82 c-0.23-0.7-0.62-1.31-1.1-1.8c-0.25-0.25-0.53-0.47-0.84-0.66l-0.01,0c-0.15,0.07-0.26-0.08-0.4-0.22 c-0.17-0.08-0.36-0.16-0.54-0.22c-1.67-0.55-3.84-0.16-6.04,1.69c-0.03,0.03-0.06,0.06-0.09,0.08c-0.24,0.2-0.48,0.42-0.72,0.66 L22.44,78.27c-0.76,0.76-2,0.76-2.76,0c-0.7-0.7-0.76-1.78-0.18-2.55L11.86,68.08L11.86,68.08z M25.08,70.11l0.67-0.67l13.79-13.79 c-1.43-0.66-2.76-1.59-3.94-2.77c-5.25-5.25-5.25-13.73,0-18.98c5.25-5.25,13.73-5.25,18.98,0c5.25,5.25,5.25,13.73,0,18.98 c-0.04,0.04-0.09,0.09-0.13,0.13c0.1,0.09,0.21,0.19,0.31,0.29c0.43,0.43,0.8,0.9,1.13,1.4l17.1-17.1c-2.62-2.62-2.62-6.9,0-9.53 L60.25,15.33c-2.62,2.62-6.9,2.62-9.53,0L15.28,50.77c2.62,2.62,2.62,6.9,0,9.53L25.08,70.11L25.08,70.11z M55.14,65.57 c-0.46,0.64-0.99,1.28-1.62,1.9l-2,2l-0.02-0.05c0.15,0.08,0.28,0.18,0.42,0.32l1.91,1.91c0.1,0.1,0.2,0.23,0.28,0.35 c2.15,2.94,1.81,5.57,0.35,7.97c0.27,0.1,0.52,0.25,0.71,0.45l1.91,1.91c0.1,0.1,0.2,0.23,0.28,0.35c2.31,3.18,1.73,5.95,0,8.48 c0.08,0.05,0.15,0.12,0.23,0.2l1.91,1.91c0.1,0.1,0.2,0.23,0.28,0.35c4.44,6.07-0.85,11.22-5.1,15.38l-1.1,1.1l-4.74,4.97 l-0.15,0.15c-4.34,3.94-8.65,4.99-13.62,6.2c-0.83,0.2-1.68,0.42-2.84,0.71c-0.03,0-0.05,0.02-0.08,0.02 c-2.69,0.73-5.14,0.91-7.33,0.52c-2.23-0.4-4.16-1.4-5.77-2.98L2.52,103.15c-1.68-1.61-2.24-3.61-1.78-5.97 c0.37-1.9,1.46-3.99,3.19-6.25l4.42-25.04v-0.1c0.04-0.31,0.1-0.66,0.17-1.04L0,56.23L56.23,0l32.24,32.24L55.14,65.57L55.14,65.57 z"/></g></svg>
             </div>
        </div>
    </header>

    <div class="max-w-5xl mx-auto px-5 pt-6 pb-[calc(9rem+var(--inset-bottom))] space-y-8">
        {{ $slot }}
    </div>

    <div class="fixed bottom-0 left-0 w-full z-50 pb-[var(--inset-bottom)]" wire:ignore.self>
        <div class="max-w-5xl mx-auto px-5 pb-3">
            <div class="app-card-strong flex items-center justify-between gap-2 px-3 py-2 bg-white/95 dark:bg-slate-900/95">
                <a
                    href="{{ route('subscriptions') }}"
                    wire:navigate
                    class="flex flex-1 flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 text-xs font-semibold transition-colors {{ request()->routeIs('subscriptions') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white' }}"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 11.5L12 5L20 11.5V19.5C20 20.6 19.1 21.5 18 21.5H6C4.9 21.5 4 20.6 4 19.5V11.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                    {{ __('app.nav.home') }}
                </a>
                <a
                    href="{{ route('add-subscription') }}"
                    wire:navigate
                    class="flex flex-1 flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 text-xs font-semibold transition-colors {{ request()->routeIs('add-subscription') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white' }}"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    {{ __('app.nav.add') }}
                </a>
                <a
                    href="{{ route('settings') }}"
                    wire:navigate
                    class="flex flex-1 flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 text-xs font-semibold transition-colors {{ request()->routeIs('settings') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white' }}"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 8.5C13.933 8.5 15.5 10.067 15.5 12C15.5 13.933 13.933 15.5 12 15.5C10.067 15.5 8.5 13.933 8.5 12C8.5 10.067 10.067 8.5 12 8.5Z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M19.4 12C19.4 11.5 19.36 11.02 19.28 10.56L21 9.2L19.2 6.2L17.12 6.88C16.34 6.24 15.44 5.74 14.46 5.42L14 3H10L9.54 5.42C8.56 5.74 7.66 6.24 6.88 6.88L4.8 6.2L3 9.2L4.72 10.56C4.64 11.02 4.6 11.5 4.6 12C4.6 12.5 4.64 12.98 4.72 13.44L3 14.8L4.8 17.8L6.88 17.12C7.66 17.76 8.56 18.26 9.54 18.58L10 21H14L14.46 18.58C15.44 18.26 16.34 17.76 17.12 17.12L19.2 17.8L21 14.8L19.28 13.44C19.36 12.98 19.4 12.5 19.4 12Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                    {{ __('app.nav.settings') }}
                </a>
            </div>
        </div>
    </nav>

    @livewireScripts
</body>
</html>
