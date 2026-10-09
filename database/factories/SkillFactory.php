<?php

namespace Database\Factories;

use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Skill> */
class SkillFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->unique()->word(), 'category' => fake()->randomElement(['backend', 'frontend', 'database', 'mobile', 'devops', 'tools', 'other']), 'level' => 'intermediate', 'icon' => null, 'sort_order' => 0, 'visible' => true];
    }
}
