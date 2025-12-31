<?php

use App\Models\Medicine;
use App\Models\MedicineDoseLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use Native\Mobile\Facades\Dialog;

uses(RefreshDatabase::class);

it('shows only active medicines by default', function () {
    Carbon::setTestNow('2025-01-10 09:00:00');

    $due = Medicine::factory()->create([
        'name' => 'Amoxicillin',
        'next_dose_at' => now()->subHour(),
        'is_active' => true,
    ]);
    $upcoming = Medicine::factory()->create([
        'name' => 'Vitamin D',
        'next_dose_at' => now()->addHours(3),
        'is_active' => true,
    ]);
    $paused = Medicine::factory()->create([
        'name' => 'Ibuprofen',
        'next_dose_at' => now()->subHour(),
        'is_active' => false,
    ]);

    $this->get('/medicines')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Medicines/Index')
            ->where('statusFilter', 'active')
            ->where('scheduledMedicines', function ($medicines) use ($due, $upcoming, $paused) {
                $names = collect($medicines)->pluck('name')->all();

                return in_array($due->name, $names, true)
                    && in_array($upcoming->name, $names, true)
                    && ! in_array($paused->name, $names, true);
            })
        );
});

it('can filter paused medicines', function () {
    $active = Medicine::factory()->create(['name' => 'Metformin', 'is_active' => true]);
    $paused = Medicine::factory()->create(['name' => 'Loratadine', 'is_active' => false]);

    $this->get('/medicines?status=paused')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Medicines/Index')
            ->where('statusFilter', 'paused')
            ->where('scheduledMedicines', function ($medicines) use ($active, $paused) {
                $names = collect($medicines)->pluck('name')->all();

                return in_array($paused->name, $names, true)
                    && ! in_array($active->name, $names, true);
            })
        );
});

it('sorts medicines by next dose time', function () {
    Carbon::setTestNow('2025-01-10 09:00:00');

    $soon = Medicine::factory()->create(['name' => 'Atorvastatin', 'next_dose_at' => now()->addHour()]);
    $later = Medicine::factory()->create(['name' => 'Amlodipine', 'next_dose_at' => now()->addHours(6)]);

    $this->get('/medicines?status=all&sort=soonest')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Medicines/Index')
            ->where('sortOrder', 'soonest')
            ->where('scheduledMedicines', function ($medicines) use ($soon, $later) {
                $names = collect($medicines)->pluck('name')->all();

                return $names === [$soon->name, $later->name];
            })
        );

    $this->get('/medicines?status=all&sort=latest')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Medicines/Index')
            ->where('sortOrder', 'latest')
            ->where('scheduledMedicines', function ($medicines) use ($soon, $later) {
                $names = collect($medicines)->pluck('name')->all();

                return $names === [$later->name, $soon->name];
            })
        );
});

it('marks a medicine as taken and updates schedule', function () {
    Carbon::setTestNow('2025-01-10 09:00:00');

    $medicine = Medicine::factory()->create([
        'frequency_hours' => 8,
        'next_dose_at' => now()->subHour(),
        'last_taken_at' => null,
        'is_active' => true,
    ]);

    Dialog::shouldReceive('toast')->once()->andReturnNull();

    $this->post("/medicines/{$medicine->id}/taken")
        ->assertRedirect('/medicines');

    $medicine->refresh();

    expect($medicine->last_taken_at?->toDateTimeString())->toBe('2025-01-10 09:00:00')
        ->and($medicine->next_dose_at?->toDateTimeString())->toBe('2025-01-10 17:00:00')
        ->and($medicine->doseLogs()->count())->toBe(1);
});

it('deletes a medicine when confirmed', function () {
    $medicine = Medicine::factory()->create(['name' => 'Cetirizine', 'is_active' => true]);

    Dialog::shouldReceive('toast')->once()->andReturnNull();

    $this->post("/medicines/{$medicine->id}/delete")
        ->assertRedirect('/medicines');

    expect(Medicine::query()->whereKey($medicine->id)->exists())->toBeFalse();
});

it('shows dose history for a medicine and can delete dose logs', function () {
    Carbon::setTestNow('2025-01-10 09:00:00');

    $medicine = Medicine::factory()->create(['name' => 'Losartan', 'is_active' => true]);
    $doseLog = $medicine->doseLogs()->create(['taken_at' => now()->subHours(2)]);

    $this->get("/medicines?status=all&open[]={$medicine->id}&history[{$medicine->id}]=5")
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Medicines/Index')
            ->where('scheduledMedicines', function ($medicines) use ($medicine, $doseLog) {
                $payload = collect($medicines)->firstWhere('id', $medicine->id);

                if (! is_array($payload) || ! isset($payload['doseLogs'])) {
                    return false;
                }

                $logIds = collect($payload['doseLogs'])->pluck('id')->all();

                return in_array($doseLog->id, $logIds, true);
            })
        );

    $this->post("/dose-logs/{$doseLog->id}/delete")
        ->assertRedirect('/medicines');

    expect(MedicineDoseLog::query()->whereKey($doseLog->id)->exists())->toBeFalse();
});

it('counts taken today from dose logs', function () {
    Carbon::setTestNow('2025-01-10 09:00:00');

    $active = Medicine::factory()->create(['is_active' => true]);
    $paused = Medicine::factory()->create(['is_active' => false]);

    $active->doseLogs()->create(['taken_at' => now()->subHour()]);
    $active->doseLogs()->create(['taken_at' => now()->subDays(1)]);
    $paused->doseLogs()->create(['taken_at' => now()->subHour()]);

    $this->get('/medicines')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Medicines/Index')
            ->where('takenTodayCount', 1)
        );
});

it('shows notes when provided', function () {
    $medicine = Medicine::factory()->create([
        'notes' => 'Take with food and a full glass of water.',
        'is_active' => true,
    ]);

    $this->get('/medicines?status=all')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Medicines/Index')
            ->where('scheduledMedicines', function ($medicines) use ($medicine) {
                $payload = collect($medicines)->firstWhere('id', $medicine->id);

                return is_array($payload) && $payload['notes'] === $medicine->notes;
            })
        );
});

it('shows as-needed medicines in the list', function () {
    $scheduled = Medicine::factory()->create([
        'name' => 'Aspirin',
        'schedule_type' => 'hours',
        'frequency_hours' => 6,
        'is_active' => true,
    ]);

    $asNeeded = Medicine::factory()->create([
        'name' => 'Ibuprofen',
        'schedule_type' => 'as_needed',
        'frequency_hours' => 0,
        'is_active' => true,
    ]);

    $this->get('/medicines?status=all')
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Medicines/Index')
            ->where('scheduledMedicines', function ($medicines) use ($scheduled) {
                $names = collect($medicines)->pluck('name')->all();

                return in_array($scheduled->name, $names, true);
            })
            ->where('asNeededMedicines', function ($medicines) use ($asNeeded) {
                $names = collect($medicines)->pluck('name')->all();

                return in_array($asNeeded->name, $names, true);
            })
        );
});
