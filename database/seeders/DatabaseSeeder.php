<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'heinerapaza@gmail.com',
        ], [
            'name' => 'Heiner',
            'password' => Hash::make('apaza'),
            'role' => 'admin',
        ]);

        User::updateOrCreate([
            'email' => 'usuario@gmail.com',
        ], [
            'name' => 'Usuario',
            'password' => Hash::make('usuario'),
            'role' => 'user',
        ]);

        $this->call([
            ProfileSeeder::class,
            TechnologySeeder::class,
            ProjectSeeder::class,
        ]);
    }
}
