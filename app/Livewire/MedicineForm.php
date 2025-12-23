<?php

namespace App\Livewire;

use App\Models\Medicine;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Native\Mobile\Facades\Dialog;

#[Layout('layouts.app')]
class MedicineForm extends Component
{
    public ?Medicine $medicine = null;

    public string $name = '';

    public string $dosage = '';

    public string $frequencyHours = '';

    public string $frequencyDays = '';

    /**
     * @var array<int, string>
     */
    public array $weekdays = [];

    public string $timeOfDay = '';

    public string $timesInput = '';

    public string $datesInput = '';

    public ?string $notes = null;

    public ?string $nextDoseAt = null;

    public string $title = '';

    public bool $isActive = true;

    public string $scheduleType = 'hours';

    public function mount(?int $id = null): void
    {
        if ($id || request()->has('id')) {
            $medicineId = $id ?? (int) request()->query('id');
            $this->loadMedicine($medicineId);
        }

        $this->title = $this->medicine
            ? __('app.titles.edit_medicine')
            : __('app.titles.add_medicine');
    }

    protected function loadMedicine(int $id): void
    {
        $this->medicine = Medicine::findOrFail($id);
        $this->name = $this->medicine->name;
        $this->dosage = $this->medicine->dosage;
        $this->frequencyHours = (string) $this->medicine->frequency_hours;
        $this->frequencyDays = (string) $this->medicine->frequency_days;
        $this->scheduleType = $this->medicine->schedule_type;
        $this->weekdays = $this->medicine->weekdays ?: [];
        $this->timeOfDay = $this->medicine->time_of_day ?? '';
        $this->timesInput = $this->medicine->times ? implode(', ', $this->medicine->times) : '';
        $this->datesInput = $this->medicine->dates ? implode(', ', $this->medicine->dates) : '';
        $this->notes = $this->medicine->notes;
        $this->nextDoseAt = $this->medicine->next_dose_at?->format('Y-m-d\TH:i');
        $this->isActive = $this->medicine->is_active;
    }

