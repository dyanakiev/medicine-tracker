<?php

use App\Livewire\MedicineForm;
use App\Models\Medicine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Native\Mobile\Facades\Dialog;

uses(RefreshDatabase::class);

it('creates a medicine', function () {
    Carbon::setTestNow('2025-01-10 09:00:00');

    Dialog::shouldReceive('toast')->once()->andReturnNull();

    Livewire::test(MedicineForm::class)
        ->set('name', 'Amoxicillin')
        ->set('dosage', '1 capsule')
        ->set('frequencyHours', '8')
        ->set('notes', 'Take with food')
        ->call('save')
        ->assertRedirect(route('medicines'))
        ->assertDispatched('medicine-saved');

    $medicine = Medicine::query()->latest('id')->first();

    expect($medicine)->not->toBeNull()
        ->and($medicine->name)->toBe('Amoxicillin')
        ->and($medicine->dosage)->toBe('1 capsule')
        ->and($medicine->frequency_hours)->toBe(8)
        ->and($medicine->schedule_type)->toBe('hours')
        ->and($medicine->notes)->toBe('Take with food')
        ->and($medicine->next_dose_at?->toDateTimeString())->toBe('2025-01-10 17:00:00')
        ->and($medicine->is_active)->toBeTrue();
});

it('updates a medicine', function () {
    $medicine = Medicine::factory()->create([
        'name' => 'Metformin',
        'dosage' => '1 tablet',
        'frequency_hours' => 12,
        'schedule_type' => 'hours',
        'notes' => null,
        'is_active' => false,
    ]);

    Dialog::shouldReceive('toast')->once()->andReturnNull();

    Livewire::test(MedicineForm::class, ['id' => $medicine->id])
        ->set('name', 'Metformin XR')
        ->set('dosage', '2 tablets')
        ->set('frequencyHours', '24')
        ->set('notes', 'Evening only')
        ->set('isActive', true)
        ->call('save')
        ->assertRedirect(route('medicines'))
        ->assertDispatched('medicine-saved');

    $medicine->refresh();

    expect($medicine->name)->toBe('Metformin XR')
        ->and($medicine->dosage)->toBe('2 tablets')
        ->and($medicine->frequency_hours)->toBe(24)
        ->and($medicine->schedule_type)->toBe('hours')
        ->and($medicine->notes)->toBe('Evening only')
        ->and($medicine->is_active)->toBeTrue();
});

it('validates required fields', function () {
    Dialog::shouldReceive('toast')->never();

    Livewire::test(MedicineForm::class)
        ->set('name', '')
        ->set('dosage', '')
        ->set('frequencyHours', '')
        ->call('save')
        ->assertHasErrors([
            'name' => 'required',
            'dosage' => 'required',
            'frequencyHours' => 'required',
        ]);
});

it('creates a days-based schedule', function () {
    Carbon::setTestNow('2025-01-10 09:00:00');

    Dialog::shouldReceive('toast')->once()->andReturnNull();

    Livewire::test(MedicineForm::class)
        ->set('name', 'Vitamin D')
        ->set('dosage', '1 tablet')
        ->set('scheduleType', 'days')
        ->set('frequencyDays', '2')
        ->set('timeOfDay', '08:30')
        ->call('save')
        ->assertRedirect(route('medicines'))
        ->assertDispatched('medicine-saved');

    $medicine = Medicine::query()->latest('id')->first();

    expect($medicine->schedule_type)->toBe('days')
        ->and($medicine->frequency_days)->toBe(2)
        ->and($medicine->time_of_day)->toBe('08:30')
        ->and($medicine->next_dose_at?->toDateTimeString())->toBe('2025-01-12 08:30:00');
});

it('requires valid times for time-based schedules', function () {
    Dialog::shouldReceive('toast')->never();

    Livewire::test(MedicineForm::class)
        ->set('name', 'Melatonin')
        ->set('dosage', '1 tablet')
        ->set('scheduleType', 'times')
        ->set('timesInput', 'bad-time')
        ->call('save')
        ->assertHasErrors(['timesInput']);
});

it('creates an as-needed medicine without a next dose', function () {
    Dialog::shouldReceive('toast')->once()->andReturnNull();

    Livewire::test(MedicineForm::class)
        ->set('name', 'Ibuprofen')
        ->set('dosage', '1 tablet')
        ->set('scheduleType', 'as_needed')
        ->call('save')
        ->assertRedirect(route('medicines'))
        ->assertDispatched('medicine-saved');

    $medicine = Medicine::query()->latest('id')->first();

    expect($medicine->schedule_type)->toBe('as_needed')
        ->and($medicine->next_dose_at)->toBeNull();
});
