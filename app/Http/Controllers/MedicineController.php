<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMedicineRequest;
use App\Http\Requests\UpdateMedicineRequest;
use App\Models\Medicine;
use App\Models\MedicineDoseLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use Native\Mobile\Facades\Dialog;
use Native\Mobile\Facades\SecureStorage;

class MedicineController extends Controller
{
    public function index(Request $request): Response
    {
        $statusFilter = $request->string('status', 'active')->toString();
        if (! in_array($statusFilter, ['active', 'paused', 'all'], true)) {
            $statusFilter = 'active';
        }

        $sortOrder = $request->string('sort', 'soonest')->toString();
        if (! in_array($sortOrder, ['soonest', 'latest'], true)) {
            $sortOrder = 'soonest';
        }

        $historyLimits = $this->resolveHistoryLimits($request->input('history', []));
        $historyOpen = $this->resolveHistoryOpen($request->input('open', []));

        $now = now();

        $medicines = Medicine::query()
            ->withCount('doseLogs')
            ->with(['doseLogs' => fn ($query) => $query->latest('taken_at')->limit(5)])
            ->when($statusFilter === 'active', fn ($query) => $query->where('is_active', true))
            ->when($statusFilter === 'paused', fn ($query) => $query->where('is_active', false))
            ->when(
                $sortOrder === 'latest',
                fn ($query) => $query->orderByRaw('next_dose_at is null, next_dose_at desc')
            )
            ->when(
                $sortOrder === 'soonest',
                fn ($query) => $query->orderByRaw('next_dose_at is null, next_dose_at asc')
            )
            ->get();

        $medicines->each(function (Medicine $medicine) use ($historyLimits, $historyOpen): void {
            $limit = $historyLimits[$medicine->id] ?? 5;

            if ($limit > 5 && in_array($medicine->id, $historyOpen, true)) {
                $logs = $medicine->doseLogs()
                    ->latest('taken_at')
                    ->limit($limit)
                    ->get();

                $medicine->setRelation('doseLogs', $logs);
            }
        });

        $dueNowCount = $medicines->filter(fn (Medicine $medicine) => $this->isDueNow($medicine, $now))->count();
        $upNextCount = $medicines->filter(fn (Medicine $medicine) => $this->isDueSoon($medicine, $now))->count();
        $takenTodayCount = MedicineDoseLog::query()
            ->whereDate('taken_at', $now->toDateString())
            ->whereHas('medicine', fn ($query) => $query->where('is_active', true))
            ->count();

        $scheduledMedicines = $medicines->reject(fn (Medicine $medicine) => $medicine->schedule_type === 'as_needed');
        $asNeededMedicines = $medicines->filter(fn (Medicine $medicine) => $medicine->schedule_type === 'as_needed');

        return Inertia::render('Medicines/Index', [
            'scheduledMedicines' => $scheduledMedicines->map(fn (Medicine $medicine) => $this->mapMedicine($medicine, $now)),
            'asNeededMedicines' => $asNeededMedicines->map(fn (Medicine $medicine) => $this->mapMedicine($medicine, $now)),
            'dueNowCount' => $dueNowCount,
            'upNextCount' => $upNextCount,
            'takenTodayCount' => $takenTodayCount,
            'compactView' => $this->resolveCompactView(),
            'title' => __('app.titles.medicines'),
            'statusFilter' => $statusFilter,
            'sortOrder' => $sortOrder,
            'historyOpen' => array_values($historyOpen),
            'historyLimits' => $historyLimits,
            'trackedLabel' => trans_choice('app.medicines.tracked', $scheduledMedicines->count(), [
                'count' => $scheduledMedicines->count(),
            ]),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Medicines/Form', [
            'medicine' => null,
            'title' => __('app.titles.add_medicine'),
        ]);
    }