    protected function rules(): array
    {
        $frequencyHoursRules = $this->scheduleType === 'hours'
            ? ['required', 'integer', 'min:1', 'max:168']
            : ['nullable', 'integer', 'min:0', 'max:168'];

        $frequencyDaysRules = $this->scheduleType === 'days'
            ? ['required', 'integer', 'min:1', 'max:365']
            : ['nullable', 'integer', 'min:0', 'max:365'];

        $weekdaysRules = $this->scheduleType === 'weekdays'
            ? ['required', 'array', 'min:1']
            : ['nullable', 'array'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'dosage' => ['required', 'string', 'max:255'],
            'scheduleType' => ['required', 'in:hours,days,weekdays,times,dates,as_needed'],
            'frequencyHours' => $frequencyHoursRules,
            'frequencyDays' => $frequencyDaysRules,
            'weekdays' => $weekdaysRules,
            'weekdays.*' => ['in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'],
            'timeOfDay' => [
                \Illuminate\Validation\Rule::requiredIf(in_array($this->scheduleType, ['days', 'weekdays', 'dates'], true)),
                'nullable',
                'date_format:H:i',
            ],
            'timesInput' => [
                \Illuminate\Validation\Rule::requiredIf($this->scheduleType === 'times'),
                'nullable',
                'string',
            ],
            'datesInput' => [
                \Illuminate\Validation\Rule::requiredIf($this->scheduleType === 'dates'),
                'nullable',
                'string',
            ],
            'notes' => ['nullable', 'string', 'max:1000'],
            'nextDoseAt' => ['nullable', 'date'],
            'isActive' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        $times = $this->parseTimeList($this->timesInput);
        $dates = $this->parseDateList($this->datesInput);

        if ($this->scheduleType === 'times' && $times === []) {
            $this->addError('timesInput', __('app.form.times_invalid'));

            return;
        }

        if ($this->scheduleType === 'dates' && $dates === []) {
            $this->addError('datesInput', __('app.form.dates_invalid'));

            return;
        }

        $nextDoseAt = $this->nextDoseAt
            ? Carbon::parse($this->nextDoseAt)
            : $this->buildScheduleModel($times, $dates)->computeNextDoseAt(now());

        if ($this->medicine) {
            $this->medicine->update([
                'name' => $this->name,
                'dosage' => $this->dosage,
                'frequency_hours' => $this->scheduleType === 'hours' ? (int) $this->frequencyHours : 0,
                'frequency_days' => $this->scheduleType === 'days' ? (int) $this->frequencyDays : 0,
                'schedule_type' => $this->scheduleType,
                'weekdays' => $this->scheduleType === 'weekdays' ? $this->weekdays : null,
                'times' => $this->scheduleType === 'times' ? $times : null,
                'dates' => $this->scheduleType === 'dates' ? $dates : null,
                'time_of_day' => in_array($this->scheduleType, ['days', 'weekdays', 'dates'], true) ? $this->timeOfDay : null,
                'notes' => $this->notes ?: null,
                'next_dose_at' => $nextDoseAt,
                'is_active' => $this->isActive,
            ]);

            Dialog::toast(__('app.toasts.medicine_updated'));
        } else {
            Medicine::create([
                'name' => $this->name,
                'dosage' => $this->dosage,
                'frequency_hours' => $this->scheduleType === 'hours' ? (int) $this->frequencyHours : 0,
                'frequency_days' => $this->scheduleType === 'days' ? (int) $this->frequencyDays : 0,
                'schedule_type' => $this->scheduleType,
                'weekdays' => $this->scheduleType === 'weekdays' ? $this->weekdays : null,
                'times' => $this->scheduleType === 'times' ? $times : null,
                'dates' => $this->scheduleType === 'dates' ? $dates : null,
                'time_of_day' => in_array($this->scheduleType, ['days', 'weekdays', 'dates'], true) ? $this->timeOfDay : null,
                'notes' => $this->notes ?: null,
                'next_dose_at' => $nextDoseAt,
                'is_active' => true,
            ]);

            Dialog::toast(__('app.toasts.medicine_added'));
        }

        $this->reset([
            'name',
            'dosage',
            'frequencyHours',
            'frequencyDays',
            'weekdays',
            'timeOfDay',
            'timesInput',
            'datesInput',
            'scheduleType',
            'notes',
            'nextDoseAt',
            'isActive',
        ]);
        $this->medicine = null;

        $this->dispatch('medicine-saved');

        $this->redirect(route('medicines'), navigate: true);
    }

    public function cancel(): void
    {
        $this->reset([
            'name',
            'dosage',
            'frequencyHours',
            'frequencyDays',
            'weekdays',
            'timeOfDay',
            'timesInput',
            'datesInput',
            'scheduleType',
            'notes',
            'nextDoseAt',
            'isActive',
        ]);
        $this->medicine = null;

        $this->redirect(route('medicines'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.medicine-form', [
            'title' => $this->title,
        ]);
    }

    /**
     * @return array<int, string>
     */
    protected function parseTimeList(string $input): array
    {
        $times = array_values(array_filter(array_map('trim', explode(',', $input))));

        return array_values(array_filter($times, fn (string $time) => preg_match('/^\d{2}:\d{2}$/', $time) === 1));
    }

    /**
     * @return array<int, string>
     */
    protected function parseDateList(string $input): array
    {
        $dates = array_values(array_filter(array_map('trim', explode(',', $input))));

        return array_values(array_filter($dates, fn (string $date) => preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) === 1));
    }

    protected function buildScheduleModel(array $times, array $dates): Medicine
    {
        return new Medicine([
            'schedule_type' => $this->scheduleType,
            'frequency_hours' => $this->scheduleType === 'hours' ? (int) $this->frequencyHours : 0,
            'frequency_days' => $this->scheduleType === 'days' ? (int) $this->frequencyDays : 0,
            'weekdays' => $this->scheduleType === 'weekdays' ? $this->weekdays : null,
            'times' => $this->scheduleType === 'times' ? $times : null,
            'dates' => $this->scheduleType === 'dates' ? $dates : null,
            'time_of_day' => in_array($this->scheduleType, ['days', 'weekdays', 'dates'], true) ? $this->timeOfDay : null,
        ]);
    }
}
