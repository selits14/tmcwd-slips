<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,      // ← must be first
            DivisionSeeder::class,
            AdminUserSeeder::class, // ← runs after roles exist
        ]);
    }
}