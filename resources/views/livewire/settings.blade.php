<div class="space-y-8">
    <header class="space-y-4 animate-fade-up">
        <div class="inline-flex items-center gap-2 rounded-full border border-violet-200/70 bg-violet-100/70 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-violet-700 dark:border-violet-500/40 dark:bg-violet-500/10 dark:text-violet-200">
            <span class="h-1.5 w-1.5 rounded-full bg-violet-500"></span>
            {{ __('app.badges.preferences') }}
        </div>
        <h1 class="text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ __('app.headers.personalize') }}</h1>
        <p class="text-slate-600 dark:text-slate-400 mt-2">{{ __('app.descriptions.manage_preferences') }}</p>
    </header>

    <div class="app-card-strong p-6 animate-fade-up animation-delay-150">
        <h3 class="text-xl font-semibold mb-6">{{ __('app.settings.display') }}</h3>

        <div class="space-y-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ __('app.settings.compact') }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('app.settings.compact_help') }}</p>
                </div>
                <button
                    wire:click="toggleCompactView"
                    class="inline-flex items-center gap-2 rounded-full border border-slate-200/80 dark:border-slate-700 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-100 bg-white dark:bg-slate-900/50 shadow-sm transition-colors hover:bg-slate-50 dark:hover:bg-slate-800"
                >
                    <span class="h-2 w-2 rounded-full {{ $compactView ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    {{ $compactView ? __('app.settings.enabled') : __('app.settings.disabled') }}
                </button>
            </div>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ __('app.settings.language') }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('app.settings.language_help') }}</p>
                </div>
                <select
                    wire:model.live="locale"
                    class="w-full sm:w-52 rounded-full border border-slate-200/80 dark:border-slate-700 bg-white dark:bg-slate-900/50 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                >
                    @foreach($languages as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ __('app.settings.currency') }}</p>
                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('app.settings.currency_help') }}</p>
                </div>
                <select
                    wire:model.live="currency"
                    class="w-full sm:w-52 rounded-full border border-slate-200/80 dark:border-slate-700 bg-white dark:bg-slate-900/50 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-sky-500"
                >
                    @foreach($currencies as $value => $label)
                        <option value="{{ $value }}">{{ $value }} {{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
