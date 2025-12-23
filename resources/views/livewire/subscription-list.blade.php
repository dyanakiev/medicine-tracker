<div class="space-y-8">
    <header class="space-y-4 animate-fade-up">
        <div class="inline-flex items-center gap-2 rounded-full border border-sky-200/70 bg-sky-100/70 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-sky-700 dark:border-sky-500/40 dark:bg-sky-500/10 dark:text-sky-200">
            <span class="h-1.5 w-1.5 rounded-full bg-sky-500"></span>
            {{ __('app.badges.monthly_pulse') }}
        </div>
        <h1 class="text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ __('app.headers.subscriptions_glance') }}</h1>
        <p class="text-slate-600 dark:text-slate-400">{{ __('app.descriptions.subscription_intro') }}</p>
    </header>
    <section class="grid gap-4 sm:grid-cols-2 animate-fade-up animation-delay-150">
        <div class="app-card p-6">
            <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('app.stats.total_month') }}</p>
            <p class="text-3xl font-semibold mt-2 text-slate-900 dark:text-white">
                {{ number_format($totalPerMonth, 2, '.', ',') }} {{ $currencySymbol }}
            </p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('app.stats.monthly_spend') }}</p>
        </div>
        <div class="app-card p-6">
            <p class="text-xs uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('app.stats.total_year') }}</p>
            <p class="text-3xl font-semibold mt-2 text-slate-900 dark:text-white">
                {{ number_format($totalPerYear, 2, '.', ',') }} {{ $currencySymbol }}
            </p>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ __('app.stats.annual_total') }}</p>
        </div>
    </section>

    <section>
        <div class="mb-4 space-y-3">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-2xl font-semibold">{{ __('app.subscriptions.active') }}</h2>
                <button
                    type="button"
                    wire:click="toggleFilters"
                    aria-label="{{ __('app.subscriptions.filters') }}"
                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200/80 dark:border-slate-700 bg-white/80 dark:bg-slate-900/70 text-slate-600 dark:text-slate-200 shadow-sm transition-colors hover:bg-slate-50 dark:hover:bg-slate-800"
                >
                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                        <path d="M2 3.5H14M4.5 8H11.5M6.5 12.5H9.5" stroke="currentColor" stroke-linecap="round" stroke-width="1.5"/>
                        <circle cx="6" cy="3.5" r="1.5" fill="currentColor"/>
                        <circle cx="10" cy="8" r="1.5" fill="currentColor"/>
                        <circle cx="8" cy="12.5" r="1.5" fill="currentColor"/>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                {{ trans_choice('app.subscriptions.tracked', $subscriptions->count(), ['count' => $subscriptions->count()]) }}
            </p>
            @if($filtersOpen)
                <div class="app-card p-4 space-y-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('app.subscriptions.filter_label') }}</p>
                        <div class="mt-2 inline-flex rounded-full border border-slate-200/80 dark:border-slate-700 bg-white/80 dark:bg-slate-900/70 overflow-hidden text-xs shadow-sm">
                            <button
                                wire:click="setStatusFilter('active')"
                                class="px-3 py-1.5 font-semibold transition-colors {{ $statusFilter === 'active' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100' }}"
                            >
                                {{ __('app.subscriptions.filter_active') }}
                            </button>
                            <button
                                wire:click="setStatusFilter('inactive')"
                                class="px-3 py-1.5 font-semibold transition-colors {{ $statusFilter === 'inactive' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100' }}"
                            >
                                {{ __('app.subscriptions.filter_inactive') }}
                            </button>
                            <button
                                wire:click="setStatusFilter('all')"
                                class="px-3 py-1.5 font-semibold transition-colors {{ $statusFilter === 'all' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100' }}"
                            >
                                {{ __('app.subscriptions.filter_all') }}
                            </button>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('app.subscriptions.sort_label') }}</p>
                        <div class="mt-2 inline-flex rounded-full border border-slate-200/80 dark:border-slate-700 bg-white/80 dark:bg-slate-900/70 overflow-hidden text-sm shadow-sm">
                            <button
                                wire:click="sortBy('desc')"
                                aria-label="{{ __('app.sort.high') }}"
                                class="inline-flex items-center justify-center h-9 w-9 transition-colors {{ $sortOrder === 'desc' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100' }}"
                            >
                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M8 2.5V13.5M8 13.5L4.5 10M8 13.5L11.5 10" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button
                                wire:click="sortBy('asc')"
                                aria-label="{{ __('app.sort.low') }}"
                                class="inline-flex items-center justify-center h-9 w-9 transition-colors {{ $sortOrder === 'asc' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100' }}"
                            >
                                <svg class="h-3.5 w-3.5 rotate-180" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M8 2.5V13.5M8 13.5L4.5 10M8 13.5L11.5 10" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        @if($compactView)
            <div class="space-y-4">
                @forelse($subscriptions as $subscription)
                    @php
                        $percentage = $totalPerMonth > 0 ? ($subscription->price / $totalPerMonth) * 100 : 0;
                        $faviconUrl = '';
                        if ($subscription->url) {
                            try {
                                $hostname = parse_url($subscription->url, PHP_URL_HOST);
                                $faviconUrl = $hostname ? "https://www.google.com/s2/favicons?domain={$hostname}&sz=64" : '';
                            } catch (\Exception $e) {
                                $faviconUrl = '';
                            }
                        }
                    @endphp
                    <div wire:key="subscription-compact-{{ $subscription->id }}" class="subscription-card app-card app-card-interactive p-4 space-y-4">
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            @if($faviconUrl)
                                <img src="{{ $faviconUrl }}" alt="{{ $subscription->name }} icon" class="w-9 h-9 rounded-full bg-white/70 p-1 shrink-0">
                            @endif
                            <div class="flex-1 min-w-0">
                                <p class="text-base font-semibold text-slate-900 dark:text-slate-100 truncate">{{ $subscription->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ __('app.subscriptions.of_monthly_spend', ['percent' => number_format($percentage, 1)]) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <div class="text-right">
                                <p class="text-lg font-bold text-slate-900 dark:text-slate-100">
                                    {{ number_format($subscription->price, 2, '.', ',') }} {{ $currencySymbol }}
                                    <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('app.subscriptions.monthly_suffix') }}</span>
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <button
                                    wire:click="edit({{ $subscription->id }})"
                                    aria-label="{{ __('app.actions.edit_subscription', ['name' => $subscription->name]) }}"
                                    title="{{ __('app.actions.edit') }}"
                                    class="h-10 w-10 rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 hover:bg-sky-200 dark:hover:bg-sky-800 flex items-center justify-center transition-colors shrink-0"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    wire:click="delete({{ $subscription->id }})"
                                    aria-label="{{ __('app.actions.delete_subscription', ['name' => $subscription->name]) }}"
                                    title="{{ __('app.actions.delete') }}"
                                    class="h-10 w-10 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-800 flex items-center justify-center transition-colors shrink-0"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <p class="text-slate-500 dark:text-slate-400">{{ __('app.subscriptions.empty') }}</p>
                    </div>
                @endforelse
            </div>
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($subscriptions as $subscription)
                    @php
                        $percentage = $totalPerMonth > 0 ? ($subscription->price / $totalPerMonth) * 100 : 0;
                        $colors = match (true) {
                            $subscription->price >= 30 => [
                                'card' => 'bg-gradient-to-br from-rose-600 via-rose-500 to-rose-700 text-white',
                                'accent' => 'text-white/85',
                                'button' => 'bg-white/15 text-white hover:bg-white/25',
                            ],
                            $subscription->price >= 15 => [
                                'card' => 'bg-gradient-to-br from-amber-500 via-orange-500 to-orange-600 text-white',
                                'accent' => 'text-white/85',
                                'button' => 'bg-white/15 text-white hover:bg-white/25',
                            ],
                            $subscription->price >= 8 => [
                                'card' => 'bg-gradient-to-br from-sky-600 via-indigo-600 to-indigo-700 text-white',
                                'accent' => 'text-white/85',
                                'button' => 'bg-white/15 text-white hover:bg-white/25',
                            ],
                            default => [
                                'card' => 'bg-white text-slate-900 border border-slate-200 dark:bg-slate-900 dark:text-slate-100 dark:border-slate-800',
                                'accent' => 'text-slate-500 dark:text-slate-400',
                                'button' => 'bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-slate-800/70 dark:text-slate-200 dark:hover:bg-slate-700/80',
                            ],
                        };
                        $faviconUrl = '';
                        if ($subscription->url) {
                            try {
                                $hostname = parse_url($subscription->url, PHP_URL_HOST);
                                $faviconUrl = $hostname ? "https://www.google.com/s2/favicons?domain={$hostname}&sz=64" : '';
                            } catch (\Exception $e) {
                                $faviconUrl = '';
                            }
                        }
                    @endphp
                    <div wire:key="subscription-grid-{{ $subscription->id }}" class="subscription-card rounded-2xl shadow p-5 flex flex-col justify-between {{ $colors['card'] }} app-card-interactive" style="min-height: 210px;">
                        <div>
                            <p class="text-sm uppercase tracking-wide {{ $colors['accent'] }}">{{ __('app.subscriptions.monthly') }}</p>
                            <p class="text-3xl font-bold">{{ number_format($subscription->price, 2, '.', ',') }} {{ $currencySymbol }}</p>
                            <p class="text-sm {{ $colors['accent'] }}">
                                {{ __('app.subscriptions.of_monthly_spend', ['percent' => number_format($percentage, 1)]) }}
                            </p>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                @if($faviconUrl)
                                    <img src="{{ $faviconUrl }}" alt="{{ $subscription->name }} icon" class="w-6 h-6 rounded-full bg-white/40 p-1">
                                @endif
                                <p class="text-lg font-semibold">{{ $subscription->name }}</p>
                            </div>
                            <p class="text-sm {{ $colors['accent'] }}">
                                {{ number_format($subscription->price * 12, 2, '.', ',') }} {{ $currencySymbol }} {{ __('app.subscriptions.yearly_suffix') }}
                            </p>
                            <div class="flex gap-2 mt-3">
                                <button
                                    wire:click="edit({{ $subscription->id }})"
                                    aria-label="{{ __('app.actions.edit_subscription', ['name' => $subscription->name]) }}"
                                    title="{{ __('app.actions.edit') }}"
                                    class="h-10 w-10 rounded-full {{ $colors['button'] }} flex items-center justify-center transition-colors shrink-0"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    wire:click="delete({{ $subscription->id }})"
                                    aria-label="{{ __('app.actions.delete_subscription', ['name' => $subscription->name]) }}"
                                    title="{{ __('app.actions.delete') }}"
                                    class="h-10 w-10 rounded-full {{ $colors['button'] }} flex items-center justify-center transition-colors shrink-0"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-slate-500 dark:text-slate-400">{{ __('app.subscriptions.empty') }}</p>
                    </div>
                @endforelse
            </div>
        @endif
    </section>

</div>
