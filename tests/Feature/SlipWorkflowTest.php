<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\GasSlip;
use App\Models\GatePass;
use App\Models\PassSlip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SlipWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_encoder_approver_and_operations_staff_can_process_each_slip_to_completion(): void
    {
        foreach (['encoder', 'approver', 'guard', 'driver'] as $role) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }

        $division = Division::create(['name' => 'Operations', 'code' => 'OPS']);
        $encoder = User::factory()->create(['division_id' => $division->id]);
        $approver = User::factory()->create(['position' => 'General Manager']);
        $guard = User::factory()->create();
        $driver = User::factory()->create(['position' => 'Driver', 'division_id' => $division->id]);
        $requester = User::factory()->create(['division_id' => $division->id]);
        $encoder->assignRole('encoder');
        $approver->assignRole('approver');
        $guard->assignRole('guard');
        $driver->assignRole('driver');
        $vehicle = Vehicle::create([
            'plate_number' => 'E2E-001',
            'description' => 'Workflow test vehicle',
            'type' => 'Van',
            'assigned_division_id' => $division->id,
        ]);

        $this->actingAs($encoder)->post(route('pass-slips.store'), [
            'employee_user_id' => $requester->id,
            'pass_type' => 'official_business',
            'purpose' => 'Deliver documents',
            'destination' => 'City Hall',
            'time_out' => '2026-10-23 09:00:00',
            'expected_return' => '2026-10-23 12:00:00',
        ])->assertRedirect(route('pass-slips.index'));
        $pass = PassSlip::sole();
        $this->assertSame('pending', $pass->status);
        $this->assertSame($encoder->id, $pass->encoded_by_user_id);

        $this->actingAs($approver)->post(route('pass-slips.approve', $pass))->assertRedirect();
        $this->assertSame('approved', $pass->fresh()->status);
        $this->actingAs($guard)->post(route('pass-slips.time-out', $pass))->assertRedirect();
        $this->assertSame('out', $pass->fresh()->status);
        $this->post(route('pass-slips.time-in', $pass))->assertRedirect();
        $this->assertDatabaseHas('pass_slips', [
            'id' => $pass->id,
            'status' => 'returned',
            'verified_out_by' => $guard->id,
            'verified_in_by' => $guard->id,
        ]);

        $this->actingAs($encoder)->post(route('gate-passes.store'), [
            'requested_by_user_id' => $requester->id,
            'driver_name' => $driver->name,
            'vehicle_type' => 'Van',
            'plate_number' => $vehicle->plate_number,
            'destination' => 'City Hall',
            'passengers' => 'Staff',
            'purpose' => 'Document delivery',
            'time_of_departure' => '2026-10-23 09:00:00',
            'time_of_arrival' => '2026-10-23 11:00:00',
        ])->assertRedirect(route('gate-passes.index'));
        $gate = GatePass::sole();
        $this->assertSame('pending', $gate->status);
        $this->assertSame($encoder->id, $gate->encoded_by_user_id);

        $this->actingAs($approver)->post(route('gate-passes.approve', $gate))->assertRedirect();
        $this->actingAs($guard)->post(route('gate-passes.release', $gate))->assertRedirect();
        $this->post(route('gate-passes.returned', $gate))->assertRedirect();
        $this->assertDatabaseHas('gate_passes', [
            'id' => $gate->id,
            'status' => 'returned',
            'approved_by_user_id' => $approver->id,
            'released_by_user_id' => $guard->id,
            'returned_verified_by' => $guard->id,
        ]);

        $this->actingAs($encoder)->post(route('gas-slips.store'), [
            'vehicle_id' => $vehicle->id,
            'driver_user_id' => $driver->id,
            'fuel_type' => 'Diesel',
            'gas_station' => 'Test Station',
            'requested_liters' => '4',
            'passenger_name' => 'Staff',
            'places_to_visit' => 'City Hall',
            'purpose' => 'Document delivery',
            'balance_in_tank_begin' => '3',
            'issued_from_stock' => '4',
        ])->assertRedirect(route('gas-slips.index'));
        $gas = GasSlip::sole();
        $this->assertSame('pending', $gas->status);
        $this->assertSame($driver->id, $gas->requested_by_user_id);
        $this->assertSame($encoder->id, $gas->encoded_by_user_id);
        $this->assertEquals(4, $gas->requested_liters);

        $this->actingAs($approver)->post(route('gas-slips.approve', $gas))->assertRedirect();
        $this->actingAs($driver)->post(route('gas-slips.complete', $gas), [
            'time_departure_office' => '2026-10-23 09:00:00',
            'time_arrival_destination' => '2026-10-23 09:30:00',
            'time_departure_destination' => '2026-10-23 10:00:00',
            'time_arrival_office' => '2026-10-23 10:30:00',
            'distance_travelled' => '12',
            'fuel_used' => '5',
            'purchased_during_trip' => '0',
        ])->assertRedirect();
        $this->assertDatabaseHas('gas_slips', [
            'id' => $gas->id,
            'status' => 'released',
            'approved_by_user_id' => $approver->id,
            'released_by_user_id' => $driver->id,
            'balance_in_tank_end' => 2,
        ]);
    }
}
