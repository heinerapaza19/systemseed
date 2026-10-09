<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(3),
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraphs(2, true),
            'cover_image' => fake()->optional()->url(),
            'github_url' => fake()->optional()->url(),
            'demo_url' => fake()->optional()->url(),
            'demo_enabled' => false,
            'demo_public' => false,
            'is_template' => false,
            'download_enabled' => false,
            'source_code_enabled' => false,
            'download_url' => null,
            'source_code_url' => null,
            'status' => 'draft',
            'featured' => false,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => 'published',
            'published_at' => now(),
        ]);
    }
}
