<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // IT Officer — Super Admin
        $it = User::firstOrCreate(
            ['email' => 'admin@tmcwd.com'],
            [
                'name'        => 'IT Administrator',
                'employee_id' => 'IT-0001',
                'position'    => 'IT Officer',
                'division_id' => 1,
                'is_active'   => false,
                'password'    => Hash::make(Str::random(64)),
            ]
        );
        $it->syncRoles(['super_admin']);

        // General Manager
        $gm = User::firstOrCreate(
            ['email' => 'gm@tmcwd.com'],
            [
                'name'        => 'ENGR. JOEL JOHN M. ULEP',
                'employee_id' => 'GM-0001',
                'position'    => 'General Manager',
                'division_id' => 1,
                'is_active'   => false,
                'password'    => Hash::make(Str::random(64)),
            ]
        );
        $gm->syncRoles(['approver']);
    }
}
