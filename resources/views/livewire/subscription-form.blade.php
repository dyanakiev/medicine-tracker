<div class="space-y-8">
    <header class="space-y-4 animate-fade-up">
        <div class="inline-flex items-center gap-2 rounded-full border border-emerald-200/70 bg-emerald-100/70 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:border-emerald-500/40 dark:bg-emerald-500/10 dark:text-emerald-200">
            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
            {{ $subscription ? __('app.badges.update') : __('app.badges.create') }}
        </div>
        <h1 class="text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">
            {{ $subscription ? __('app.titles.edit_subscription') : __('app.titles.add_subscription') }}
        </h1>
        <p class="text-slate-600 dark:text-slate-400">
            {{ $subscription ? __('app.descriptions.subscription_edit') : __('app.descriptions.subscription_add') }}
        </p>
    </header>

    <div class="app-card-strong p-6 animate-fade-up animation-delay-150">
        <form wire:submit="save" class="space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    {{ __('app.form.name') }}
                </label>
                <input
                    type="text"
                    id="name"
                    wire:model="name"
                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    placeholder="{{ __('app.form.name_placeholder') }}"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    {{ __('app.form.price', ['currency' => $currencySymbol]) }}
                </label>
                <input
                    type="number"
                    id="price"
                    wire:model="price"
                    step="0.01"
                    min="0"
                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    placeholder="{{ __('app.form.price_placeholder') }}"
                >
                @error('price')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="url" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    {{ __('app.form.url') }}
                </label>
                <input
                    type="url"
                    id="url"
                    wire:model="url"
                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                    placeholder="{{ __('app.form.url_placeholder') }}"
                >
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('app.form.url_help') }}</p>
                @error('url')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            @if($subscription)
                <div class="flex items-center justify-between rounded-2xl border border-slate-200/80 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-900/50 px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-slate-900 dark:text-slate-100">{{ __('app.form.active') }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('app.form.active_help') }}</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="isActive" class="sr-only peer">
                        <div class="h-6 w-11 rounded-full bg-slate-200 peer-checked:bg-emerald-500 dark:bg-slate-700 transition-colors"></div>
                        <div class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow-sm transition-transform peer-checked:translate-x-5"></div>
                    </label>
                </div>
            @endif

            <button
                type="submit"
                class="w-full px-4 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-medium rounded-xl transition-colors shadow-sm"
            >
            {{ $subscription ? __('app.form.submit_update') : __('app.form.submit_add') }}
        </button>

        @if($subscription)
            <button
                type="button"
                wire:click="cancel"
                class="w-full px-4 py-2.5 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-900 dark:text-slate-100 font-medium rounded-xl transition-colors"
            >
                {{ __('app.form.cancel') }}
            </button>
        @endif
        </form>
    </div>
</div>
