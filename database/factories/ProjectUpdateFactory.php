<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProjectUpdate;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProjectUpdate> */
class ProjectUpdateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory()->published(),
            'title' => fake()->sentence(5),
            'content' => fake()->paragraph(),
            'published_at' => now(),
            'visible' => true,
        ];
    }
}
