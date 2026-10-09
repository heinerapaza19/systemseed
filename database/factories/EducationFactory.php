<?php

namespace Database\Factories;

use App\Models\Education;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Education> */
class EducationFactory extends Factory
{
    public function definition(): array
    {
        return ['institution' => fake()->company(), 'degree' => fake()->words(3, true), 'field_of_study' => fake()->optional()->word(), 'description' => fake()->optional()->paragraph(), 'start_date' => '2020-01-01', 'end_date' => '2023-12-31', 'is_current' => false, 'location' => fake()->city(), 'sort_order' => 0, 'visible' => true];
    }
}
