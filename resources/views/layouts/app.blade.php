<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
    <title>{{ $title ?? __('app.app_name') }}</title>
    @vite(['resources/css/app.css'])
    @livewireStyles
</head>
<body class="min-h-screen text-slate-900 dark:text-slate-100 antialiased bg-gradient-to-br from-teal-50 via-slate-50 to-cyan-100 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900 transition-colors relative overflow-x-hidden nativephp-safe-area">
    <div class="pointer-events-none fixed inset-0 -z-10" wire:ignore.self>
        <div class="absolute -top-24 -left-24 h-72 w-72 rounded-full bg-teal-200/60 blur-3xl dark:bg-teal-500/10"></div>
        <div class="absolute top-20 -right-16 h-80 w-80 rounded-full bg-cyan-300/50 blur-3xl dark:bg-cyan-500/10"></div>
        <div class="absolute bottom-10 left-1/3 h-64 w-64 rounded-full bg-sky-200/40 blur-3xl dark:bg-sky-500/10"></div>
    </div>

    <header wire:ignore.self class="max-w-5xl mx-auto px-5 pt-6">
        <div class="app-card-strong px-5 py-4 flex items-center justify-between">
            <div class="min-w-0">
                <p class="text-[clamp(0.55rem,1.6vw,0.75rem)] uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400 whitespace-nowrap">{{ __('app.header_tagline') }}</p>
                <h1 class="truncate text-2xl font-semibold text-slate-900 dark:text-white">
                    {{ $title ?? __('app.app_name') }}
                </h1>
            </div>
            <div class="h-10 w-10 rounded-full bg-slate-900 text-white dark:bg-white dark:text-slate-900 flex items-center justify-center">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 6V18M6 12H18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    <path d="M4.5 12C4.5 7.86 7.86 4.5 12 4.5C16.14 4.5 19.5 7.86 19.5 12C19.5 16.14 16.14 19.5 12 19.5C7.86 19.5 4.5 16.14 4.5 12Z" stroke="currentColor" stroke-width="1.2" opacity="0.6"/>
                </svg>
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
                    href="{{ route('medicines') }}"
                    wire:navigate
                    class="flex flex-1 flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 text-center text-xs font-semibold leading-tight transition-colors {{ request()->routeIs('medicines') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white' }}"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 11.5L12 5L20 11.5V19.5C20 20.6 19.1 21.5 18 21.5H6C4.9 21.5 4 20.6 4 19.5V11.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                    {{ __('app.nav.home') }}
                </a>
                <a
                    href="{{ route('add-medicine') }}"
                    wire:navigate
                    class="flex flex-1 flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 text-center text-xs font-semibold leading-tight transition-colors {{ request()->routeIs('add-medicine') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white' }}"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 5V19M5 12H19" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    {{ __('app.nav.add') }}
                </a>
                <a
                    href="{{ route('settings') }}"
                    wire:navigate
                    class="flex flex-1 flex-col items-center justify-center gap-1 rounded-xl px-3 py-2 text-center text-xs font-semibold leading-tight transition-colors {{ request()->routeIs('settings') ? 'bg-slate-100 text-slate-900 dark:bg-slate-800 dark:text-white' : 'text-slate-500 hover:text-slate-900 dark:text-slate-300 dark:hover:text-white' }}"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 8.5C13.933 8.5 15.5 10.067 15.5 12C15.5 13.933 13.933 15.5 12 15.5C10.067 15.5 8.5 13.933 8.5 12C8.5 10.067 10.067 8.5 12 8.5Z" stroke="currentColor" stroke-width="1.5"/>
                        <path d="M19.4 12C19.4 11.5 19.36 11.02 19.28 10.56L21 9.2L19.2 6.2L17.12 6.88C16.34 6.24 15.44 5.74 14.46 5.42L14 3H10L9.54 5.42C8.56 5.74 7.66 6.24 6.88 6.88L4.8 6.2L3 9.2L4.72 10.56C4.64 11.02 4.6 11.5 4.6 12C4.6 12.5 4.64 12.98 4.72 13.44L3 14.8L4.8 17.8L6.88 17.12C7.66 17.76 8.56 18.26 9.54 18.58L10 21H14L14.46 18.58C15.44 18.26 16.34 17.76 17.12 17.12L19.2 17.8L21 14.8L19.28 13.44C19.36 12.98 19.4 12.5 19.4 12Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/>
                    </svg>
                    {{ __('app.nav.settings') }}
                </a>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
