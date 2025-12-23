<div class="space-y-6">
    <header class="space-y-3 animate-fade-up">
        <div class="inline-flex items-center gap-2 rounded-full border border-teal-200/70 bg-teal-100/70 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-teal-700 dark:border-teal-500/40 dark:bg-teal-500/10 dark:text-teal-200">
            <span class="h-1.5 w-1.5 rounded-full bg-teal-500"></span>
            {{ __('app.badges.care_plan') }}
        </div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900 dark:text-white">{{ __('app.headers.medicines_glance') }}</h1>
        <p class="text-sm text-slate-600 dark:text-slate-400">{{ __('app.descriptions.medicine_intro') }}</p>
    </header>

    <section class="animate-fade-up animation-delay-150">
        <div class="app-card px-4 py-3">
            <div class="flex flex-wrap items-center gap-3 text-sm text-slate-600 dark:text-slate-300">
                <div class="flex items-center gap-2 rounded-full border border-rose-100/80 dark:border-rose-900/40 bg-rose-50/70 dark:bg-rose-900/20 px-3 py-1">
                    <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                    <span class="text-[11px] uppercase tracking-widest text-rose-500">{{ __('app.stats.due_now') }}</span>
                    <span class="text-base font-semibold text-slate-900 dark:text-white">{{ $dueNowCount }}</span>
                </div>
                <div class="flex items-center gap-2 rounded-full border border-sky-100/80 dark:border-sky-900/40 bg-sky-50/70 dark:bg-sky-900/20 px-3 py-1">
                    <span class="h-2 w-2 rounded-full bg-sky-500"></span>
                    <span class="text-[11px] uppercase tracking-widest text-sky-600">{{ __('app.stats.next_24h') }}</span>
                    <span class="text-base font-semibold text-slate-900 dark:text-white">{{ $upNextCount }}</span>
                </div>
                <div class="flex items-center gap-2 rounded-full border border-emerald-100/80 dark:border-emerald-900/40 bg-emerald-50/70 dark:bg-emerald-900/20 px-3 py-1">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <span class="text-[11px] uppercase tracking-widest text-emerald-600">{{ __('app.stats.taken_today') }}</span>
                    <span class="text-base font-semibold text-slate-900 dark:text-white">{{ $takenTodayCount }}</span>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="mb-4 space-y-3">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-2xl font-semibold">{{ __('app.medicines.schedule') }}</h2>
                <button
                    type="button"
                    wire:click="toggleFilters"
                    aria-label="{{ __('app.medicines.filters') }}"
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
                {{ trans_choice('app.medicines.tracked', $scheduledMedicines->count(), ['count' => $scheduledMedicines->count()]) }}
            </p>
            @if($filtersOpen)
                <div class="app-card p-4 space-y-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('app.medicines.filter_label') }}</p>
                        <div class="mt-2 inline-flex rounded-full border border-slate-200/80 dark:border-slate-700 bg-white/80 dark:bg-slate-900/70 overflow-hidden text-xs shadow-sm">
                            <button
                                wire:click="setStatusFilter('active')"
                                class="px-3 py-1.5 font-semibold transition-colors {{ $statusFilter === 'active' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100' }}"
                            >
                                {{ __('app.medicines.filter_active') }}
                            </button>
                            <button
                                wire:click="setStatusFilter('paused')"
                                class="px-3 py-1.5 font-semibold transition-colors {{ $statusFilter === 'paused' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100' }}"
                            >
                                {{ __('app.medicines.filter_paused') }}
                            </button>
                            <button
                                wire:click="setStatusFilter('all')"
                                class="px-3 py-1.5 font-semibold transition-colors {{ $statusFilter === 'all' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100' }}"
                            >
                                {{ __('app.medicines.filter_all') }}
                            </button>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('app.medicines.sort_label') }}</p>
                        <div class="mt-2 inline-flex rounded-full border border-slate-200/80 dark:border-slate-700 bg-white/80 dark:bg-slate-900/70 overflow-hidden text-sm shadow-sm">
                            <button
                                wire:click="sortBy('soonest')"
                                aria-label="{{ __('app.sort.soonest') }}"
                                class="inline-flex items-center justify-center h-9 w-9 transition-colors {{ $sortOrder === 'soonest' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100' }}"
                            >
                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M8 2.5V13.5M8 2.5L4.5 6M8 2.5L11.5 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button
                                wire:click="sortBy('latest')"
                                aria-label="{{ __('app.sort.latest') }}"
                                class="inline-flex items-center justify-center h-9 w-9 transition-colors {{ $sortOrder === 'latest' ? 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-100' : 'bg-transparent text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100' }}"
                            >
                                <svg class="h-3.5 w-3.5 rotate-180" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="M8 2.5V13.5M8 2.5L4.5 6M8 2.5L11.5 6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if($compactView)
            <div class="space-y-4">
                @forelse($scheduledMedicines as $medicine)
                    @php
                        $nextDose = $medicine->next_dose_at;
                        $isDue = $medicine->is_active && $nextDose && $nextDose->lessThanOrEqualTo(now());
                        $statusLabel = $isDue
                            ? __('app.medicines.due_now')
                            : ($nextDose ? $nextDose->diffForHumans() : __('app.medicines.not_scheduled'));
                        $scheduleSummary = match ($medicine->schedule_type) {
                            'hours' => __('app.medicines.schedule_hours', ['hours' => $medicine->frequency_hours]),
                            'days' => __('app.medicines.schedule_days', [
                                'days' => $medicine->frequency_days,
                                'time' => $medicine->time_of_day ?? '--:--',
                            ]),
                            'weekdays' => __('app.medicines.schedule_weekdays', [
                                'days' => $medicine->weekdays ? implode(', ', $medicine->weekdays) : __('app.medicines.not_scheduled'),
                                'time' => $medicine->time_of_day ?? '--:--',
                            ]),
                            'times' => __('app.medicines.schedule_times', [
                                'times' => $medicine->times ? implode(', ', $medicine->times) : __('app.medicines.not_scheduled'),
                            ]),
                            'dates' => __('app.medicines.schedule_dates', [
                                'dates' => $medicine->dates ? implode(', ', $medicine->dates) : __('app.medicines.not_scheduled'),
                                'time' => $medicine->time_of_day ?? '--:--',
                            ]),
                            'as_needed' => __('app.medicines.as_needed'),
                            default => __('app.medicines.not_scheduled'),
                        };
                    @endphp
                    <div wire:key="medicine-compact-{{ $medicine->id }}" class="app-card app-card-interactive p-4 space-y-4 min-w-0">
                        <div class="flex items-start gap-3 min-w-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-base font-semibold text-slate-900 dark:text-slate-100 truncate">{{ $medicine->name }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">{{ $medicine->dosage }} · {{ $scheduleSummary }}</p>
                                @if($medicine->notes)
                                    <button
                                        type="button"
                                        wire:click="toggleHistory({{ $medicine->id }})"
                                        class="mt-1 block w-full min-w-0 overflow-hidden text-ellipsis whitespace-nowrap text-left text-xs text-slate-500 dark:text-slate-400"
                                    >
                                        <span class="font-semibold text-slate-600 dark:text-slate-300">{{ __('app.medicines.notes_label') }}</span>
                                        {{ $medicine->notes }}
                                    </button>
                                @endif
                            </div>
                            <span class="text-xs font-semibold {{ $isDue ? 'text-rose-600 dark:text-rose-300' : 'text-slate-500 dark:text-slate-400' }}">
                                {{ $statusLabel }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $medicine->last_taken_at ? __('app.medicines.last_taken', ['time' => $medicine->last_taken_at->diffForHumans()]) : __('app.medicines.not_taken') }}
                            </div>
                            <div class="flex gap-2">
                                <button
                                    wire:click="markTaken({{ $medicine->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="markTaken({{ $medicine->id }})"
                                    class="inline-flex items-center gap-2 rounded-full bg-emerald-500 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-colors hover:bg-emerald-600 disabled:opacity-60"
                                >
                                    <span>{{ __('app.actions.taken') }}</span>
                                </button>
                                <button
                                    wire:click="toggleHistory({{ $medicine->id }})"
                                    aria-label="{{ __('app.actions.view_history', ['name' => $medicine->name]) }}"
                                    title="{{ __('app.actions.history') }}"
                                    class="h-9 w-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 7v5l3 2M4 12a8 8 0 101.86-5.11"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 4v4h4"/>
                                    </svg>
                                </button>
                                <button
                                    wire:click="edit({{ $medicine->id }})"
                                    aria-label="{{ __('app.actions.edit_medicine', ['name' => $medicine->name]) }}"
                                    title="{{ __('app.actions.edit') }}"
                                    class="h-9 w-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <button
                                    wire:click="delete({{ $medicine->id }})"
                                    aria-label="{{ __('app.actions.delete_medicine', ['name' => $medicine->name]) }}"
                                    title="{{ __('app.actions.delete') }}"
                                    class="h-9 w-9 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-200 dark:hover:bg-rose-800 flex items-center justify-center transition-colors shrink-0"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @if(in_array($medicine->id, $historyOpen, true))
                            <div class="rounded-xl border border-slate-200/70 dark:border-slate-800/70 bg-white/70 dark:bg-slate-900/60 px-3 py-2 text-xs text-slate-600 dark:text-slate-300">
                                @if($medicine->notes)
                                    <div class="space-y-1">
                                        <p class="text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('app.medicines.notes_label') }}</p>
                                        <p>{{ $medicine->notes }}</p>
                                    </div>
                                @endif
                                <p class="text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400 {{ $medicine->notes ? 'mt-3' : '' }}">{{ __('app.medicines.history') }}</p>
                                <div class="mt-2 space-y-1">
                                    @forelse($medicine->doseLogs as $doseLog)
                                        <div class="flex items-center justify-between gap-2">
                                            <p>{{ __('app.medicines.history_entry', ['time' => $doseLog->taken_at->diffForHumans()]) }}</p>
                                            <button
                                                type="button"
                                                wire:click="deleteDoseLog({{ $doseLog->id }})"
                                                class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-300 dark:hover:text-rose-200"
                                            >
                                                {{ __('app.actions.remove') }}
                                            </button>
                                        </div>
                                    @empty
                                        <p class="text-slate-500 dark:text-slate-400">{{ __('app.medicines.history_empty') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-12">
                        <p class="text-slate-500 dark:text-slate-400">{{ __('app.medicines.empty') }}</p>
                    </div>
                @endforelse
            </div>
            @if($asNeededMedicines->isNotEmpty())
                <div class="mt-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ __('app.medicines.as_needed_section') }}</h3>
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('app.medicines.as_needed') }}</span>
                    </div>
                    <div class="space-y-4">
                        @foreach($asNeededMedicines as $medicine)
                            <div wire:key="medicine-as-needed-compact-{{ $medicine->id }}" class="app-card app-card-interactive p-4 space-y-4 min-w-0">
                                <div class="flex items-start gap-3 min-w-0">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-base font-semibold text-slate-900 dark:text-slate-100 truncate">{{ $medicine->name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">{{ $medicine->dosage }} · {{ __('app.medicines.as_needed') }}</p>
                                        @if($medicine->notes)
                                            <button
                                                type="button"
                                                wire:click="toggleHistory({{ $medicine->id }})"
                                                class="mt-1 block w-full min-w-0 overflow-hidden text-ellipsis whitespace-nowrap text-left text-xs text-slate-500 dark:text-slate-400"
                                            >
                                                <span class="font-semibold text-slate-600 dark:text-slate-300">{{ __('app.medicines.notes_label') }}</span>
                                                {{ $medicine->notes }}
                                            </button>
                                        @endif
                                    </div>
                                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">
                                        {{ __('app.medicines.as_needed') }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-3">
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $medicine->last_taken_at ? __('app.medicines.last_taken', ['time' => $medicine->last_taken_at->diffForHumans()]) : __('app.medicines.not_taken') }}
                                    </div>
                                    <div class="flex gap-2">
                                        <button
                                            wire:click="markTaken({{ $medicine->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="markTaken({{ $medicine->id }})"
                                            class="inline-flex items-center gap-2 rounded-full bg-emerald-500 text-white px-3 py-1.5 text-xs font-semibold shadow-sm transition-colors hover:bg-emerald-600 disabled:opacity-60"
                                        >
                                            <span>{{ __('app.actions.taken') }}</span>
                                        </button>
                                        <button
                                            wire:click="toggleHistory({{ $medicine->id }})"
                                            aria-label="{{ __('app.actions.view_history', ['name' => $medicine->name]) }}"
                                            title="{{ __('app.actions.history') }}"
                                            class="h-9 w-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 7v5l3 2M4 12a8 8 0 101.86-5.11"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 4v4h4"/>
                                            </svg>
                                        </button>
                                        <button
                                            wire:click="edit({{ $medicine->id }})"
                                            aria-label="{{ __('app.actions.edit_medicine', ['name' => $medicine->name]) }}"
                                            title="{{ __('app.actions.edit') }}"
                                            class="h-9 w-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            wire:click="delete({{ $medicine->id }})"
                                            aria-label="{{ __('app.actions.delete_medicine', ['name' => $medicine->name]) }}"
                                            title="{{ __('app.actions.delete') }}"
                                            class="h-9 w-9 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-200 dark:hover:bg-rose-800 flex items-center justify-center transition-colors shrink-0"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                @if(in_array($medicine->id, $historyOpen, true))
                                    <div class="rounded-xl border border-slate-200/70 dark:border-slate-800/70 bg-white/70 dark:bg-slate-900/60 px-3 py-2 text-xs text-slate-600 dark:text-slate-300">
                                        @if($medicine->notes)
                                            <div class="space-y-1">
                                                <p class="text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('app.medicines.notes_label') }}</p>
                                                <p>{{ $medicine->notes }}</p>
                                            </div>
                                        @endif
                                        <p class="text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400 {{ $medicine->notes ? 'mt-3' : '' }}">{{ __('app.medicines.history') }}</p>
                                        <div class="mt-2 space-y-1">
                                            @forelse($medicine->doseLogs as $doseLog)
                                                <div class="flex items-center justify-between gap-2">
                                                    <p>{{ __('app.medicines.history_entry', ['time' => $doseLog->taken_at->diffForHumans()]) }}</p>
                                                    <button
                                                        type="button"
                                                        wire:click="deleteDoseLog({{ $doseLog->id }})"
                                                        class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-300 dark:hover:text-rose-200"
                                                    >
                                                        {{ __('app.actions.remove') }}
                                                    </button>
                                                </div>
                                            @empty
                                                <p class="text-slate-500 dark:text-slate-400">{{ __('app.medicines.history_empty') }}</p>
                                            @endforelse
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @forelse($scheduledMedicines as $medicine)
                    @php
                        $nextDose = $medicine->next_dose_at;
                        $isDue = $medicine->is_active && $nextDose && $nextDose->lessThanOrEqualTo(now());
                        $statusLabel = $isDue
                            ? __('app.medicines.due_now')
                            : ($nextDose ? $nextDose->diffForHumans() : __('app.medicines.not_scheduled'));
                        $scheduleSummary = match ($medicine->schedule_type) {
                            'hours' => __('app.medicines.schedule_hours', ['hours' => $medicine->frequency_hours]),
                            'days' => __('app.medicines.schedule_days', [
                                'days' => $medicine->frequency_days,
                                'time' => $medicine->time_of_day ?? '--:--',
                            ]),
                            'weekdays' => __('app.medicines.schedule_weekdays', [
                                'days' => $medicine->weekdays ? implode(', ', $medicine->weekdays) : __('app.medicines.not_scheduled'),
                                'time' => $medicine->time_of_day ?? '--:--',
                            ]),
                            'times' => __('app.medicines.schedule_times', [
                                'times' => $medicine->times ? implode(', ', $medicine->times) : __('app.medicines.not_scheduled'),
                            ]),
                            'dates' => __('app.medicines.schedule_dates', [
                                'dates' => $medicine->dates ? implode(', ', $medicine->dates) : __('app.medicines.not_scheduled'),
                                'time' => $medicine->time_of_day ?? '--:--',
                            ]),
                            'as_needed' => __('app.medicines.as_needed'),
                            default => __('app.medicines.not_scheduled'),
                        };
                    @endphp
                    <div wire:key="medicine-grid-{{ $medicine->id }}" class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/90 dark:bg-slate-900/70 shadow-sm p-5 flex flex-col justify-between gap-6 app-card-interactive min-w-0">
                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('app.medicines.next_dose') }}</p>
                                    <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ $statusLabel }}</p>
                                </div>
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold {{ $isDue ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-200' : 'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-200' }}">
                                    {{ $medicine->is_active ? __('app.medicines.active') : __('app.medicines.paused') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="min-w-0">
                                    <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $medicine->name }}</p>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $medicine->dosage }} · {{ $scheduleSummary }}</p>
                                    @if($medicine->notes)
                                        <button
                                            type="button"
                                            wire:click="toggleHistory({{ $medicine->id }})"
                                            class="mt-1 block w-full min-w-0 overflow-hidden text-ellipsis whitespace-nowrap text-left text-xs text-slate-500 dark:text-slate-400"
                                        >
                                            <span class="font-semibold text-slate-600 dark:text-slate-300">{{ __('app.medicines.notes_label') }}</span>
                                            {{ $medicine->notes }}
                                        </button>
                                    @endif
                                </div>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $medicine->last_taken_at ? __('app.medicines.last_taken', ['time' => $medicine->last_taken_at->diffForHumans()]) : __('app.medicines.not_taken') }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                wire:click="markTaken({{ $medicine->id }})"
                                wire:loading.attr="disabled"
                                wire:target="markTaken({{ $medicine->id }})"
                                class="flex-1 inline-flex items-center justify-center gap-2 rounded-full bg-emerald-500 text-white px-4 py-2 text-sm font-semibold shadow-sm transition-colors hover:bg-emerald-600 disabled:opacity-60"
                            >
                                {{ __('app.actions.taken') }}
                            </button>
                            <button
                                wire:click="toggleHistory({{ $medicine->id }})"
                                aria-label="{{ __('app.actions.view_history', ['name' => $medicine->name]) }}"
                                title="{{ __('app.actions.history') }}"
                                class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 7v5l3 2M4 12a8 8 0 101.86-5.11"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 4v4h4"/>
                                </svg>
                            </button>
                            <button
                                wire:click="edit({{ $medicine->id }})"
                                aria-label="{{ __('app.actions.edit_medicine', ['name' => $medicine->name]) }}"
                                title="{{ __('app.actions.edit') }}"
                                class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </button>
                            <button
                                wire:click="delete({{ $medicine->id }})"
                                aria-label="{{ __('app.actions.delete_medicine', ['name' => $medicine->name]) }}"
                                title="{{ __('app.actions.delete') }}"
                                class="h-10 w-10 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-200 dark:hover:bg-rose-800 flex items-center justify-center transition-colors shrink-0"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                        @if(in_array($medicine->id, $historyOpen, true))
                            <div class="rounded-xl border border-slate-200/70 dark:border-slate-800/70 bg-white/70 dark:bg-slate-900/60 px-3 py-2 text-xs text-slate-600 dark:text-slate-300">
                                @if($medicine->notes)
                                    <div class="space-y-1">
                                        <p class="text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('app.medicines.notes_label') }}</p>
                                        <p>{{ $medicine->notes }}</p>
                                    </div>
                                @endif
                                <p class="text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400 {{ $medicine->notes ? 'mt-3' : '' }}">{{ __('app.medicines.history') }}</p>
                                <div class="mt-2 space-y-1">
                                    @forelse($medicine->doseLogs as $doseLog)
                                        <div class="flex items-center justify-between gap-2">
                                            <p>{{ __('app.medicines.history_entry', ['time' => $doseLog->taken_at->diffForHumans()]) }}</p>
                                            <button
                                                type="button"
                                                wire:click="deleteDoseLog({{ $doseLog->id }})"
                                                class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-300 dark:hover:text-rose-200"
                                            >
                                                {{ __('app.actions.remove') }}
                                            </button>
                                        </div>
                                    @empty
                                        <p class="text-slate-500 dark:text-slate-400">{{ __('app.medicines.history_empty') }}</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-slate-500 dark:text-slate-400">{{ __('app.medicines.empty') }}</p>
                    </div>
                @endforelse
            </div>
            @if($asNeededMedicines->isNotEmpty())
                <div class="mt-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">{{ __('app.medicines.as_needed_section') }}</h3>
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('app.medicines.as_needed') }}</span>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($asNeededMedicines as $medicine)
                            <div wire:key="medicine-as-needed-{{ $medicine->id }}" class="rounded-2xl border border-slate-200/70 dark:border-slate-800/70 bg-white/90 dark:bg-slate-900/70 shadow-sm p-5 flex flex-col justify-between gap-6 app-card-interactive min-w-0">
                                <div class="space-y-3">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('app.medicines.next_dose') }}</p>
                                            <p class="text-2xl font-semibold text-slate-900 dark:text-white">{{ __('app.medicines.as_needed') }}</p>
                                        </div>
                                        <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold bg-slate-100 text-slate-700 dark:bg-slate-900/40 dark:text-slate-200">
                                            {{ __('app.medicines.as_needed') }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="min-w-0">
                                            <p class="text-lg font-semibold text-slate-900 dark:text-slate-100">{{ $medicine->name }}</p>
                                            <p class="text-sm text-slate-500 dark:text-slate-400">{{ $medicine->dosage }} · {{ __('app.medicines.as_needed') }}</p>
                                            @if($medicine->notes)
                                                <button
                                                    type="button"
                                                    wire:click="toggleHistory({{ $medicine->id }})"
                                                    class="mt-1 block w-full min-w-0 overflow-hidden text-ellipsis whitespace-nowrap text-left text-xs text-slate-500 dark:text-slate-400"
                                                >
                                                    <span class="font-semibold text-slate-600 dark:text-slate-300">{{ __('app.medicines.notes_label') }}</span>
                                                    {{ $medicine->notes }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $medicine->last_taken_at ? __('app.medicines.last_taken', ['time' => $medicine->last_taken_at->diffForHumans()]) : __('app.medicines.not_taken') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button
                                        wire:click="markTaken({{ $medicine->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="markTaken({{ $medicine->id }})"
                                        class="flex-1 inline-flex items-center justify-center gap-2 rounded-full bg-emerald-500 text-white px-4 py-2 text-sm font-semibold shadow-sm transition-colors hover:bg-emerald-600 disabled:opacity-60"
                                    >
                                        {{ __('app.actions.taken') }}
                                    </button>
                                    <button
                                        wire:click="toggleHistory({{ $medicine->id }})"
                                        aria-label="{{ __('app.actions.view_history', ['name' => $medicine->name]) }}"
                                        title="{{ __('app.actions.history') }}"
                                        class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M12 7v5l3 2M4 12a8 8 0 101.86-5.11"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M4 4v4h4"/>
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="edit({{ $medicine->id }})"
                                        aria-label="{{ __('app.actions.edit_medicine', ['name' => $medicine->name]) }}"
                                        title="{{ __('app.actions.edit') }}"
                                        class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center transition-colors shrink-0"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="delete({{ $medicine->id }})"
                                        aria-label="{{ __('app.actions.delete_medicine', ['name' => $medicine->name]) }}"
                                        title="{{ __('app.actions.delete') }}"
                                        class="h-10 w-10 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-200 dark:hover:bg-rose-800 flex items-center justify-center transition-colors shrink-0"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                                @if(in_array($medicine->id, $historyOpen, true))
                                    <div class="rounded-xl border border-slate-200/70 dark:border-slate-800/70 bg-white/70 dark:bg-slate-900/60 px-3 py-2 text-xs text-slate-600 dark:text-slate-300">
                                        @if($medicine->notes)
                                            <div class="space-y-1">
                                                <p class="text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400">{{ __('app.medicines.notes_label') }}</p>
                                                <p>{{ $medicine->notes }}</p>
                                            </div>
                                        @endif
                                        <p class="text-[11px] uppercase tracking-widest text-slate-500 dark:text-slate-400 {{ $medicine->notes ? 'mt-3' : '' }}">{{ __('app.medicines.history') }}</p>
                                        <div class="mt-2 space-y-1">
                                            @forelse($medicine->doseLogs as $doseLog)
                                                <div class="flex items-center justify-between gap-2">
                                                    <p>{{ __('app.medicines.history_entry', ['time' => $doseLog->taken_at->diffForHumans()]) }}</p>
                                                    <button
                                                        type="button"
                                                        wire:click="deleteDoseLog({{ $doseLog->id }})"
                                                        class="text-[11px] font-semibold text-rose-600 hover:text-rose-700 dark:text-rose-300 dark:hover:text-rose-200"
                                                    >
                                                        {{ __('app.actions.remove') }}
                                                    </button>
                                                </div>
                                            @empty
                                                <p class="text-slate-500 dark:text-slate-400">{{ __('app.medicines.history_empty') }}</p>
                                            @endforelse
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </section>
</div>
