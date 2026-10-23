<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\GasSlip;
use App\Models\PassSlip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SlipAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_cannot_complete_another_drivers_gas_slip(): void
    {
        $role = Role::create(['name' => 'driver', 'guard_name' => 'web']);
        $driver = User::factory()->create();
        $otherDriver = User::factory()->create();
        $driver->assignRole($role);
        $otherDriver->assignRole($role);

        $division = Division::create(['name' => 'Operations', 'code' => 'OPS']);
        $vehicle = Vehicle::create([
            'plate_number' => 'TEST-001',
            'description' => 'Test vehicle',
            'type' => 'Van',
        ]);
        $slip = GasSlip::create([
            'slip_number' => 'GS-2026-0001',
            'vehicle_id' => $vehicle->id,
            'driver_user_id' => $otherDriver->id,
            'requested_by_user_id' => $otherDriver->id,
            'encoded_by_user_id' => $driver->id,
            'division_id' => $division->id,
            'places_to_visit' => 'Depot',
            'purpose' => 'Test',
            'status' => 'approved',
        ]);

        $this->actingAs($driver)
            ->post(route('gas-slips.complete', $slip))
            ->assertForbidden();

        $this->assertSame('approved', $slip->fresh()->status);
    }

    public function test_driver_only_sees_their_own_gas_slips(): void
    {
        $role = Role::create(['name' => 'driver', 'guard_name' => 'web']);
        $driver = User::factory()->create();
        $otherDriver = User::factory()->create();
        $driver->assignRole($role);
        $otherDriver->assignRole($role);
        $division = Division::create(['name' => 'Operations', 'code' => 'OPS']);
        $vehicle = Vehicle::create(['plate_number' => 'TEST-002', 'description' => 'Van', 'type' => 'Van']);
        $ownSlip = $this->gasSlip($driver, $division, $vehicle, 'GS-2026-0002');
        $otherSlip = $this->gasSlip($otherDriver, $division, $vehicle, 'GS-2026-0003');

        $response = $this->actingAs($driver)->get(route('gas-slips.index'));

        $this->assertSame([$ownSlip->id], $response->viewData('gasSlips')->pluck('id')->all());
        $this->get(route('gas-slips.show', $otherSlip))->assertNotFound();
    }

    public function test_division_manager_only_sees_slips_in_their_division(): void
    {
        $manager = User::factory()->create(['position' => 'Division Manager']);
        Role::create(['name' => 'approver', 'guard_name' => 'web']);
        $manager->assignRole('approver');
        $ownDivision = Division::create(['name' => 'Operations', 'code' => 'OPS', 'head_user_id' => $manager->id]);
        $otherDivision = Division::create(['name' => 'Finance', 'code' => 'FIN']);
        $vehicle = Vehicle::create(['plate_number' => 'TEST-003', 'description' => 'Van', 'type' => 'Van']);
        $ownSlip = $this->gasSlip($manager, $ownDivision, $vehicle, 'GS-2026-0004');
        $otherSlip = $this->gasSlip($manager, $otherDivision, $vehicle, 'GS-2026-0005');

        $response = $this->actingAs($manager)->get(route('gas-slips.index'));

        $this->assertSame([$ownSlip->id], $response->viewData('gasSlips')->pluck('id')->all());
        $this->get(route('gas-slips.show', $otherSlip))->assertNotFound();
    }

    public function test_guard_can_view_gate_and_pass_slips_but_not_gas_slips(): void
    {
        $guard = User::factory()->create();
        Role::create(['name' => 'guard', 'guard_name' => 'web']);
        $guard->assignRole('guard');

        $this->actingAs($guard)->get(route('gate-passes.index'))->assertOk();
        $this->get(route('pass-slips.index'))->assertOk();
        $this->get(route('gas-slips.index'))->assertForbidden();
    }

    public function test_rejection_records_who_rejected_and_cannot_be_applied_twice(): void
    {
        $approver = User::factory()->create(['position' => 'General Manager']);
        Role::create(['name' => 'approver', 'guard_name' => 'web']);
        $approver->assignRole('approver');
        $division = Division::create(['name' => 'Operations', 'code' => 'OPS']);
        $employee = User::factory()->create();
        $slip = PassSlip::create([
            'slip_number' => 'PS-2026-0001',
            'employee_user_id' => $employee->id,
            'division_id' => $division->id,
            'pass_type' => 'official_business',
            'purpose' => 'Test',
            'destination' => 'Office',
            'status' => 'pending',
        ]);

        $this->actingAs($approver)
            ->post(route('pass-slips.reject', $slip), ['remarks' => 'Incomplete request'])
            ->assertRedirect();

        $this->assertDatabaseHas('pass_slips', [
            'id' => $slip->id,
            'status' => 'rejected',
            'remarks' => 'Incomplete request',
            'rejected_by_user_id' => $approver->id,
        ]);
        $this->assertNotNull($slip->fresh()->rejected_at);

        $this->post(route('pass-slips.reject', $slip), ['remarks' => 'Duplicate'])
            ->assertStatus(409);
    }

    private function gasSlip(User $driver, Division $division, Vehicle $vehicle, string $number): GasSlip
    {
        return GasSlip::create([
            'slip_number' => $number,
            'vehicle_id' => $vehicle->id,
            'driver_user_id' => $driver->id,
            'requested_by_user_id' => $driver->id,
            'division_id' => $division->id,
            'places_to_visit' => 'Depot',
            'purpose' => 'Test',
            'status' => 'pending',
        ]);
    }
}
