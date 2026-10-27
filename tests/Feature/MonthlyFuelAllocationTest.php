<?php

namespace Tests\Feature;

use App\Models\GasSlip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MonthlyFuelAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_lists_current_month_released_fuel_against_vehicle_allocations_for_admin_only(): void
    {
        $role = Role::create(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        $vehicle = Vehicle::create([
            'plate_number' => 'FUEL-001',
            'description' => 'Test vehicle',
            'type' => 'Van',
            'monthly_fuel_allocation' => 20,
        ]);
        GasSlip::create([
            'slip_number' => 'GS-TEST-001',
            'vehicle_id' => $vehicle->id,
            'status' => 'released',
            'fuel_used' => 20,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Monthly Fuel Allocation')
            ->assertSee('FUEL-001')
            ->assertSee('At/over limit');

        $user = User::factory()->create();
        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('Monthly Fuel Allocation');
    }
}
