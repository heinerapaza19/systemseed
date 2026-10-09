<?php

namespace Database\Factories;

use App\Models\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'institution' => fake()->company(),
            'description' => fake()->optional()->sentence(),
            'issued_at' => fake()->optional()->date(),
            'credential_id' => fake()->optional()->uuid(),
            'verification_url' => fake()->optional()->url(),
            'file_url' => null,
            'file_type' => null,
            'category' => fake()->optional()->word(),
            'visible' => true,
            'sort_order' => 0,
        ];
    }
}
