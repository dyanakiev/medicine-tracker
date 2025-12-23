<?php

namespace App\Livewire;

use App\Models\Medicine;
use App\Models\MedicineDoseLog;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Native\Mobile\Attributes\OnNative;
use Native\Mobile\Events\Alert\ButtonPressed;
use Native\Mobile\Facades\Dialog;
use Native\Mobile\Facades\SecureStorage;

#[Layout('layouts.app')]
class MedicineList extends Component
{
    public string $sortOrder = 'soonest';

    public bool $compactView = false;

    public string $statusFilter = 'active';

    public bool $filtersOpen = false;

    /**
     * @var array<int, int>
     */
    public array $historyOpen = [];

    public function mount(): void
    {
        try {
            $this->compactView = SecureStorage::get('compact_view') === 'true';
        } catch (\Exception $e) {
            $this->compactView = false;
        }
    }

    #[On('medicine-saved')]
    public function refresh(): void
    {
        // Component will automatically re-render when medicines change
    }

    public function sortBy(string $order): void
    {
        $this->sortOrder = $order;
    }

    public function setStatusFilter(string $filter): void
    {
        $this->statusFilter = $filter;
    }

    public function toggleFilters(): void
    {
        $this->filtersOpen = ! $this->filtersOpen;
    }

    public function toggleHistory(int $id): void
    {
        if (in_array($id, $this->historyOpen, true)) {
            $this->historyOpen = array_values(array_diff($this->historyOpen, [$id]));

            return;
        }

        $this->historyOpen[] = $id;
    }

    public function markTaken(int $id): void
    {
        $medicine = Medicine::findOrFail($id);

        if (! $medicine->is_active) {
            return;
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
    }

    public function deleteDoseLog(int $id): void
    {
        MedicineDoseLog::query()->whereKey($id)->delete();
    }

    public function delete(int $id): void
    {
        $medicine = Medicine::findOrFail($id);

        Dialog::alert(
            __('app.dialogs.delete_title'),
            __('app.dialogs.delete_body', ['name' => $medicine->name]),
            [__('app.actions.cancel'), __('app.actions.delete')]
        )->id("delete-medicine-{$id}")->event(ButtonPressed::class);
    }

    #[OnNative(ButtonPressed::class)]
    public function handleDeleteConfirmation(int $index, string $label, ?string $id = null): void
    {
        if ($index === 1 && $id && str_starts_with($id, 'delete-medicine-')) {
            $medicineId = (int) str_replace('delete-medicine-', '', $id);
            Medicine::findOrFail($medicineId)->delete();
            Dialog::toast(__('app.toasts.medicine_deleted'));
        }
    }

    public function edit(int $id): void
    {
        $this->redirect(route('add-medicine', ['id' => $id]), navigate: true);
    }

    public function render(): View
    {
        $now = now();

        $medicines = Medicine::query()
            ->with(['doseLogs' => fn ($query) => $query->latest('taken_at')->limit(5)])
            ->when($this->statusFilter === 'active', fn ($query) => $query->where('is_active', true))
            ->when($this->statusFilter === 'paused', fn ($query) => $query->where('is_active', false))
            ->when(
                $this->sortOrder === 'latest',
                fn ($query) => $query->orderByRaw('next_dose_at is null, next_dose_at desc')
            )
            ->when(
                $this->sortOrder === 'soonest',
                fn ($query) => $query->orderByRaw('next_dose_at is null, next_dose_at asc')
            )
            ->get();

        $dueNowCount = $medicines->filter(fn (Medicine $medicine) => $this->isDueNow($medicine, $now))->count();
        $upNextCount = $medicines->filter(fn (Medicine $medicine) => $this->isDueSoon($medicine, $now))->count();
        $takenTodayCount = MedicineDoseLog::query()
            ->whereDate('taken_at', $now->toDateString())
            ->whereHas('medicine', fn ($query) => $query->where('is_active', true))
            ->count();

        $asNeededMedicines = $medicines->filter(fn (Medicine $medicine) => $medicine->schedule_type === 'as_needed');
        $scheduledMedicines = $medicines->reject(fn (Medicine $medicine) => $medicine->schedule_type === 'as_needed');

        return view('livewire.medicine-list', [
            'scheduledMedicines' => $scheduledMedicines,
            'asNeededMedicines' => $asNeededMedicines,
            'dueNowCount' => $dueNowCount,
            'upNextCount' => $upNextCount,
            'takenTodayCount' => $takenTodayCount,
            'compactView' => $this->compactView,
            'title' => __('app.titles.medicines'),
        ]);
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
}
