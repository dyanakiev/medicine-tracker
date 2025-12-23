<?php

namespace Database\Factories;

use App\Models\Medicine;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicineDoseLog>
 */
class MedicineDoseLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medicine_id' => Medicine::factory(),
            'taken_at' => now()->subHours(fake()->numberBetween(1, 24)),
        ];
    }
}
