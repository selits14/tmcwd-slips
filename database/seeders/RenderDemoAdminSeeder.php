<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class RenderDemoAdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('DEMO_ADMIN_NAME');
        $email = env('DEMO_ADMIN_EMAIL');
        $password = env('DEMO_ADMIN_PASSWORD');

        if (! $name || ! filter_var($email, FILTER_VALIDATE_EMAIL) || ! $password || strlen($password) < 12) {
            throw new RuntimeException('Set a valid DEMO_ADMIN_NAME, DEMO_ADMIN_EMAIL, and a DEMO_ADMIN_PASSWORD of at least 12 characters.');
        }

        $this->call([RoleSeeder::class, DivisionSeeder::class]);

        $user = User::where('email', $email)->first();
        if (! $user) {
            $division = Division::where('code', 'ICT')->firstOrFail();
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'employee_id' => 'DEMO-' . strtoupper(bin2hex(random_bytes(4))),
                'position' => 'IT Officer',
                'division_id' => $division->id,
                'is_active' => true,
                'password' => Hash::make($password),
            ]);
        } elseif (! $user->hasRole('super_admin')) {
            throw new RuntimeException('The demo admin email already belongs to a non-admin account. Choose a new email.');
        }

        $user->syncRoles(['super_admin']);
    }
}