    public function edit(Medicine $medicine): Response
    {
        return Inertia::render('Medicines/Form', [
            'medicine' => [
                'id' => $medicine->id,
                'name' => $medicine->name,
                'dosage' => $medicine->dosage,
                'scheduleType' => $medicine->schedule_type,
                'frequencyHours' => (string) $medicine->frequency_hours,
                'frequencyDays' => (string) $medicine->frequency_days,
                'weekdays' => $medicine->weekdays ?: [],
                'timeOfDay' => $medicine->time_of_day,
                'timesInput' => $medicine->times ? implode(', ', $medicine->times) : '',
                'datesInput' => $medicine->dates ? implode(', ', $medicine->dates) : '',
                'notes' => $medicine->notes,
                'nextDoseAt' => $medicine->next_dose_at?->format('Y-m-d\TH:i'),
                'isActive' => $medicine->is_active,
            ],
            'title' => __('app.titles.edit_medicine'),
        ]);
    }

    public function store(StoreMedicineRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $result = $this->buildScheduleData($data);
        if ($result instanceof RedirectResponse) {
            return $result;
        }

        Medicine::create($result + [
            'is_active' => true,
        ]);

        Dialog::toast(__('app.toasts.medicine_added'));

        return redirect()->route('medicines');
    }

    public function update(UpdateMedicineRequest $request, Medicine $medicine): RedirectResponse
    {
        $data = $request->validated();

        $result = $this->buildScheduleData($data);
        if ($result instanceof RedirectResponse) {
            return $result;
        }

        $medicine->update($result + [
            'is_active' => $data['is_active'],
        ]);

        Dialog::toast(__('app.toasts.medicine_updated'));

        return redirect()->route('medicines');
    }

    public function destroy(Medicine $medicine): RedirectResponse
    {
        $medicine->delete();

        Dialog::toast(__('app.toasts.medicine_deleted'));

        return redirect()->route('medicines');
    }

    public function markTaken(Medicine $medicine): RedirectResponse
    {
        if (! $medicine->is_active) {
            return redirect()->route('medicines');
        }

        $now = now();

        $medicine->update([
            'last_taken_at' => $now,
            'next_dose_at' => $medicine->computeNextDoseAt($now),
        ]);

        $medicine->doseLogs()->create([
            'taken_at' => $now,
        ]);

        Dialog::toast(__('app.toasts.medicine_taken'));

        return redirect()->route('medicines');
    }

    public function destroyDoseLog(MedicineDoseLog $doseLog): RedirectResponse
    {
        $doseLog->delete();

        return redirect()->route('medicines');
    }

    /**
     * @return array<string, mixed>|\Illuminate\Http\RedirectResponse
     */
    protected function buildScheduleData(array $data): array|RedirectResponse
    {
        $scheduleType = $data['schedule_type'];
        $timesInput = $data['times_input'] ?? '';
        $datesInput = $data['dates_input'] ?? '';

        $times = $this->parseTimeList($timesInput);
        $dates = $this->parseDateList($datesInput);

        if ($scheduleType === 'times' && $times === []) {
            return back()->withErrors(['times_input' => __('app.form.times_invalid')])->withInput();
        }

        if ($scheduleType === 'dates' && $dates === []) {
            return back()->withErrors(['dates_input' => __('app.form.dates_invalid')])->withInput();
        }

        $nextDoseAt = null;

        if ($scheduleType !== 'as_needed') {
            if (! empty($data['next_dose_at'])) {
                $nextDoseAt = Carbon::parse($data['next_dose_at']);
            } else {
                $nextDoseAt = $this->buildScheduleModel($data, $times, $dates)->computeNextDoseAt(now());
            }
        }

        return [
            'name' => $data['name'],
            'dosage' => $data['dosage'],
            'frequency_hours' => $scheduleType === 'hours' ? (int) ($data['frequency_hours'] ?? 0) : 0,
            'frequency_days' => $scheduleType === 'days' ? (int) ($data['frequency_days'] ?? 0) : 0,
            'schedule_type' => $scheduleType,
            'weekdays' => $scheduleType === 'weekdays' ? ($data['weekdays'] ?? []) : null,
            'times' => $scheduleType === 'times' ? $times : null,
            'dates' => $scheduleType === 'dates' ? $dates : null,
            'time_of_day' => in_array($scheduleType, ['days', 'weekdays', 'dates'], true) ? ($data['time_of_day'] ?? null) : null,
            'notes' => $data['notes'] ?? null,
            'next_dose_at' => $nextDoseAt,
        ];
    }

