<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]
            ->forgetCachedPermissions();

        // Delete old roles first
        Role::query()->delete();

        // Create new roles
        Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'approver']);
        Role::create(['name' => 'encoder']);
        Role::create(['name' => 'driver']);
        Role::create(['name' => 'guard']);
        Role::create(['name' => 'viewer']);
    }
}