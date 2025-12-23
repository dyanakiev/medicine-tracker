<div class="space-y-8">
    <header class="space-y-4 animate-fade-up">
        <div class="inline-flex items-center gap-2 rounded-full border border-cyan-200/70 bg-cyan-100/70 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-cyan-700 dark:border-cyan-500/40 dark:bg-cyan-500/10 dark:text-cyan-200">
            <span class="h-1.5 w-1.5 rounded-full bg-cyan-500"></span>
            {{ $medicine ? __('app.badges.update') : __('app.badges.create') }}
        </div>
        <h1 class="text-3xl font-semibold tracking-tight text-slate-900 dark:text-white">
            {{ $medicine ? __('app.titles.edit_medicine') : __('app.titles.add_medicine') }}
        </h1>
        <p class="text-slate-600 dark:text-slate-400">
            {{ $medicine ? __('app.descriptions.medicine_edit') : __('app.descriptions.medicine_add') }}
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
                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                    placeholder="{{ __('app.form.name_placeholder') }}"
                >
                @error('name')
                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="dosage" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    {{ __('app.form.dosage') }}
                </label>
                <input
                    type="text"
                    id="dosage"
                    wire:model="dosage"
                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                    placeholder="{{ __('app.form.dosage_placeholder') }}"
                >
                @error('dosage')
                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="frequencyHours" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    {{ __('app.form.schedule_type') }}
                </label>
                <select
                    id="scheduleType"
                    wire:model.live="scheduleType"
                    class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2.5 text-sm text-slate-900 dark:text-slate-100 shadow-sm focus:outline-none focus:ring-2 focus:ring-cyan-500"
                >
                    <option value="hours">{{ __('app.form.schedule_hours') }}</option>
                    <option value="days">{{ __('app.form.schedule_days') }}</option>
                    <option value="weekdays">{{ __('app.form.schedule_weekdays') }}</option>
                    <option value="times">{{ __('app.form.schedule_times') }}</option>
                    <option value="dates">{{ __('app.form.schedule_dates') }}</option>
                    <option value="as_needed">{{ __('app.form.schedule_as_needed') }}</option>
                </select>
                @error('scheduleType')
                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            @if($scheduleType === 'as_needed')
                <div class="rounded-2xl border border-slate-200/80 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-900/50 px-4 py-3 text-sm text-slate-600 dark:text-slate-300">
                    {{ __('app.form.as_needed_help') }}
                </div>
            @endif

            @if($scheduleType === 'hours')
                <div>
                    <label for="frequencyHours" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        {{ __('app.form.frequency_hours') }}
                    </label>
                    <input
                        type="number"
                        id="frequencyHours"
                        wire:model="frequencyHours"
                        min="1"
                        max="168"
                        class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                        placeholder="{{ __('app.form.frequency_hours_placeholder') }}"
                    >
                    @error('frequencyHours')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            @if($scheduleType === 'days')
                <div class="space-y-3">
                    <div>
                        <label for="frequencyDays" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('app.form.frequency_days') }}
                        </label>
                        <input
                            type="number"
                            id="frequencyDays"
                            wire:model="frequencyDays"
                            min="1"
                            max="365"
                            class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            placeholder="{{ __('app.form.frequency_days_placeholder') }}"
                        >
                        @error('frequencyDays')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="timeOfDay" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('app.form.time_of_day') }}
                        </label>
                        <input
                            type="time"
                            id="timeOfDay"
                            wire:model="timeOfDay"
                            class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                        >
                        @error('timeOfDay')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif

            @if($scheduleType === 'weekdays')
                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ __('app.form.weekdays') }}</p>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day)
                                <label class="inline-flex items-center gap-2 rounded-full border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-200">
                                    <input type="checkbox" wire:model.live="weekdays" value="{{ $day }}" class="rounded border-slate-300 text-cyan-600 focus:ring-cyan-500">
                                    {{ __('app.form.weekday_'.$day) }}
                                </label>
                            @endforeach
                        </div>
                        @error('weekdays')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="timeOfDay" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('app.form.time_of_day') }}
                        </label>
                        <input
                            type="time"
                            id="timeOfDay"
                            wire:model="timeOfDay"
                            class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                        >
                        @error('timeOfDay')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif

            @if($scheduleType === 'times')
                <div>
                    <label for="timesInput" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                        {{ __('app.form.times') }}
                    </label>
                    <input
                        type="text"
                        id="timesInput"
                        wire:model="timesInput"
                        class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                        placeholder="{{ __('app.form.times_placeholder') }}"
                    >
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('app.form.times_help') }}</p>
                    @error('timesInput')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            @if($scheduleType === 'dates')
                <div class="space-y-3">
                    <div>
                        <label for="datesInput" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('app.form.dates') }}
                        </label>
                        <input
                            type="text"
                            id="datesInput"
                            wire:model="datesInput"
                            class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            placeholder="{{ __('app.form.dates_placeholder') }}"
                        >
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('app.form.dates_help') }}</p>
                        @error('datesInput')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="timeOfDay" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                            {{ __('app.form.time_of_day') }}
                        </label>
                        <input
                            type="time"
                            id="timeOfDay"
                            wire:model="timeOfDay"
                            class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                        >
                        @error('timeOfDay')
                            <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif

            <div>
                <label for="nextDoseAt" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    {{ __('app.form.next_dose') }}
                </label>
                <input
                    type="datetime-local"
                    id="nextDoseAt"
                    wire:model="nextDoseAt"
                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                >
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('app.form.next_dose_help') }}</p>
                @error('nextDoseAt')
                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">
                    {{ __('app.form.notes') }}
                </label>
                <textarea
                    id="notes"
                    wire:model="notes"
                    rows="3"
                    class="w-full px-4 py-2.5 border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                    placeholder="{{ __('app.form.notes_placeholder') }}"
                ></textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            @if($medicine)
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
                class="w-full px-4 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white font-medium rounded-xl transition-colors shadow-sm"
            >
                {{ $medicine ? __('app.form.submit_update') : __('app.form.submit_add') }}
            </button>

            @if($medicine)
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
