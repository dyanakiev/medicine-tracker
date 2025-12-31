<?php

use App\Models\Medicine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Native\Mobile\Facades\Dialog;

uses(RefreshDatabase::class);

it('creates a medicine', function () {
    Carbon::setTestNow('2025-01-10 09:00:00');

    Dialog::shouldReceive('toast')->once()->andReturnNull();

    $response = $this->post('/medicines', [
        'name' => 'Amoxicillin',
        'dosage' => '1 capsule',
        'schedule_type' => 'hours',
        'frequency_hours' => 8,
        'notes' => 'Take with food',
    ]);

    $response->assertRedirect('/medicines');

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

    $response = $this->put("/medicines/{$medicine->id}", [
        'name' => 'Metformin XR',
        'dosage' => '2 tablets',
        'schedule_type' => 'hours',
        'frequency_hours' => 24,
        'notes' => 'Evening only',
        'is_active' => true,
    ]);

    $response->assertRedirect('/medicines');

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

    $this->post('/medicines', [
        'name' => '',
        'dosage' => '',
        'schedule_type' => '',
    ])->assertSessionHasErrors(['name', 'dosage', 'schedule_type']);
});

it('creates a days-based schedule', function () {
    Carbon::setTestNow('2025-01-10 09:00:00');

    Dialog::shouldReceive('toast')->once()->andReturnNull();

    $response = $this->post('/medicines', [
        'name' => 'Vitamin D',
        'dosage' => '1 tablet',
        'schedule_type' => 'days',
        'frequency_days' => 2,
        'time_of_day' => '08:30',
    ]);

    $response->assertRedirect('/medicines');

    $medicine = Medicine::query()->latest('id')->first();

    expect($medicine->schedule_type)->toBe('days')
        ->and($medicine->frequency_days)->toBe(2)
        ->and($medicine->time_of_day)->toBe('08:30')
        ->and($medicine->next_dose_at?->toDateTimeString())->toBe('2025-01-12 08:30:00');
});

it('requires valid times for time-based schedules', function () {
    Dialog::shouldReceive('toast')->never();

    $this->post('/medicines', [
        'name' => 'Melatonin',
        'dosage' => '1 tablet',
        'schedule_type' => 'times',
        'times_input' => 'bad-time',
    ])->assertSessionHasErrors(['times_input']);
});

it('creates an as-needed medicine without a next dose', function () {
    Dialog::shouldReceive('toast')->once()->andReturnNull();

    $response = $this->post('/medicines', [
        'name' => 'Ibuprofen',
        'dosage' => '1 tablet',
        'schedule_type' => 'as_needed',
    ]);

    $response->assertRedirect('/medicines');

    $medicine = Medicine::query()->latest('id')->first();

    expect($medicine->schedule_type)->toBe('as_needed')
        ->and($medicine->next_dose_at)->toBeNull();
});
