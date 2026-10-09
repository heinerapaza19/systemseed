<?php

namespace Database\Factories;

use App\Models\Experience;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Experience> */
class ExperienceFactory extends Factory
{
    public function definition(): array
    {
        return ['company' => fake()->company(), 'position' => fake()->jobTitle(), 'description' => fake()->paragraph(), 'start_date' => '2024-01-01', 'end_date' => null, 'is_current' => true, 'location' => fake()->city(), 'sort_order' => 0, 'visible' => true];
    }
}
