<?php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        Profile::query()->firstOrCreate([
            'id' => Profile::MAIN_ID,
        ], [
            'full_name' => 'Heiner',
        ]);
    }
}
