<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medicine>
 */
class MedicineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'dosage' => fake()->randomElement(['1 tablet', '2 tablets', '5 ml', '10 ml', '1 capsule']),
            'frequency_hours' => fake()->numberBetween(6, 24),
            'frequency_days' => 0,
            'schedule_type' => 'hours',
            'weekdays' => null,
            'times' => null,
            'dates' => null,
            'time_of_day' => null,
            'notes' => fake()->optional()->sentence(),
            'next_dose_at' => now()->addHours(fake()->numberBetween(1, 12)),
            'last_taken_at' => fake()->optional()->dateTimeBetween('-2 days', 'now'),
        ];
    }
}
