<?php

namespace Database\Seeders;

use App\Models\Technology;
use Illuminate\Database\Seeder;

class TechnologySeeder extends Seeder
{
    public function run(): void
    {
        $technologies = [
            ['name' => 'Laravel', 'slug' => 'laravel', 'category' => 'backend'],
            ['name' => 'PHP', 'slug' => 'php', 'category' => 'backend'],
            ['name' => 'PostgreSQL', 'slug' => 'postgresql', 'category' => 'database'],
            ['name' => 'Kotlin', 'slug' => 'kotlin', 'category' => 'mobile'],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'category' => 'frontend'],
            ['name' => 'Tailwind CSS', 'slug' => 'tailwind-css', 'category' => 'frontend'],
            ['name' => 'Git', 'slug' => 'git', 'category' => 'devops'],
        ];

        foreach ($technologies as $technology) {
            Technology::updateOrCreate(['slug' => $technology['slug']], $technology);
        }
    }
}