    protected function resolveCompactView(): bool
    {
        try {
            $storedCompactView = SecureStorage::get('compact_view');
        } catch (\Exception $e) {
            $storedCompactView = null;
        }

        return $storedCompactView === 'true';
    }

    /**
     * @param  array<int, mixed>  $open
     * @return array<int, int>
     */
    protected function resolveHistoryOpen(array $open): array
    {
        return array_values(array_map('intval', array_filter($open, fn ($id) => is_numeric($id))));
    }

    /**
     * @param  array<string, mixed>  $history
     * @return array<int, int>
     */
    protected function resolveHistoryLimits(array $history): array
    {
        $limits = [];

        foreach ($history as $id => $limit) {
            if (! is_numeric($id) || ! is_numeric($limit)) {
                continue;
            }

            $limits[(int) $id] = max(5, (int) $limit);
        }

        return $limits;
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapMedicine(Medicine $medicine, Carbon $now): array
    {
        $nextDoseAt = $medicine->next_dose_at;
        $isDue = $medicine->is_active && $nextDoseAt && $nextDoseAt->lessThanOrEqualTo($now);
        $statusLabel = $isDue
            ? __('app.medicines.due_now')
            : ($nextDoseAt ? $nextDoseAt->diffForHumans() : __('app.medicines.not_scheduled'));

        return [
            'id' => $medicine->id,
            'name' => $medicine->name,
            'dosage' => $medicine->dosage,
            'scheduleType' => $medicine->schedule_type,
            'frequencyHours' => (int) $medicine->frequency_hours,
            'frequencyDays' => (int) $medicine->frequency_days,
            'weekdays' => $medicine->weekdays ?: [],
            'times' => $medicine->times ?: [],
            'dates' => $medicine->dates ?: [],
            'timeOfDay' => $medicine->time_of_day,
            'notes' => $medicine->notes,
            'nextDoseAt' => $medicine->next_dose_at?->toIso8601String(),
            'lastTakenAt' => $medicine->last_taken_at?->toIso8601String(),
            'isActive' => $medicine->is_active,
            'statusLabel' => $statusLabel,
            'isDue' => $isDue,
            'doseLogs' => $medicine->doseLogs->map(fn (MedicineDoseLog $log) => [
                'id' => $log->id,
                'takenAt' => $log->taken_at->toIso8601String(),
                'takenAtLabel' => $log->taken_at->translatedFormat('M j, Y H:i'),
            ]),
            'doseLogsCount' => $medicine->dose_logs_count,
        ];
    }

    protected function isDueNow(Medicine $medicine, Carbon $now): bool
    {
        return $medicine->is_active
            && $medicine->next_dose_at !== null
            && $medicine->next_dose_at->lessThanOrEqualTo($now);
    }

    protected function isDueSoon(Medicine $medicine, Carbon $now): bool
    {
        return $medicine->is_active
            && $medicine->next_dose_at !== null
            && $medicine->next_dose_at->greaterThan($now)
            && $medicine->next_dose_at->lessThanOrEqualTo($now->copy()->addDay());
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

    protected function buildScheduleModel(array $data, array $times, array $dates): Medicine
    {
        return new Medicine([
            'schedule_type' => $data['schedule_type'],
            'frequency_hours' => $data['schedule_type'] === 'hours' ? (int) ($data['frequency_hours'] ?? 0) : 0,
            'frequency_days' => $data['schedule_type'] === 'days' ? (int) ($data['frequency_days'] ?? 0) : 0,
            'weekdays' => $data['schedule_type'] === 'weekdays' ? ($data['weekdays'] ?? []) : null,
            'times' => $data['schedule_type'] === 'times' ? $times : null,
            'dates' => $data['schedule_type'] === 'dates' ? $dates : null,
            'time_of_day' => in_array($data['schedule_type'], ['days', 'weekdays', 'dates'], true) ? ($data['time_of_day'] ?? null) : null,
        ]);
    }
}
