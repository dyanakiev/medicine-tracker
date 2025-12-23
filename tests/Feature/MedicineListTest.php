<?php

use App\Livewire\MedicineList;
use App\Models\Medicine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
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

    Livewire::test(MedicineList::class)
        ->assertSee($due->name)
        ->assertSee($upcoming->name)
        ->assertDontSee($paused->name);
});

it('can filter paused medicines', function () {
    $active = Medicine::factory()->create(['name' => 'Metformin', 'is_active' => true]);
    $paused = Medicine::factory()->create(['name' => 'Loratadine', 'is_active' => false]);

    Livewire::test(MedicineList::class)
        ->call('setStatusFilter', 'paused')
        ->assertSee($paused->name)
        ->assertDontSee($active->name);
});

it('sorts medicines by next dose time', function () {
    Carbon::setTestNow('2025-01-10 09:00:00');

    $soon = Medicine::factory()->create(['name' => 'Atorvastatin', 'next_dose_at' => now()->addHour()]);
    $later = Medicine::factory()->create(['name' => 'Amlodipine', 'next_dose_at' => now()->addHours(6)]);

    Livewire::test(MedicineList::class)
        ->call('setStatusFilter', 'all')
        ->assertSeeInOrder([$soon->name, $later->name])
        ->call('sortBy', 'latest')
        ->assertSeeInOrder([$later->name, $soon->name]);
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

    Livewire::test(MedicineList::class)
        ->call('markTaken', $medicine->id);

    $medicine->refresh();

    expect($medicine->last_taken_at?->toDateTimeString())->toBe('2025-01-10 09:00:00')
        ->and($medicine->next_dose_at?->toDateTimeString())->toBe('2025-01-10 17:00:00')
        ->and($medicine->doseLogs()->count())->toBe(1);
});

it('deletes a medicine when confirmed', function () {
    $medicine = Medicine::factory()->create(['name' => 'Cetirizine', 'is_active' => true]);

    Dialog::shouldReceive('toast')->once()->andReturnNull();

    Livewire::test(MedicineList::class)
        ->call('handleDeleteConfirmation', 1, 'Delete', "delete-medicine-{$medicine->id}");

    expect(Medicine::query()->whereKey($medicine->id)->exists())->toBeFalse();
});

it('shows dose history for a medicine', function () {
    Carbon::setTestNow('2025-01-10 09:00:00');

    $medicine = Medicine::factory()->create(['name' => 'Losartan', 'is_active' => true]);
    $doseLog = $medicine->doseLogs()->create(['taken_at' => now()->subHours(2)]);

    Livewire::test(MedicineList::class)
        ->call('setStatusFilter', 'all')
        ->call('toggleHistory', $medicine->id)
        ->assertSee(__('app.medicines.history'))
        ->assertSee(__('app.medicines.history_entry', ['time' => 'Jan 10, 2025 07:00']))
        ->call('deleteDoseLog', $doseLog->id);

    expect($medicine->doseLogs()->count())->toBe(0);
});

it('counts taken today from dose logs', function () {
    Carbon::setTestNow('2025-01-10 09:00:00');

    $active = Medicine::factory()->create(['is_active' => true]);
    $paused = Medicine::factory()->create(['is_active' => false]);

    $active->doseLogs()->create(['taken_at' => now()->subHour()]);
    $active->doseLogs()->create(['taken_at' => now()->subDays(1)]);
    $paused->doseLogs()->create(['taken_at' => now()->subHour()]);

    Livewire::test(MedicineList::class)
        ->assertViewHas('takenTodayCount', 1);
});

it('shows a notes preview and full notes when expanded', function () {
    $medicine = Medicine::factory()->create([
        'notes' => 'Take with food and a full glass of water.',
        'is_active' => true,
    ]);

    Livewire::test(MedicineList::class)
        ->call('setStatusFilter', 'all')
        ->assertSee(__('app.medicines.notes_label'))
        ->assertSee($medicine->notes)
        ->call('toggleHistory', $medicine->id)
        ->assertSee($medicine->notes);
});

it('shows as-needed medicines in a separate section', function () {
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

    Livewire::test(MedicineList::class)
        ->call('setStatusFilter', 'all')
        ->assertSee($scheduled->name)
        ->assertSee($asNeeded->name)
        ->assertSee(__('app.medicines.as_needed_section'));
});
