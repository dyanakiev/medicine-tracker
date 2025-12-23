<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Medicine extends Model
{
    /** @use HasFactory<\Database\Factories\MedicineFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'dosage',
        'frequency_hours',
        'frequency_days',
        'schedule_type',
        'weekdays',
        'times',
        'dates',
        'time_of_day',
        'notes',
        'next_dose_at',
        'last_taken_at',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'frequency_hours' => 'integer',
            'frequency_days' => 'integer',
            'weekdays' => 'array',
            'times' => 'array',
            'dates' => 'array',
            'next_dose_at' => 'datetime',
            'last_taken_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function computeNextDoseAt(Carbon $from): ?Carbon
    {
        return match ($this->schedule_type) {
            'hours' => $this->frequency_hours > 0 ? $from->copy()->addHours($this->frequency_hours) : null,
            'days' => $this->nextDaysDose($from),
            'weekdays' => $this->nextWeekdayDose($from),
            'times' => $this->nextTimeDose($from),
            'dates' => $this->nextDateDose($from),
            'as_needed' => null,
            default => null,
        };
    }

    public function doseLogs(): HasMany
    {
        return $this->hasMany(MedicineDoseLog::class);
    }

    protected function nextWeekdayDose(Carbon $from): ?Carbon
    {
        $weekdays = $this->weekdays ?: [];
        $timeOfDay = $this->time_of_day;

        if ($weekdays === [] || ! is_string($timeOfDay) || $timeOfDay === '') {
            return null;
        }

        $weekdayMap = [
            'Mon' => 1,
            'Tue' => 2,
            'Wed' => 3,
            'Thu' => 4,
            'Fri' => 5,
            'Sat' => 6,
            'Sun' => 7,
        ];

        $targetDays = array_values(array_filter($weekdays, fn ($day) => isset($weekdayMap[$day])));

        if ($targetDays === []) {
            return null;
        }

        $candidate = $from->copy();

        for ($i = 0; $i <= 7; $i++) {
            $dayKey = array_search($candidate->dayOfWeekIso, $weekdayMap, true);

            if ($dayKey !== false && in_array($dayKey, $targetDays, true)) {
                [$hour, $minute] = array_map('intval', explode(':', $timeOfDay));
                $scheduled = $candidate->copy()->setTime($hour, $minute);

                if ($scheduled->greaterThan($from)) {
                    return $scheduled;
                }
            }

            $candidate->addDay();
        }

        return null;
    }

    protected function nextDaysDose(Carbon $from): ?Carbon
    {
        $timeOfDay = $this->time_of_day;

        if ($this->frequency_days <= 0 || ! is_string($timeOfDay) || $timeOfDay === '') {
            return null;
        }

        [$hour, $minute] = array_map('intval', explode(':', $timeOfDay));

        return $from->copy()->addDays($this->frequency_days)->setTime($hour, $minute);
    }

    protected function nextTimeDose(Carbon $from): ?Carbon
    {
        $times = $this->times ?: [];

        if ($times === []) {
            return null;
        }

        $candidates = [];

        foreach ($times as $time) {
            if (! is_string($time) || $time === '') {
                continue;
            }

            [$hour, $minute] = array_map('intval', explode(':', $time));
            $candidates[] = $from->copy()->setTime($hour, $minute);
        }

        $next = collect($candidates)->filter(fn (Carbon $time) => $time->greaterThan($from))->sort()->first();

        if ($next instanceof Carbon) {
            return $next;
        }

        $tomorrow = $from->copy()->addDay()->startOfDay();

        $nextTomorrow = collect($candidates)
            ->map(fn (Carbon $time) => $tomorrow->copy()->setTime($time->hour, $time->minute))
            ->sort()
            ->first();

        return $nextTomorrow instanceof Carbon ? $nextTomorrow : null;
    }

    protected function nextDateDose(Carbon $from): ?Carbon
    {
        $dates = $this->dates ?: [];
        $timeOfDay = $this->time_of_day;

        if ($dates === [] || ! is_string($timeOfDay) || $timeOfDay === '') {
            return null;
        }

        [$hour, $minute] = array_map('intval', explode(':', $timeOfDay));

        $next = collect($dates)
            ->filter(fn ($date) => is_string($date) && $date !== '')
            ->map(fn ($date) => Carbon::parse($date)->setTime($hour, $minute))
            ->filter(fn (Carbon $dateTime) => $dateTime->greaterThan($from))
            ->sort()
            ->first();

        return $next instanceof Carbon ? $next : null;
    }
}
