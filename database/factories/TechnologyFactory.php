<?php

namespace Database\Factories;

use App\Models\Technology;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Technology>
 */
class TechnologyFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(),
            'icon' => null,
            'category' => fake()->randomElement(['backend', 'frontend', 'database', 'mobile', 'devops', 'other']),
        ];
    }
}
